<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use app\models\NewOfferForm;
use app\models\EditOfferForm;
use app\models\NewCommentForm;
use app\models\Offer;
use app\models\Category;

class OffersController extends AccessController
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    public function getAccessRules(): array
    {
        return [
            [
                'actions' => ['index', 'category'],
                'allow' => true,
                'roles' => ['?', '@'],
            ],
            [
                'actions' => ['add', 'edit', 'delete'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ];
    }

    public function actionIndex($id)
    {
        $offer = $this->findOffer($id, ['images', 'categories', 'user']);
        $comments = $offer->getComments()
            ->with('user')
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        $newCommentForm = new NewCommentForm();
        if (Yii::$app->request->isPost && $newCommentForm->load(Yii::$app->request->post())) {
            if (Yii::$app->user->isGuest) {
                throw new ForbiddenHttpException('Войдите, чтобы оставить комментарий.');
            }
            if ($newCommentForm->createComment($id)) {
                Yii::$app->session->setFlash('success', 'Комментарий успешно добавлен.');
                return $this->redirect(['offers/index', 'id' => $id]);
            }
        }

        return $this->render('index', [
            'offer' => $offer,
            'comments' => $comments,
            'newCommentForm' => $newCommentForm,
        ]);
    }

    public function actionAdd()
    {
        $newOfferForm = new NewOfferForm();
        if (Yii::$app->request->getIsPost()) {
            $newOfferForm->load(Yii::$app->request->post());
            $newOfferId = $newOfferForm->createOffer();
            if ($newOfferId) {
                Yii::$app->session->setFlash('success', 'Объявление успешно добавлено.');
                return $this->redirect(['/my']);
            }
        }

        return $this->render('add', [
            'newOfferForm' => $newOfferForm,
        ]);
    }

    public function actionEdit($id)
    {
        $offer = $this->findOffer($id);
        $this->ensureOwner($offer);

        $editOfferForm = new EditOfferForm();
        $editOfferForm->setAttributes([
            'title' => $offer->title,
            'description' => $offer->description,
            'price' => (int) $offer->price,
            'type' => $offer->type,
            'category_id' => array_column($offer->categories, 'id'),
        ], false);
        if (Yii::$app->request->getIsPost()) {
            $editOfferForm->load(Yii::$app->request->post());
            if ($editOfferForm->updateOffer($offer->id)) {
                Yii::$app->session->setFlash('success', 'Объявление успешно обновлено.');
                return $this->redirect(['offers/index', 'id' => $id]);
            }
        }

        return $this->render('edit', [
            'offer' => $offer,
            'editOfferForm' => $editOfferForm,
        ]);
    }

    public function actionDelete($id)
    {
        $offer = $this->findOffer($id, ['images']);
        $this->ensureCanDelete($offer);
        $offer->deleteWithFiles();

        return $this->redirect(['/my']);
    }

    public function actionCategory($id)
    {
        $category = Category::findOne($id);
        if ($category === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $query = Offer::find()
            ->alias('offers')
            ->with(['images', 'categories'])
            ->joinWith(['categories categories'])
            ->where(['categories.id' => $id])
            ->orderBy(['offers.created_at' => SORT_DESC, 'offers.id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);

        return $this->render('category', [
            'offers' => $dataProvider->getModels(),
            'pagination' => $dataProvider->getPagination(),
            'category' => $category,
            'categories' => Category::findWithOfferCounts(),
        ]);
    }

    private function findOffer(int $id, array $with = []): Offer
    {
        $query = Offer::find()->where(['id' => $id]);
        if ($with) {
            $query->with($with);
        }
        $offer = $query->one();
        if ($offer === null) {
            throw new NotFoundHttpException('Объявление не найдено.');
        }

        return $offer;
    }

    private function ensureOwner(Offer $offer): void
    {
        if ($offer->user_id != Yii::$app->user->id) {
            throw new ForbiddenHttpException('Редактировать объявление может только его автор.');
        }
    }

    private function ensureCanDelete(Offer $offer): void
    {
        if ($offer->user_id != Yii::$app->user->id && !Yii::$app->user->can('deleteOffer')) {
            throw new ForbiddenHttpException('Нельзя удалить чужое объявление.');
        }
    }
}
