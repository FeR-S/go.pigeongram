<?php

namespace common\models;

use common\models\User;
use Yii;
use common\models\Chat;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "chat_member".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $chat_id
 * @property integer $user_id
 */
class ChatMember extends ActiveRecord
{

    public $chat_members = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_member';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(Chat::className(), ['id' => 'chat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'chat_id', 'user_id', 'chat_members'], 'required'],
            [['chat_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'chat_id' => 'Chat ID',
            'user_id' => 'User ID',
            'chat_members' => 'Chat Members'
        ];
    }

    /**
     * @return array
     */
    public static function getChats()
    {
        $query = ChatMember::find()
            ->with(['chat'])
            ->where(['user_id' => Yii::$app->user->getId()]);

        $activeDataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        $arrayDataProvider = ArrayHelper::map($query->all(), 'chat.id', 'chat.id');

        return [
            'activeDataProvider' => $activeDataProvider,
            'arrayDataProvider' => $arrayDataProvider,
        ];

    }

    /**
     * @param $chat_id
     * @return array
     */
    public function getChatMembers($chat_id)
    {
        $query = ChatMember::find()
            ->asArray()
            ->where(['chat_id' => $chat_id])
            ->andWhere(['!=', 'user_id', Yii::$app->user->getId()]);

        return $arrayDataProvider = ArrayHelper::getColumn($query->all(), 'user_id');
    }
}
