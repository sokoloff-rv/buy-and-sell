<?php

namespace app\controllers;

use app\models\Offer;
use app\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ChatController extends AccessController
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'token' => ['GET'],
                    'open' => ['POST'],
                    'dialogs' => ['GET'],
                ],
            ],
        ]);
    }

    public function actionToken(): Response
    {
        return $this->asJson([
            'token' => Yii::$app->firebase->createCustomToken((int) Yii::$app->user->id),
        ]);
    }

    public function actionOpen(): Response
    {
        $offer = $this->findOffer((int) Yii::$app->request->post('offerId'));
        $buyer = Yii::$app->user->identity;
        if ((int) $offer->user_id === (int) $buyer->id) {
            throw new ForbiddenHttpException('Автор объявления не может создать диалог с собой.');
        }

        $reference = Yii::$app->firebase->database
            ->getReference(sprintf('chats/%d/%d', $offer->id, $buyer->id));
        if (!$reference->getChild('meta')->getSnapshot()->exists()) {
            $reference->getChild('meta')->set([
                'sellerId' => (string) $offer->user_id,
                'buyerId' => (string) $buyer->id,
                'offerId' => (string) $offer->id,
                'updatedAt' => round(microtime(true) * 1000),
            ]);
        }

        return $this->asJson($this->dialogData($offer, $buyer));
    }

    public function actionDialogs(int $offerId): Response
    {
        $offer = $this->findOffer($offerId);
        if ((int) $offer->user_id !== (int) Yii::$app->user->id) {
            throw new ForbiddenHttpException('Диалоги доступны только автору объявления.');
        }

        $dialogs = [];
        $value = Yii::$app->firebase->database
            ->getReference('chats/' . $offer->id)
            ->getSnapshot()
            ->getValue();

        foreach (is_array($value) ? $value : [] as $buyerId => $dialog) {
            $meta = $dialog['meta'] ?? [];
            if ((string) ($meta['sellerId'] ?? '') !== (string) $offer->user_id
                || (string) ($meta['offerId'] ?? '') !== (string) $offer->id
                || (string) ($meta['buyerId'] ?? '') !== (string) $buyerId) {
                continue;
            }
            $buyer = User::findOne((int) $buyerId);
            if ($buyer !== null) {
                $dialogs[] = $this->dialogData($offer, $buyer);
            }
        }

        return $this->asJson(['dialogs' => $dialogs]);
    }

    private function dialogData(Offer $offer, User $buyer): array
    {
        return [
            'offerId' => (string) $offer->id,
            'sellerId' => (string) $offer->user_id,
            'sellerName' => $offer->user->name,
            'buyerId' => (string) $buyer->id,
            'buyerName' => $buyer->name,
        ];
    }

    private function findOffer(int $id): Offer
    {
        $offer = Offer::find()->with('user')->where(['id' => $id])->one();
        if ($offer === null) {
            throw new NotFoundHttpException('Объявление не найдено.');
        }

        return $offer;
    }
}
