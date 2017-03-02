<?php
/**
 * Created by PhpStorm.
 * User: dsfre
 * Date: 25.01.2016
 * Time: 11:04
 */

use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\SignupForm */

$this->title = 'Signup';
?>
<div class="login-box-body">
	<p class="login-box-msg">Sign in to start your session</p>

	<?php $form = ActiveForm::begin([
		'id' => 'signup-form',
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

	<?= $form->field($model, 'email', [
		'inputOptions' => [
			'placeholder' => 'Email',
		],
	])->label(false); ?>

	<?= $form->field($model, 'password', [
		'inputOptions' => [
			'placeholder' => 'Password',
		],
	])->passwordInput()->label(false); ?>

	<?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
		'template' => '<div class="row"><div class="col-lg-5">{image}</div><div class="col-lg-7">{input}</div></div>',
	])->label(false); ?>

	<div class="row">
		<div class="col-xs-6">
			<?= Html::submitButton('Signup', ['class' => 'btn btn-primary btn-block', 'name' => 'signup-button']) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

<!--	<div class="social-auth-links text-center">-->
<!--		<p>- OR -</p>-->
<!--		<a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using-->
<!--			Facebook</a>-->
<!--		<a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using-->
<!--			Google+</a>-->
<!--	</div>-->

	<a href="/site/login" class="text-center">I already have a membership</a>

</div>
