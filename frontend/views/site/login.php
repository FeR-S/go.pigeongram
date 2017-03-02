<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
?>

<div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
//                'label' => 'col-sm-2 control-label',
                'offset' => '',
                'wrapper' => 'col-xs-12',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'username', [
        'inputOptions' => [
            'placeholder' => 'Login',
        ],
    ])->label(false); ?>

    <?= $form->field($model, 'password', [
        'inputOptions' => [
            'placeholder' => 'Password',
        ],
    ])->passwordInput()->label(false); ?>

    <div class="row">
        <div class="col-xs-6">
            <?= $form->field($model, 'rememberMe')->checkbox(); ?>
        </div>
        <div class="col-xs-6">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

<!--    <div class="social-auth-links text-center">-->
<!--        <p>- OR -</p>-->
<!--        <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using-->
<!--            Facebook</a>-->
<!--        <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using-->
<!--            Google+</a>-->
<!--    </div>-->
<!--     /.social-auth-links -->

<!--    <a href="#">I forgot my password</a><br>-->
    <a href="/site/signup" class="text-center">Register a new membership</a>

</div>
