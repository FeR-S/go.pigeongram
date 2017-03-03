<?php

namespace common\models;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "bid".
 *
 * @property integer $id
 * @property integer $user_id_from
 * @property integer $user_id_to
 * @property string $created_at
 */
class Bid extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bid';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id_from', 'user_id_to', 'created_at'], 'required'],
            [['user_id_from', 'user_id_to'], 'integer'],
            [['created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id_from' => 'User Id From',
            'user_id_to' => 'User Id To',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param $user_id
     * @return static
     */
    public static function thereWasTheBidFromU($user_id)
    {
        return static::findOne([
            'user_id_from' => Yii::$app->user->getId(),
            'user_id_to' => $user_id
        ]);
    }

    /**
     * @param $user_id
     * @return static
     */
    public static function thereWasTheBidFromUser($user_id)
    {
        return static::findOne([
            'user_id_from' => $user_id,
            'user_id_to' => Yii::$app->user->getId()
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTo()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFrom()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id_to']);
    }

    /**
     * @return ActiveDataProvider
     */
    public static function getInboxBids()
    {
        $query = Bid::find()->with(['userTo']);

        $query->andFilterWhere([
            'user_id_to' => Yii::$app->user->getId()
        ]);

        return $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @return ActiveDataProvider
     */
    public static function getOutboxBids()
    {
        $query = Bid::find()->joinWith(['userFrom']);

        $query->andFilterWhere([
            'user_id_from' => Yii::$app->user->getId()
        ]);

        return $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
