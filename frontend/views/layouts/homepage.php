<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use app\components\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo  Yii::$app->language ?>">
<head>
    <meta charset="<?php echo  Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo  Html::csrfMetaTags() ?>
    <title><?php echo  Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>


<body class="skin-blue layout-boxed">
<div class="wrapper">

    <header class="main-header">

        <!-- Logo -->
        <a href="#" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>PG</b>Go</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Pigeon</b>Go</span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <span class="logo-lg in-nav"><b>Pigeon</b>Go</span>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
<!--                    <li class="dropdown user user-menu">-->
<!--                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">-->
<!--                            <img src="/images/users/user2-160x160.jpg" class="user-image" alt="User Image">-->
<!--                            <span class="hidden-xs">--><?php //echo Yii::$app->user->identity->username?><!--</span>-->
<!--                        </a>-->
<!--                        <ul class="dropdown-menu">-->
<!--                            <!-- User image -->
<!--                            <li class="user-header">-->
<!--                                                      --><?php //echo Yii::$app->user->identity->user_icon; ?>
<!--                                <p>-->
<!--                                    --><?php //echo Yii::$app->user->identity->username?>
<!--                                    <small>--><?php //echo Yii::$app->user->identity->email?><!--</small>-->
<!--                                </p>-->
<!--                            </li>-->
<!--                            <!-- Menu Body -->
<!--                            <li class="user-body">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-xs-4 text-center">-->
<!--                                        <a href="#">Followers</a>-->
<!--                                    </div>-->
<!--                                    <div class="col-xs-4 text-center">-->
<!--                                        <a href="#">Sales</a>-->
<!--                                    </div>-->
<!--                                    <div class="col-xs-4 text-center">-->
<!--                                        <a href="#">Friends</a>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <!-- /.row -->
<!--                            </li>-->
<!--                            <!-- Menu Footer-->
<!--                            <li class="user-footer">-->
<!--                                <div class="pull-left">-->
<!--                                    <a href="#" class="btn btn-default btn-flat">Profile</a>-->
<!--                                </div>-->
<!--                                <div class="pull-right">-->
<!--                                    <a href="/site/logout" class="btn btn-default btn-flat">Logout</a>-->
<!--                                </div>-->
<!--                            </li>-->
<!--                        </ul>-->
<!--                    </li>-->

                    <!-- Control Sidebar Toggle Button -->
<!--                    <li>-->
<!--                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>-->
<!--                    </li>-->
                    <li>
                        <a href="/site/logout"><?php echo Yii::$app->user->identity->username; ?> <i class="fa fa-fw fa-sign-out"></i></a>
                    </li>
                </ul>
            </div>

        </nav>
    </header>

    <?php echo  $content ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane" id="control-sidebar-home-tab">
                <h3 class="control-sidebar-heading">Recent Activity</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-birthday-cake bg-red"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
                                <p>Will be 23 on April 24th</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-user bg-yellow"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>
                                <p>New phone +1(800)555-1234</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>
                                <p>nora@example.com</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-file-code-o bg-green"></i>
                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>
                                <p>Execution time 5 seconds</p>
                            </div>
                        </a>
                    </li>
                </ul><!-- /.control-sidebar-menu -->

                <h3 class="control-sidebar-heading">Tasks Progress</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Custom Template Design
                                <span class="label label-danger pull-right">70%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Update Resume
                                <span class="label label-success pull-right">95%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Laravel Integration
                                <span class="label label-warning pull-right">50%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Back End Framework
                                <span class="label label-primary pull-right">68%</span>
                            </h4>
                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                            </div>
                        </a>
                    </li>
                </ul><!-- /.control-sidebar-menu -->

            </div><!-- /.tab-pane -->

            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">
                <form method="post">
                    <h3 class="control-sidebar-heading">General Settings</h3>
                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Report panel usage
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Some information about this general settings option
                        </p>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Allow mail redirect
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Other sets of options are available
                        </p>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Expose author name in posts
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                        <p>
                            Allow the user to show his name in blog posts
                        </p>
                    </div><!-- /.form-group -->

                    <h3 class="control-sidebar-heading">Chat Settings</h3>

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Show me as online
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Turn off notifications
                            <input type="checkbox" class="pull-right">
                        </label>
                    </div><!-- /.form-group -->

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Delete chat history
                            <a href="javascript::;" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                        </label>
                    </div><!-- /.form-group -->
                </form>
            </div><!-- /.tab-pane -->
        </div>
    </aside><!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

</div><!-- ./wrapper -->

<!--<audio id="outbox-message-notice">-->
<!--    <source src="../audio/outbox.mp3" volume="0.2">-->
<!--</audio>-->

<audio id="inbox-message-notice">
    <source src="../audio/inbox-message.mp3" volume="0.2">
</audio>

<ul class="dropdown-menu" id="context-menu">
    <li><a href=""><img src="../images/ajax-loader.gif" alt=""></a></li>
</ul>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
 