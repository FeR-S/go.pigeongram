<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\PjaxAsset;
use kartik\form\ActiveForm;

//PjaxAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\ArticleSearch */
/* @var $form yii\widgets\ActiveForm */

Pjax::begin(['enablePushState' => false, 'id' => 'user-search-pjax']);

$form = ActiveForm::begin([
//    'method' => 'post',
        'id' => 'user_search_form',
        'options' => [
            'data-pjax' => true,
            'class' => 'sidebar-form'
        ],
//        'fullSpan' => true,
        'action' => '/user/search',
    ]
); ?>

<?php

echo $form->field($model, 'search_line', [
    'template' => '{input}',
    'options' => [
        'placeHolder' => '{input}',
    ],
    'addon' => [
        'append' => [
            'content' => Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-primary']),
            'asButton' => true
        ]
    ]
])->textInput(['placeHolder' => 'Search users...']); ?>

<?php ActiveForm::end(); ?>

<div class="search-result" id="search-result">
    <?php echo isset($dataProvider) ? \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '/user/_user-item',
        'options' => [
            'tag' => 'ul',
            'class' => 'nav nav-sidebar contacts'
        ],
        'layout' => "{items}",
        'itemOptions' => [
            'tag' => false,
        ],
        'emptyText' => 'Ничего не найдено...',
        'emptyTextOptions' => [
            'tag' => 'li',
            'class' => 'list-empty',
            'style' => 'padding: 10px; color: #999999;'
        ],
    ]) : ''; ?>
</div>

<?php Pjax::end(); ?>


