<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "chat".
 *
 * @property integer $id
 * @property integer $created_at
 * @property string $title
 * @property string $description
 */
class ActionsWithUser extends Model
{

    const ACTION_CREATE_THE_CHAT_ID = 'create-the-chat';

    // удалить из друзей
    /**
     * @return array
     */
    public static function actionRemoveFromBuddies()
    {
        return [
            'action_label' => 'Remove from buddies',
            'action_url' => '/buddy/remove-from-buddies',
            'action_id' => 'remove-from-buddies',
        ];
    }

    /**
     * @return array
     */
    public static function actionCreateTheChat()
    {
        return [
            'action_label' => 'Create the chat',
            'action_url' => '/chat/create-the-chat',
            'action_id' => self::ACTION_CREATE_THE_CHAT_ID,
        ];
    }


    // отменить заявку
    /**
     * @return array
     */
    public static function actionCancelTheBid()
    {
        return [
            'action_label' => 'Cancel the bid',
            'action_url' => '/buddy/cancel-the-bid',
            'action_id' => 'cancel-the-bid',
        ];
    }


    // принять заявку в друзья
    /**
     * @return array
     */
    public static function actionAcceptTheBid()
    {
        return [
            'action_label' => 'Accept the bid',
            'action_url' => '/buddy/accept-the-bid',
            'action_id' => 'accept-the-bid',
        ];
    }

    // создать заявку в друзья
    /**
     * @return array
     */
    public static function actionCreateTheBid()
    {
        return [
            'action_label' => 'Add to buddies',
            'action_url' => '/buddy/create-the-bid',
            'action_id' => 'create-the-bid',
        ];
    }

}
