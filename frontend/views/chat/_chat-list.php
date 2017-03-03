<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

Pjax::begin(['enablePushState' => false, 'id' => 'pjax-get-chats',]);

echo ListView::widget([
    'dataProvider' => $chats,
    'itemView' => '/chat/_chat-item',
    'options' => [
        'tag' => 'ul',
        'class' => 'sidebar-menu'
    ],
    'layout' => "{items}",
    'itemOptions' => [
        'tag' => false,
    ],
    'emptyText' => 'No Chats yet',
    'emptyTextOptions' => [
        'tag' => 'div',
        'class' => 'no-results'
    ],
]);

Pjax::end();