<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\redis;
use app\assets\SocketAsset;

SocketAsset::register($this);
$this->title = 'My Yii Application';

?>

<aside class="main-sidebar">
    <section class="sidebar">

        <!--  SEARCH FORM  -->
        <?php echo $this->render('/user/_user-search-form', [
            'model' => new \common\models\search\UserSearch
        ]); ?>
        <!--  END SEARCH FORM  -->

        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs list-tabs">
                <li class="">
                    <a href="#tab_1" data-toggle="tab" class="btn btn-app" aria-expanded="false">
                        <?php $buddies_count = Yii::$app->redis->SCARD('user:' . Yii::$app->user->id . ':buddies'); ?>
                        <span class="badge bg-purple"><?php echo $buddies_count; ?></span>
                        <i class="fa fa-users"></i> Buddies
                    </a>
                </li>
                <li class="active">
                    <a href="#tab_2" data-toggle="tab" class="btn btn-app" aria-expanded="true">
                        <?php $chat_count = Yii::$app->redis->SCARD('user:' . Yii::$app->user->id . ':chats'); ?>
                        <span class="badge bg-aqua"><?php echo $chat_count; ?></span>
                        <i class="fa fa-envelope"></i> Chats
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane" id="tab_1">
                    <!--  FRIENDS  -->
                    <?php echo $this->render('/buddy/_buddies-list', [
                        'buddies' => \common\models\Buddy::getBuddies(),
                    ]); ?>
                    <!--  END FRIENDS  -->

                    <?php echo $this->render('/bid/_bids-list', [
                        'dataProvider' => \common\models\Bid::getInboxBids(),
                        'id' => 'inbox-bids',
                        'title' => 'Inbox bids',
                        'view_id' => 'inbox-bid-related',
                    ]) ?>

                    <?php echo $this->render('/bid/_bids-list', [
                        'dataProvider' => \common\models\Bid::getOutboxBids(),
                        'id' => 'outbox-bids',
                        'title' => 'Outbox bids',
                        'view_id' => 'outbox-bid-related',
                    ]) ?>
                </div><!-- /.tab-pane -->
                <div class="tab-pane active" id="tab_2">
                    <!--  CHATS  -->
                    <?php echo $this->render('/chat/_chat-list', [
                        'chats' => \common\models\Chat::getChats(),
                    ]) ?>
                    <!--  END CHATS  -->
                </div>
            </div>
        </div>

    </section>

</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="min-height: 100%!important">

    <!-- Content Header (Page header) -->
    <!--<section class="content-header">-->
    <!--    <h1>-->
    <!--        Dashboard-->
    <!--        <small>Version 2.0</small>-->
    <!--    </h1>-->
    <!--    <ol class="breadcrumb">-->
    <!--        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>-->
    <!--        <li class="active">Dashboard</li>-->
    <!--    </ol>-->
    <!--</section>-->

    <!-- Main content -->
    <section class="content">


        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-md-12">

                <!-- /.box -->
                <div class="row">
                    <div class="col-md-12">
                        <!-- DIRECT CHAT -->
                        <div id="chat-container">

                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->


            </div><!-- /.col -->

            <!-- /.col -->
        </div><!-- /.row -->
    </section>

    <!--<div class="site-index">-->

    <!--    <div class="row">-->
    <!--        <div class="col-xs-3">-->
    <!--            <h4 style="margin-bottom: 0">Поиск пользователей</h4>-->
    <!--            <p>Поиск пользователей по Username`у</p>-->
    <!--            <div id="content-1">-->
    <!--                --><? //= $this->render('/user/-user-search', [
    //                    'userList' => $userList
    //                ]) ?>
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="col-xs-3">-->
    <!--            <h4 style="margin-bottom: 0">Избранные пользователи</h4>-->
    <!--            <p>Список избранных пользователей</p>-->
    <!--            <div id="content-2">-->
    <!--                --><? //= $this->render('/buddy/-get-buddies', [
    //                    'buddies' => $buddies,
    //                ]) ?>
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="col-xs-3">-->
    <!--            <h4 style="margin-bottom: 0">Чаты</h4>-->
    <!--            <p>Список чатов, с участием текущего пользователя</p>-->
    <!--            <div id="content-5">-->
    <!--                --><? //= $this->render('/chat/-get-chats', [
    //                    'chats' => $chat_member,
    //                ]) ?>
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="col-xs-3">-->
    <!--            <h4 style="margin-bottom: 0">Выбранный чат</h4>-->
    <!--            <p>Активный чат с сообщениями</p>-->
    <!--            <div id="chat-container">-->
    <!--                select chat to start chat)-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    <div class="row" style="margin-top: 50px;">-->
    <!--        <div class="col-xs-3">-->
    <!--            <h4 style="margin-bottom: 0">Заявки в друзья</h4>-->
    <!--            <p>Кто меня позвал в друзья</p>-->
    <!--            <div id="content-5">-->
    <!--                --><? //= $this->render('/buddy/-get-inbox-bids', [
    //                    'inbox_bids' => $inbox_bids,
    //                ]) ?>
    <!--            </div>-->
    <!--        </div>-->
    <!---->
    <!--        <div class="col-xs-3">-->
    <!--            <h4 style="margin-bottom: 0">Мои заявки</h4>-->
    <!--            <p>Кого я позвал в друзья</p>-->
    <!--            <div id="content-5">-->
    <!--                --><? //= $this->render('/buddy/-get-outbox-bids', [
    //                    'outbox_bids' => $outbox_bids,
    //                ]) ?>
    <!--            </div>-->
    <!--        </div>-->
    <!---->
    <!--    </div>-->

    <!--</div>-->

</div><!-- /.content-wrapper -->

<?php Modal::begin([
    'id' => 'create-new-chat-modal',
    'header' => 'Create New Chat',
//    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal'])
//        . PHP_EOL . Html::button('Add', ['class' => 'btn btn-primary', 'id' => 'this-modal-form-submit']),
]); ?>

<div style="text-align: center; padding: 30px 0 50px"><img src="../images/ajax-loader.gif" alt=""></div>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'edit-current-chat-form-modal',
    'header' => 'Edit Current Chat',
//    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal'])
//        . PHP_EOL . Html::button('Add', ['class' => 'btn btn-primary', 'id' => 'this-modal-form-submit']),
]); ?>

<div style="text-align: center; padding: 30px 0 50px"><img src="../images/ajax-loader.gif" alt=""></div>

<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'edit-current-chat-modal',
    'header' => 'Edit Current Chat',
//    'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal'])
//        . PHP_EOL . Html::button('Add', ['class' => 'btn btn-primary', 'id' => 'this-modal-form-submit']),
]); ?>


<?php Modal::end(); ?>


