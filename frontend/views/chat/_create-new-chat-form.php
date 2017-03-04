<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Buddy;
use yii\widgets\Pjax;

?>

<?php

//Pjax::begin(['enablePushState' => false, 'id' => 'pjax-chat-form',]);

$form = ActiveForm::begin([
    'id' => \common\models\ActionsWithUser::ACTION_CREATE_THE_CHAT_ID,
//    'enableClientValidation' => true,
//    'options' => [
//        'data-pjax' => true
//    ]
]);

if (isset($user_id_current)) {
    $model->_chat_members = $user_id_current;
}

echo $form->field($model, 'title')->textInput();

echo $form->field($model, 'description')->textInput();

echo $form->field($model, '_chat_members')->checkboxList(ArrayHelper::map(Buddy::getBuddies(true), 'buddy_2', 'user.username'));

echo Html::submitButton('Create New Chat', ['class' => 'btn btn-default', 'name' => 'create-the-chat', 'id' => 'create-new-chat-button']);

ActiveForm::end();

//Pjax::end();

?>
