<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property string $message
 * @property integer $user_id
 * @property integer $type
 * @property integer $chat_id
 * @property string $created_at
 */
class Message extends \yii\db\ActiveRecord
{

    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['message'], 'filter', 'filter' => 'addslashes'],
            [['user_id', 'chat_id', 'created_at'], 'required'],
            [['message'], 'trim'],
            [['user_id', 'chat_id', 'type'], 'integer'],
            [['created_at', 'message'], 'safe'],
            ['type', 'default', 'value' => self::TYPE_MESSAGE],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],

            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png, gif'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'user_id' => 'User ID',
            'chat_id' => 'Chat ID',
            'created_at' => 'Created At',
        ];
    }

    const TYPE_MESSAGE = 1;
    const TYPE_NOTICE = 0;

    /**
     * @return mixed
     */
    public function getMessageType()
    {
        return ArrayHelper::getValue(self::getTypesArray(), $this->type);
    }

    /**
     * @return array
     */
    public static function getTypesArray()
    {
        return [
            self::TYPE_MESSAGE => 'Сообщение',
            self::TYPE_NOTICE => 'Уведомление',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getAttachments()
//    {
//        return $this->hasOne(Attachment::className(), ['id' => 'attachment_id']);
//    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @param $chat_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMessages($chat_id)
    {
        $query = Message::find()
            ->with(['user'])
            ->where(['chat_id' => $chat_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(50)
            ->asArray()
            ->all();

//        $activeDataProvider = new ActiveDataProvider([
//            'query' => $query,
//            'sort' => [
//                'defaultOrder' => [
//                    'created_at' => SORT_ASC
//                ]
//            ],
//            'pagination' => false
//        ]);

//
//        echo '<pre>';
//        var_dump($query);
//        echo '</pre>';

//        foreach ($activeDataProvider->getModels() as $value){
//            echo '<pre>';
//            echo $value['user']['username'].' - '.$value['message'];
//            echo '</pre>';
//        }
//
//        die;

        return $query;
    }

    /**
     * @param $chat_id
     * @param $last_message_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getNewMessages($chat_id, $last_message_id)
    {
        $query = Message::find()
            ->with(['user'])
            ->where(['chat_id' => $chat_id])
            ->andWhere(['<', 'id', $last_message_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(50)
            ->asArray()
            ->all();

        return $query;
    }

    /**
     * @param $checked_messages
     * @return mixed
     */
    public function removeCheckedMessages($checked_messages)
    {
        Message::deleteAll(['in', 'id', $checked_messages]);
        return $checked_messages;
    }

    /**
     * @param $chat_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getLastMessage($chat_id)
    {
        $query = Message::find()
            ->where(['chat_id' => $chat_id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1)
            ->one();

        return $query;
    }

    /**
     * @param $message_text
     * @param $chat_id
     */
    public static function saveNewNotice($message_text, $chat_id)
    {
        $message = new Message();
        $message->message = $message_text;
        $message->user_id = Yii::$app->user->identity->getId();
        $message->chat_id = $chat_id;
        $message->created_at = date("Y-m-d H:i:s");
        $message->type = self::TYPE_NOTICE;
        $message->save();
    }
}
