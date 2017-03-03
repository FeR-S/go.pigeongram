<?php

namespace common\components;

use Yii;

class ActiveRecord extends \yii\db\ActiveRecord
{
    public function hasOne($class, $link)
    {
        /*
        // если класс кэшируется, то берем из кэша
        // todo: ошибка на странице списка офферов
        if (is_subclass_of($class, '\common\components\CacheActiveRecord'))
        {
            // берем из кеша
            foreach ($link as $relation_key => $this_key) {
                return $class::findOne([$relation_key => $this->{$this_key}]);
            }
        }*/
        return parent::hasOne($class, $link);
    }

    public static function getCacheKey($class, $key, $value)
    {
        $class = explode('\\', $class);
        return 'model:' . end($class) . ':' . $key . ':' . $value;
    }
}
