<?php

namespace app\controllers;

use common\models\Buddy;
use common\models\Chat;
use common\models\ChatMember;
use Yii;
use yii\helpers\Html;

class ChatController extends \yii\web\Controller
{

    public function actionCreateTheChat()
    {
        $chat = new Chat();
        $buddies = new Buddy();
        $chat_member = new ChatMember();

        if (Yii::$app->request->isAjax) {

            $create_new_chat = Yii::$app->request->post('create_new_chat');

            if ($create_new_chat) {

                $user_id_from = Yii::$app->user->id;
                $form_data = Yii::$app->request->post('form_data');
                $actual_form_data = [];
                $actual_form_data['users_id'] = [];

                foreach ($form_data as $value) {
                    if ($value['name'] == '_csrf' || $value['name'] == 'create-the-chat' || $value['name'] == 'ChatMember[chat_members]') continue;
                    if ($value['name'] == 'ChatMember[chat_members][]') {
                        array_push($actual_form_data['users_id'], (int)$value['value']);
                    } else {
                        // �����������)) ������ ����� ��� ������))
                        $actual_form_data[explode(']', explode('Chat[', $value['name'])[1])[0]] = $value['value'];
                    }
                }

                array_push($actual_form_data['users_id'], $user_id_from);

                // ���������� ������ � �������
                $created_at = date("Y-m-d H:i:s");

                $chat->title = $actual_form_data['title'];
                $chat->description = $actual_form_data['description'];
                $chat->created_at = $created_at;

                $chat->save();
                // ������� ���
                $chat_id = $chat->getId();

                // ����� �������
                $chat_member->chat_members = $actual_form_data['users_id'];

                for ($i = 0; $i < count($actual_form_data['users_id']); $i++) {
                    if ($i > 0) {
                        $chat_member = new ChatMember();
                        $chat_member->chat_members = $actual_form_data['users_id'];
                    }

                    $chat_member->chat_id = $chat_id;
                    $chat_member->created_at = $created_at;
                    $chat_member->user_id = $chat_member->chat_members[$i];

                    // ������� ����� ��������� id ������ ���� � redis
                    $redis = Yii::$app->redis;
                    $redis->sadd('user:' . $chat_member->user_id . ':chats', $chat_id);

                    $chat_member->save();
                }

                return json_encode([
                    'chat_members' => $actual_form_data['users_id'],
                    'chat_id' => $chat_id
                ]);

            } else {
                $user_id_to = (int)Yii::$app->request->post('user_id');
                array_push($chat_member->chat_members, $user_id_to);

                return $this->renderAjax('-get-create-new-chat-form', [
                    'model' => $chat,
                    'buddies' => $buddies->getBuddies()['arrayDataProvider'],
                    'chat_member' => $chat_member,
                    'user_id' => $user_id_to
                ]);
            }
        }
    }

    public function actionEditCurrentChatForm()
    {
        $chat = new Chat();
        $buddies = new Buddies();
        $chat_member = new ChatMember();
        $redis = Yii::$app->redis;
        $notices = [];

        if (Yii::$app->request->isAjax) {
            $edit_current_chat = Yii::$app->request->post('edit_current_chat');
            $chat_id = Yii::$app->request->post('chat_id');
            $chat_members = $chat_member->getChatMembers($chat_id);
            $chat_info = $chat->find()
                ->where(['id' => $chat_id])
                ->one();

            foreach ($chat_members as $value) {
                array_push($chat_member->chat_members, $value);
            }

            if ($edit_current_chat !== null) {

                $form_data = Yii::$app->request->post('form_data');
                $actual_form_data = [];
                $actual_form_data['users_id'] = [];

                $username_current = User::getUsername(Yii::$app->user->identity->getId());

                $new_chat_info = [
                    'id' => $chat_id,
                ];

                foreach ($form_data as $value) {
                    if ($value['name'] == '_csrf' || $value['name'] == 'edit-current-chat' || $value['name'] == 'ChatMember[chat_members]') continue;
                    if ($value['name'] == 'ChatMember[chat_members][]') {
                        array_push($actual_form_data['users_id'], (int)$value['value']);
                    } else {
                        $actual_form_data[explode(']', explode('Chat[', $value['name'])[1])[0]] = $value['value'];
                    }
                }

                $new_chat_members = array_values(array_diff($actual_form_data['users_id'], $chat_member->chat_members));
                $removed_chat_members = array_values(array_diff($chat_member->chat_members, $actual_form_data['users_id']));


                // remove user with id $edit_chat_members from chat members
                if (count($removed_chat_members) > 0) {
                    foreach ($removed_chat_members as $value) {
                        $chat_member->deleteAll([
                            'chat_id' => $chat_id,
                            'user_id' => $value
                        ]);
                        $redis->srem(
                            'user:' . $value . ':chats',
                            $chat_id
                        );
                    }

                    $new_chat_info['removed_chat_members'] = $removed_chat_members;

                    $message_text = '<b>' . $username_current . '</b> удалил из чата: ';

                    foreach ($removed_chat_members as $key => $removed_chat_member) {
                        $username = User::getUsername($removed_chat_member);
                        if ($key == count($removed_chat_members) - 1) {
                            $message_text .= '<b>' . $username . '</b>.';
                        } else {
                            $message_text .= '<b>' . $username . '</b>, ';
                        }
                    }
                    Message::saveNewNotice($message_text, $chat_id);
                    array_push($notices, $message_text);
                }

                // add user with id $edit_chat_members to chat members
                if (count($new_chat_members) > 0) {
                    $created_at = date("Y-m-d H:i:s");
                    for ($i = 0; $i < count($new_chat_members); $i++) {
                        if ($i > 0) {
                            $chat_member = new ChatMember();
                        }
//                        $chat_member->chat_members  = $new_chat_members;
                        $chat_member->chat_id = $chat_id;
                        $chat_member->created_at = $created_at;
                        $chat_member->user_id = $new_chat_members[$i];
                        $redis->sadd('user:' . $chat_member->user_id . ':chats', $chat_id);
                        $chat_member->save();
                    }
                    $new_chat_info['new_chat_members'] = $new_chat_members;

                    $message_text = '<b>' . $username_current . '</b> добавил в чат: ';

                    foreach ($new_chat_members as $key => $new_chat_member) {
                        $username = User::getUsername($new_chat_member);
                        if ($key == count($new_chat_members) - 1) {
                            $message_text .= '<b>' . $username . '</b>.';
                        } else {
                            $message_text .= '<b>' . $username . '</b>, ';
                        }
                    }
                    Message::saveNewNotice($message_text, $chat_id);
                    array_push($notices, $message_text);
                }

                if ($chat_info['title'] !== $actual_form_data['title'] || $chat_info['description'] !== $actual_form_data['description']) {

                    if ($chat_info['title'] !== $actual_form_data['title']) {
                        $new_chat_info['title'] = $actual_form_data['title'];
                        $chat_info->title = $actual_form_data['title'];

                        $message_text = '<b>' . $username_current . '</b> изменил название чата на: <b>' . $chat_info->title . '</b>';
                        Message::saveNewNotice($message_text, $chat_id);
                        array_push($notices, $message_text);
                    }

                    if ($chat_info['description'] !== $actual_form_data['description']) {
                        $new_chat_info['description'] = $actual_form_data['description'];
                        $chat_info->description = $actual_form_data['description'];

                        $message_text = '<b>' . $username_current . '</b> изменил описание чата на: <b>' . $chat_info->description . '</b>';
                        Message::saveNewNotice($message_text, $chat_id);
                        array_push($notices, $message_text);
                    }

                    $chat_info->save();
                }


                return json_encode([
                    'user_id' => Yii::$app->user->id,
                    'chat_id' => $chat_id,
                    'notices' => $notices
                ]);

            } else {
                $chat->title = $chat_info['title'];
                $chat->description = $chat_info['description'];
                return $this->renderAjax('-edit-current-chat-form', [
                    'model' => $chat,
                    'buddies' => $buddies->getBuddies()['arrayDataProvider'],
                    'chat_member' => $chat_member,
                ]);
            }
        }
    }

    public function actionGetCurrentChat()
    {
        $message = new Message();
        $chat = new Chat();
        $current_user_id = Yii::$app->user->id;

        if (Yii::$app->request->isAjax) {

            $chat_id = Yii::$app->request->post('chat_id');
            $messages = $message->getMessages($chat_id);
            $chat_data = $chat::findOne($chat_id);

            return json_encode([
                'view' => $this->renderAjax('-get-current-chat', [
                    'chat_data' => $chat_data,
                    'message' => $message,
                    'current_user_id' => $current_user_id
                ]),
                'messages' => $messages,
            ]);
        }
    }

    public function actionGetMoreMessages()
    {
        $message = new Message();

        if (Yii::$app->request->isAjax) {
            $chat_id = Yii::$app->request->post('chat_id');
            $last_msg_id = Yii::$app->request->post('last_msg_id');

            $messages = $message->getNewMessages($chat_id, $last_msg_id);

            return json_encode([
                'messages' => $messages,
            ]);
        }
    }

    public function actionRemoveCheckedMessages()
    {
        $message = new Message();

        if (Yii::$app->request->isAjax) {
            $checked_messages = Yii::$app->request->post('checked_messages');
            $removed_request = $message->removeCheckedMessages($checked_messages);

            return json_encode([
                'removed_messages_id' => $removed_request,
            ]);
        }
    }

    public function actionSaveNewMessage()
    {
        $start = microtime(true);
        $message = new Message();
//        $redis = \Yii::$app->redis;

        if (Yii::$app->request->isAjax) {
            $form_data = Yii::$app->request->post('form_data');
            $form_data_message = Html::encode($form_data['message']);
            $form_data_user_id = $form_data['from_user_id'];
            $form_data_chat_id = $form_data['chat_id'];
            $form_data_created_at = date("Y-m-d H:i:s");
            $form_data_message_type = Yii::$app->request->post('type');

            $message->message = $form_data_message;
            $message->user_id = $form_data_user_id;
            $message->chat_id = $form_data_chat_id;
            $message->created_at = $form_data_created_at;
            $message->type = $form_data_message_type;
//            $query = $message::find()
//                ->asArray()
//                ->where(['chat_id' => $form_data_chat_id])
//                ->orderBy(['created_at' => SORT_DESC])
//                ->one();
            $last_message_created_at = $message->getLastMessage($form_data_chat_id);

            if ($last_message_created_at !== null) {
                $last_message_created_at = $last_message_created_at->created_at;
            } else {
                $last_message_created_at = '';
            }

            if ($message->validate() && $message->save()) {

                $message_id = $message->getId();

                $message_data = [
                    'message_type' => $form_data_message_type,
                    'message_text' => $form_data_message,
                    'message_chat_id' => $form_data_chat_id,
                    'message_created_at' => $form_data_created_at,
                    'message_from_user_id' => $form_data_user_id,
                    'message_id' => $message_id,
                    'script_time' => microtime(true) - $start,
                    'last_message_created_at' => $last_message_created_at,
//                'previous_message_user_id'  => $form_data_user_id,
                ];

                return json_encode($message_data);

            }
        }
    }

    public function actionLeaveChat()
    {
        $chat = new Chat();
        $chat_member = new ChatMember();
        $redis = Yii::$app->redis;

        if (Yii::$app->request->isAjax) {
            $chat_id = Yii::$app->request->post('chat-id');
            $user_id = Yii::$app->user->id;

            $query = $chat_member->find()
                ->where(['chat_id' => $chat_id])
                ->count();

            $chat_member->deleteAll([
                'chat_id' => $chat_id,
                'user_id' => $user_id
            ]);

            $redis->srem('user:' . $user_id . ':chats', $chat_id);

            if ((int)$query == 2) {
                $return = [
                    'chat_id' => $chat_id,
                    'chat_members_count' => (int)$query - 1 // остался один
                ];
            } elseif ((int)$query < 2) {
                $chat->deleteAll([
                    'id' => $chat_id,
                ]);

                $message = new Message();
                $message->deleteAll([
                    'chat_id' => $chat_id,
                ]);

                $return = [
                    'chat_id' => $chat_id,
                    'chat_members_count' => (int)$query - 1 // никого не осталось
                ];
            } else {
                $return = [
                    'chat_id' => $chat_id,
                    'chat_members_count' => (int)$query - 1 // минимум двое еще есть
                ];
            }

            return json_encode($return);
        }
    }
}
