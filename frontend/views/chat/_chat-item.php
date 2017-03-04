<?php
/**
 * Created by PhpStorm.
 * User: dsfre
 * Date: 28.01.2016
 * Time: 21:51
 */

?>

<li chat-id="<?php echo $model->id; ?>" class="list-item chat-item">
    <a href="#">
        <div class="user-icon-container">
            <div class="user-icon-inner"><i class="fa fa-envelope"></i></div>
        </div>
        <div class="item-info">
            <div class="item-title"><?php echo $model->title; ?><span
                        class="new-message-label label label-primary pull-right">!</span></div>
            <div class="item-description"><?php echo $model->description; ?></div>
        </div>
    </a>
</li>

<!--<li class="chat" chat-id="--><? //= $chat_id; ?><!--" style="    border-radius: 3px;">-->
<!--	<div class="user-sidebar-menu-link">-->
<!--		<div class="media-left">-->
<!--			<img class="media-object" data-src="holder.js/64x64" alt="64x64"-->
<!--			     src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PCEtLQpTb3VyY2UgVVJMOiBob2xkZXIuanMvNjR4NjQKQ3JlYXRlZCB3aXRoIEhvbGRlci5qcyAyLjYuMC4KTGVhcm4gbW9yZSBhdCBodHRwOi8vaG9sZGVyanMuY29tCihjKSAyMDEyLTIwMTUgSXZhbiBNYWxvcGluc2t5IC0gaHR0cDovL2ltc2t5LmNvCi0tPjxkZWZzPjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+PCFbQ0RBVEFbI2hvbGRlcl8xNTI4OTk1OGNkMCB0ZXh0IHsgZmlsbDojQUFBQUFBO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1mYW1pbHk6QXJpYWwsIEhlbHZldGljYSwgT3BlbiBTYW5zLCBzYW5zLXNlcmlmLCBtb25vc3BhY2U7Zm9udC1zaXplOjEwcHQgfSBdXT48L3N0eWxlPjwvZGVmcz48ZyBpZD0iaG9sZGVyXzE1Mjg5OTU4Y2QwIj48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIxNC41IiB5PSIzNi41Ij42NHg2NDwvdGV4dD48L2c+PC9nPjwvc3ZnPg=="-->
<!--			     data-holder-rendered="true" style="width: 64px; height: 64px;">-->
<!--		</div>-->
<!--		<div class="part-body">-->
<!--			<h4 class="media-heading">--><? //= $chat_title; ?><!--</h4>-->
<!---->
<!--			<p style="font-size: 12px;">--><? //= $chat_description; ?><!--</p>-->
<!---->
<!--			<p style="font-size: 10px">--><? //= $chat_created_at; ?><!--</p>-->
<!---->
<!--			<div class="btn-group chat-actions">-->
<!--				<button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown"-->
<!--				        aria-haspopup="true" aria-expanded="true">Default <span class="caret"></span></button>-->
<!--				<ul class="dropdown-menu">-->
<!--					<li><a href="#" data-target="#edit-current-chat-modal" action-id="edit-current-chat-form" data-toggle="modal" action-url="/chat/edit-current-chat-form">Редактировать чат</a></li>-->
<!--					<li class="disabled"><a href="#">Аттачи чата</a></li>-->
<!--					<li role="separator" class="divider"></li>-->
<!--					<li><a href="#" action-id="leave_the_chat" action-url="/chat/leave-the-chat">Покинуть чат</a></li>-->
<!--				</ul>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
<!--</li>-->
