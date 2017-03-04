<?php
/* @var $this yii\web\View */


?>

<?php foreach ($actions as $action): ?>
    <li>
        <?php

        $options = [
            'user-id' => $user_id,
            'action-id' => $action['action_id'],
            'action-url' => $action['action_url'],
        ];

        if ($action['action_id'] == \common\models\ActionsWithUser::ACTION_CREATE_THE_CHAT_ID) {
            $options['data-target'] = '#modal-' . $action['action_id'];
            $options['data-toggle'] = 'modal';
        }

        echo \yii\helpers\Html::a($action['action_label'], '#', $options); ?>

    </li>
<?php endforeach; ?>






