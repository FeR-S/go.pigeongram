<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

Pjax::begin(['enablePushState' => false, 'id' => 'pjax-' . $id,]);

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '/user/_user-item',
    'viewParams' => [
        'view_id' => $view_id
    ],
    'options' => [
        'tag' => 'ul',
        'class' => 'sidebar-menu'
    ],
    'layout' => "<li class='header'>" . $title . "</li>{items}",
    'itemOptions' => [
        'tag' => false,
    ],
    'emptyText' => 'No ' . $title,
    'emptyTextOptions' => [
        'tag' => 'div',
        'class' => 'no-results'
    ],
]);

Pjax::end();
