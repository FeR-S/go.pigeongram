<?php

namespace frontend\controllers;

use common\models\ActionsWithUser;
use common\models\Bid;
use common\models\Buddy;
use Yii;

class BuddyController extends \yii\web\Controller
{
    public function actionActionsWithUser()
    {
        if (Yii::$app->request->isAjax) {
            $user_id = Yii::$app->request->post('user_id');

            // проверка на друзей
            if (Buddy::isItBuddie($user_id)) {

                $current_actions = [
                    ActionsWithUser::actionRemoveFromBuddies(),
                    ActionsWithUser::actionCreateTheChat()
                ];

            } else {
                if (Bid::thereWasTheBidFromU($user_id)) {
                    // уже есть заявка от тебя к этому юзеру
                    // Значит можно отменить заявку
                    $current_actions = [
                        ActionsWithUser::actionCancelTheBid()
                    ];
                } elseif (Bid::thereWasTheBidFromUser($user_id)) {
                    // от этого юзера есть заявка тебе
                    // Значит можно её принять
                    $current_actions = [
                        ActionsWithUser::actionAcceptTheBid()
                    ];
                } else {
                    // нет никаких заявок
                    // Значит можно добавиться в друзья
                    $current_actions = [
                        ActionsWithUser::actionCreateTheBid()
                    ];
                };
            }

            return $this->renderPartial('_actions-with-user', [
                'actions' => $current_actions,
                'user_id' => $user_id
            ]);

        }
    }

    /** Create the Bid */
    public function actionCreateTheBid()
    {
        if (Yii::$app->request->isAjax) {

            $bid = new Bid();
            $user_id_to = Yii::$app->request->post('user_id');

            $bid->user_id_from = Yii::$app->user->identity->id;
            $bid->user_id_to = $user_id_to;
            $bid->created_at = date("Y-m-d H:i:s");

            // что б наверняка
            if (!Buddy::isItBuddie($user_id_to)) {
                if ($bid->save()) {
                    // оставили заявку на дружбу
                    // отправляем уведомление - кого добавили в друзья!!!!!
                    return Yii::$app->user->id;
                }
            }

        };
    }

    /** Accept the Bid */
    public function actionAcceptTheBid()
    {
        if (Yii::$app->request->isAjax) {

            $buddy_1 = new Buddy();
            $user_id_from = Yii::$app->user->identity->id;
            $user_id_to = Yii::$app->request->post('user_id');
            $buddies_date = date("Y-m-d H:i:s");

            $buddy_1->buddy_1 = $user_id_from;
            $buddy_1->buddy_2 = $user_id_to;
            $buddy_1->created_at = $buddies_date;

            if ($buddy_1->save()) {
                // Записали первого друга
                // записываем второго
                Yii::$app->redis->sadd(
                    'user:' . $user_id_from . ':buddies',
                    $user_id_to
                );

                $buddie_2 = new Buddy;
                $buddie_2->buddy_1 = $user_id_to;
                $buddie_2->buddy_2 = $user_id_from;
                $buddie_2->created_at = $buddies_date;

                if ($buddie_2->save()) {
                    // Записали второго друга
                    // Можно оповестить заявителя что его заявка принята
                    Yii::$app->redis->sadd(
                        'user:' . $user_id_to . ':buddies',
                        $user_id_from
                    );

                    // Удаляем заявку
                    $remove_request = Bid::deleteAll([
                        'user_id_from' => $user_id_to,
                        'user_id_to' => $user_id_from
                    ]);

                    if ($remove_request) {
                        return Yii::$app->user->id;
                    }
                }

            }
        }

    }

    /** Remove the Buddie*/
    public function actionRemoveFromBuddies()
    {
        if (Yii::$app->request->isAjax) {
            $buddies = new Buddy();
            $buddie_2 = Yii::$app->request->post('user_id');
            $redis = \Yii::$app->redis;

            $remove_request_first = $buddies->deleteAll([
                'buddy_1' => Yii::$app->user->id,
                'buddy_2' => $buddie_2
            ]);

            if ($remove_request_first) {
                // Удалили первую строку друзей
                // Удаляем вторую

                $redis->srem(
                    'user:' . Yii::$app->user->getId() . ':buddies',
                    $buddie_2
                );

                $buddies = new Buddy();
                $remove_request_second = $buddies->deleteAll([
                    'buddy_2' => Yii::$app->user->id,
                    'buddy_1' => $buddie_2
                ]);

                if ($remove_request_second) {
                    $redis->srem(
                        'user:' . $buddie_2 . ':buddies',
                        Yii::$app->user->getId()
                    );
                    // Удалили вторую строку
                    // Удалили друзей
                    return Yii::$app->user->id;
                }
            }
        };
    }

    /** Cancel the Bid */
    public function actionCancelTheBid()
    {
        if (Yii::$app->request->isAjax) {

            $user_id_to = Yii::$app->request->post('user_id');

            $remove_request = Bid::deleteAll([
                'user_id_from' => Yii::$app->user->id,
                'user_id_to' => $user_id_to
            ]);

            if ($remove_request) {
                // удалили заявку на дружбу
                return Yii::$app->user->id;
            }
        };
    }

    /** Get inbox Bids */
    public function actionGetInboxBids()
    {
        $bid = new Bid();
        return $this->renderAjax('-get-inbox-bids', [
            'inbox_bids' => $bid::getInboxBids(),
        ]);
    }

    /** Get outbox Bids */
    public function actionGetOutboxBids()
    {
        $bid = new Bid();
        return $this->renderAjax('-get-outbox-bids', [
            'outbox_bids' => $bid::getOutboxBids(),
        ]);
    }

}
