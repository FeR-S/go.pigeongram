<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SocketAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/AdminLTE.css',
        'css/_all-skins.css',
        'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css',
    ];

    public $js = [
//        'http://5.101.124.194:3000/socket.io/socket.io.js',
//        'moment-timezone-with-data.js',
//        'js/plugins/jquery.slimscroll.min.js',
//        'js/plugins/moment.min.js',
//        'js/plugins/moment-timezone-with-data-2010-2020.min.js',
        'js/app.min.js',
        'js/main.js',
//        'js/demo.js',
//        'js/client.js',
 //        'js/dashboard.js',
    ];
 
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
