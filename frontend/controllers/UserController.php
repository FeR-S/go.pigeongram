<?php

namespace frontend\controllers;

use app\models\ChatMember;
use common\models\search\UserSearch;
use Yii;
use yii\web\Controller;

use app\models\User;
use yii\data\ActiveDataProvider;

class UserController extends Controller
{
    public function actionGetUserData()
    {
        if (Yii::$app->request->isAjax) {
            $user_data = [
                'user_id' => Yii::$app->user->id,
            ];
            return json_encode($user_data);
        }
    }

    public function actionSearch()
    {
        $searchModel = new UserSearch();
        if ($searchModel->load(Yii::$app->request->post())) {
            return $this->renderAjax('_user-search-form', [
                'model' => $searchModel,
                'dataProvider' => $searchModel->search()
            ]);
        }
    }

}
