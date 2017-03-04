/**
 * Created by Сельвестр Сталоневич on 03.03.2017.
 */

var ContextMenu = $('#context-menu');
var section_Content = $('section.content');
var CurrentChatId = '';
var ChatContainer = $('#chat-container');

function AjaxTime(action_url, data) {
    var promise = $.ajax({
        type: 'post',
        // dataType: 'json',
        url: action_url,
        data: data,
        error: function (request, status, error) {
            console.log(status + ' - ' + error + request.responseText);
        }
    });
    return promise;
}

function ajaxLoader(tag) {
    tag.html('<div class="loader"><img src="../images/ajax-loader.gif"/></div>');
}

// Закрываем элемент по клику вне его области
$(document).mouseup(function (e) { // событие клика по веб-документу
    var current_block = $("#search-result"); // тут указываем ID элемента
    if (!current_block.is(e.target) // если клик был не по нашему блоку
        && current_block.has(e.target).length === 0) { // и не по его дочерним элементам
        current_block.hide(); // скрываем его
    }
});

// Закрываем элемент по клику вне его области
$(document).mouseup(function (e) { // событие клика по веб-документу
    var current_block = ContextMenu; // тут указываем ID элемента
    if (!current_block.is(e.target) // если клик был не по нашему блоку
        && current_block.has(e.target).length === 0) { // и не по его дочерним элементам
        current_block.hide(); // скрываем его
    }
});


// обрабатываем начало перезагрузки пиджака
$(document).on('ready pjax:start', '#user-search-pjax', function () {
    $(this).find('button[type=submit]').html('<img src="../images/loader.svg" style="margin-right: -3px;"/>');
});

// обрабатываем начало перезагрузки пиджака
// $(document).on('ready pjax:end', '#user-search-pjax', function(){
//     $(this).find('button[type=submit]').html('<i class="fa fa-search"></i>');
// });

// смотрим возможные действия с этим юзером
$(document).on('click', '[action-with-user]', function () {

    ContextMenu
        .empty()
        .css({
            left: event.pageX + 'px',
            top: event.pageY + 'px'
        })
        .append('<li style="text-align: center"><a href=""><img src="../images/ajax-loader.gif" alt=""></a></li>')
        .show('fast');

    var that = $(this),
        user_id = that.attr('user-id'),
        action_url = '/buddy/actions-with-user',
        data = {
            'user_id': user_id
        };

    AjaxTime(action_url, data).done(function (result) {
        ContextMenu
            .empty()
            .append(result);
    }).fail(function (request, status, error) {
        console.log(status + ' - ' + error + request.responseText);
    });

});

$(document).on('click', '[action-id]', function () {
    var that = $(this),
        action_id = that.attr('action-id'),
        action_url = that.attr('action-url'),
        user_id = that.attr('user-id');

    // create-the-bid
    // accept-the-bid
    // remove-from-buddies
    // cancel-the-bid
    // get-create-new-chat-form
    // get-chat-members

    switch (action_id) {
        case 'create-the-bid':

            var data = {
                'user_id': user_id
            };

            AjaxTime(action_url, data).done(function (user_id_from) {

                // socket.emit(action_id, {
                //     user_id_to: user_id,
                //     user_id_from: user_id_from
                // });

                $.pjax.reload({container: '#pjax-inbox-bids', async: false});
                $.pjax.reload({container: '#pjax-outbox-bids', async: false});

            }).fail(function (request, status, error) {
                console.log(status + ' - ' + error + request.responseText);
            });
            break;

        case 'accept-the-bid':

            var data = {
                'user_id': user_id
            };

            AjaxTime(action_url, data).done(function (user_id_from) {

                // socket.emit(action_id, {
                //     user_id_to: user_id,
                //     user_id_from: user_id_from
                // });

                $.pjax.reload({container: '#pjax-inbox-bids', async: false});
                $.pjax.reload({container: '#pjax-outbox-bids', async: false});
                $.pjax.reload({container: '#pjax-get-buddies', async: false});

            }).fail(function (request, status, error) {
                console.log(status + ' - ' + error + request.responseText);
            });

            break;

        case 'remove-from-buddies':

            var data = {
                'user_id': user_id
            };

            if (confirm("Удалить из друзей?")) {

                AjaxTime(action_url, data).done(function (user_id_from) {

                    // socket.emit(action_id, {
                    //     user_id_to: user_id,
                    //     user_id_from: user_id_from
                    // });

                    $.pjax.reload({container: '#pjax-get-buddies', async: false});

                }).fail(function (request, status, error) {
                    console.log(status + ' - ' + error + request.responseText);
                });
            }

            break;

        case 'cancel-the-bid':

            var data = {
                'user_id': user_id
            };

            AjaxTime(action_url, data).done(function (user_id_from) {

                // socket.emit(action_id, {
                //     user_id_to: user_id,
                //     user_id_from: user_id_from
                // });

                $.pjax.reload({container: '#pjax-inbox-bids', async: false});
                $.pjax.reload({container: '#pjax-outbox-bids', async: false});

            }).fail(function (request, status, error) {
                console.log(status + ' - ' + error + request.responseText);
            });

            break;

        // -------  chat actions  -------- //

        case 'create-the-chat':

            var data = {
                'user_id': user_id
            };

            AjaxTime(action_url, data).done(function (result) {
                $(NewChatId + ' .modal-body').html(result);
            }).fail(function (request, status, error) {
                console.log(status + ' - ' + error + request.responseText);
            });

            break;

        case 'edit-current-chat-form':
            var msg_container = $('.' + msg_container_class),
                chat_id = msg_container.attr('my-daddy-chat-id'),
                data = {
                    'chat_id': chat_id
                };

            $.ajax({
                type: "post",
                url: action_url,
                data: data,
                success: function (result) {
                    $('#edit-current-chat-form-modal .modal-body').html(result);
                    var formData = $('#edit-current-chat').serializeArray();

                    CURRENT_CHAT_FORM = {};
                    CURRENT_CHAT_FORM.members = [];

                    $.each(formData, function (i, val) {
                        if (val.name == 'Chat[title]') {
                            CURRENT_CHAT_FORM.title = val.value;
                        }

                        if (val.name == 'Chat[description]') {
                            CURRENT_CHAT_FORM.description = val.value;
                        }

                        if (val.name == 'ChatMember[chat_members][]') {
                            CURRENT_CHAT_FORM.members.push(val.value);
                        }
                    });
                }
            });

            break;

    }

    ContextMenu.hide();

});

$(document).on('submit', '#create-the-chat', function (e) {
    e.preventDefault();

    var form_data = $(this).serializeArray(),
        action_url = $(this).attr('action');

    AjaxTime(action_url, form_data).done(function (result) {
        // socket.emit('create-new-chat', result);
        $(NewChatId).modal('hide');
        $.pjax.reload({container: '#pjax-get-chats'});
        // делаем активным первый чат в списке
        // $('#pjax-get-chats ul li.chat').eq(0).addClass('active');
    }).fail(function (request, status, error) {
        console.log(status + ' - ' + error + request.responseText);
    });

});

// обрабатываем закрытие модального окна с формой чата
//todo: унифицировать ajax-loader
$(NewChatId).on('hidden.bs.modal', function () {
    $(this).find('.modal-body').html('<div style="text-align: center; padding: 30px 0 50px"><img src="../images/ajax-loader.gif" alt=""></div>');
});

// обрабатываем клик на чат
$('#pjax-get-chats').on('click', '[chat-id]', function () {
    var that = $(this);
    var selected_chat_id = $(this).attr('chat-id');
    var section_Content_Padding = parseInt(section_Content.css('paddingTop'));
    var data = {
        'chat_id': selected_chat_id
    };
    CurrentChatId = selected_chat_id;
    if (!that.hasClass('active')) {
        $('#pjax-get-chats > ul > li.chat-item').removeClass('active');
        that.addClass('active');

        AjaxTime('/chat/get-current-chat', data)
            .beforeSend(function(){
                ajaxLoader(ChatContainer);
            })
            .done(function (result) {
                var data = JSON.parse(result);
                ChatContainer.html(data.view);

        }).fail(function (request, status, error) {
            console.log(status + ' - ' + error + request.responseText);
        });


    }
});
