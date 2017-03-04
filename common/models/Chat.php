<?php

namespace common\models;

use Yii;
use yii\redis;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "chat".
 *
 * @property integer $id
 * @property integer $created_at
 * @property string $title
 * @property string $description
 */
class Chat extends \common\components\CacheActiveRecord
{
    const PUBLIC_CHAT_ID = 0;

    public $_chat_members;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'title', 'description', '_chat_members'], 'required'],
            [['title', 'description'], 'string', 'max' => 255],
            [['title', 'description'], 'trim']
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
            'title' => 'Title',
            'description' => 'Description',
            '_chat_members' => 'Chat Members',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatMembers()
    {
        return $this->hasMany(ChatMember::className(), ['chat_id' => 'id']);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return ActiveDataProvider
     */
    public static function getChats()
    {
        $query = Chat::find()
            ->joinWith(['chatMembers'])
            ->where(['chat_member.user_id' => Yii::$app->user->getId()]);

        $activeDataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        return $activeDataProvider;
    }

}
