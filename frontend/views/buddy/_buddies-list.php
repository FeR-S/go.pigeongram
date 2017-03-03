<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

Pjax::begin(['enablePushState' => false, 'id' => 'pjax-get-buddies',]);

echo ListView::widget([
    'dataProvider' => $buddies,
    'itemView' => '/user/_user-item',
    'viewParams' => [
        'view_id' => 'direct-related'
    ],
    'options' => [
        'tag' => 'ul',
        'class' => 'sidebar-menu'
    ],
    'layout' => "{items}",
    'itemOptions' => [
        'tag' => false,
    ],
    'emptyText' => 'No Buddies yet',
    'emptyTextOptions' => [
        'tag' => 'div',
        'class' => 'no-results'
    ],
]);

Pjax::end();