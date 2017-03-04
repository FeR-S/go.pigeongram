<?php

namespace common\models;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "buddies".
 *
 * @property integer $id
 * @property integer $buddy_1
 * @property integer $buddy_2
 * @property string $created_at
 */
class Buddy extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'buddy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['buddy_1', 'buddy_2', 'created_at'], 'required'],
            [['buddy_1', 'buddy_2'], 'integer'],
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
            'buddy_1' => 'Buddie 1',
            'buddy_2' => 'Buddie 2',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'buddy_2']);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function isItBuddie($user_id)
    {
        $cache_check = Yii::$app->redis->sismember(
            'user:' . Yii::$app->user->identity->getId() . ':buddies',
            $user_id
        );

        if ($cache_check) {
            return true;
        } else {
            $database_check = Buddy::findOne([
                'buddy_1' => Yii::$app->user->identity->getId(),
                'buddy_2' => $user_id
            ]);

            if ($database_check) {
                Yii::$app->redis->sadd(
                    'user:' . Yii::$app->user->identity->getId() . ':buddies',
                    $user_id
                );
                return true;

            } else {
                return false;
            }
        }
    }


    /**
     * @param bool $array
     * @return array|ActiveDataProvider|ActiveRecord[]
     */
    public static function getBuddies($array = false)
    {
        $query = Buddy::find()
            ->with(['user'])
            ->where(['buddy_1' => Yii::$app->user->getId()])
            ->orderBy(['id' => SORT_DESC]);

        if ($array) {
            return $query->all();
        }

        $activeDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $activeDataProvider;
    }
}
