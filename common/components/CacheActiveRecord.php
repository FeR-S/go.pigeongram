<?php
/**
 * Created by PhpStorm.
 * User: Сельвестр Сталоневич
 * Date: 02.03.2017
 * Time: 10:21
 */


namespace common\components;

use Yii;
use yii\base\Exception;

class CacheActiveRecord extends \common\components\ActiveRecord
{
    /**
     * @var int
     */
    public static $cache_time = 0;

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->saveInCache('id', $this->id);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        $cache_key = static::getCacheKey(get_called_class(), 'id', $this->id);
        Yii::$app->redis->del($cache_key);
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $key
     * @param $value
     * @throws Exception
     */
    public function saveInCache($key, $value)
    {
        $cache_key = static::getCacheKey(get_called_class(), $key, $value);
        if (!$this->id) throw new Exception('Добавьте в правила модели поле id. ' . get_called_class());
        Yii::$app->redis->hmset($cache_key, $this->getAttributes());
        if (self::$cache_time > 0) Yii::$app->redis->expire($cache_key, self::$cache_time);
    }

    // todo: сделать поиск не только по id
    // добавить метод со списком свойст или группы свойст для сохранения в кэше
    /**
     * @param mixed $conditions
     * @return bool|static
     * @throws Exception
     */
    public static function findOne($conditions)
    {
        // если поиск по праймари
        if (!is_array($conditions)) {
            $key = 'id';
            $cache_key = static::getCacheKey(get_called_class(), $key, $conditions);
        } else {
            if (count($conditions) > 2) throw new Exception('На данный момент не поддерживается больше 1 условия');
            $key = key($conditions);
            $value = current($conditions);
            $cache_key = static::getCacheKey(get_called_class(), $key, $value);
        }
        //echo $cache_key;
        $data = Yii::$app->redis->hgetall($cache_key);
        if ($data) {
            // если ключ кому-то нужно, то продлеваем срок жизни
            if (self::$cache_time > 0) Yii::$app->redis->expire($cache_key, self::$cache_time);
//            echo 'из кэша';
            $className = self::className();
            $model = new $className;
            $model->setAttributes($data);
            $model->setOldAttributes($data);
            return $model;
        }

        $model = parent::findOne($conditions);

        if (!is_null($model)) {
//            echo 'из базы';
            Yii::$app->redis->hmset(static::getCacheKey(get_called_class(), $key, $model->{$key}), $model->getAttributes());
            return $model;
        }
        return false;
    }
}
