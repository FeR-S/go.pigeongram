<?php

if (isset($view_id)) {
    switch ($view_id) {
        case 'direct-related':
            $new_model = $model->user;
            break;

        case 'inbox-bid-related':
            $new_model = $model->userTo;
            break;

        case 'outbox-bid-related':
            $new_model = $model->userFrom;
            break;
    }
} else {
    $new_model = $model;
}

?>

<li user-id="<?php echo $new_model->id; ?>" action-with-user>
    <a href="#">
        <?php echo $new_model->getUserIcon(); ?>
        <div class="item-info">
            <div class="item-title"><?php echo $new_model->username; ?></div>
            <div class="item-description"><?php echo $new_model->email; ?></div>
        </div>
    </a>
</li>
