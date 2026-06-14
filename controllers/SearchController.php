<?php

namespace app\controllers;
use app\models\Offer;

use Yii;
use yii\web\Controller;

class SearchController extends Controller
{
    public function actionIndex()
    {
        $searchModel = Yii::$app->search->getSearchModel();
        $query = Offer::find()
            ->where(['like', 'title', (string) $searchModel->query])
            ->orderBy(['created_at' => SORT_DESC]);

        $pagination = new \yii\data\Pagination([
            'defaultPageSize' => 8,
            'totalCount' => $query->count(),
        ]);

        $offers = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'offers' => $offers,
            'pagination' => $pagination,
            'newOffers' => Offer::find()
                ->with(['images', 'categories'])
                ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
                ->limit(8)
                ->all(),
        ]);
    }
}
