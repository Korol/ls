<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends MY_Controller {

    /** Отправить сообщение */
    public function send() {
        try {
            $isChat = $this->input->post('isChat');
            $recipient = $this->input->post('recipient');
            $message = $this->input->post('message');

            if (empty($isChat) && !is_numeric($isChat))
                throw new RuntimeException('Не указан тип получателя');

            if (empty($recipient) && !is_numeric($recipient))
                throw new RuntimeException('Не указан ID получателя сообщения');

            if (empty($message))
                throw new RuntimeException('Не указан текст сообщения');

            $message = $this->getMessageModel()->chatMessageSend($isChat, $this->getUserID(), $recipient, $message);

            $this->json_response(array("status" => 1, "message" => $message));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /** Получение списка чатов */
    public function chats() {
        $this->json_response([
            'chats' => $this->getMessageModel()->chatGetList($this->getUserID(), $this->getUserRole()),
            'unread' => $this->getMessageModel()->chatMessageUnread($this->getUserID())
        ]);
    }

    /** Получение списка пользователей чата */
    public function chat_users() {
        $id = $this->input->post('id');

        $this->json_response([
            'selected' => $this->getMessageModel()->chatEmployees($id)
        ]);
    }

    /** Создать чат */
    public function save() {
        if (!$this->isDirector())
            show_error('Данный раздел доступен только для директора', 403, 'Доступ запрещен');

        try {
            $id = $this->input->post('id');
            $type = $this->input->post('type');
            $avatar = $this->input->post('avatar');
            $access = $this->input->post('access');
            $name = $this->input->post('name');
            $employees = $this->input->post('employees');

            if (empty($type) && !is_numeric($type))
                throw new RuntimeException('Не указан тип чата');

            if (empty($access) && !is_numeric($access))
                throw new RuntimeException('Не указаны права доступа');
            
            if (empty($name))
                throw new RuntimeException('Не указано название чата');

            // Если не указан список пользователей и это не общий чат
            if (empty($employees) && !empty($type))
                throw new RuntimeException('Не указан список сотрудников');

            $chat = $this->getMessageModel()->chatSave($this->getUserID(), $id, $type, $access, $name, $employees, $avatar);

            $this->json_response(array("status" => 1, 'chat' => $chat));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    private function clearAvatar($idChat) {
        $chat = $this->getMessageModel()->chatFindById($idChat);

        if ($chat['avatar']) {
            $this->getImageModel()->remove($chat['avatar']);
        }
    }

    /** Удалить чат */
    public function remove() {
        if (!$this->isDirector())
            show_error('Данный раздел доступен только для директора', 403, 'Доступ запрещен');

        try {
            $id = $this->input->post('id');

            $this->clearAvatar($id);
            $this->getMessageModel()->chatRemove($id);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /** Получения списка непрочитанных сообщений */
    public function unread() {
        $this->json_response($this->getMessageModel()->chatMessageUnread($this->getUserID()));
    }

    /** Получения списка новых сообщений */
    public function check() {
        try {
            $isChat = $this->input->post('isChat');
            $recipient = $this->input->post('recipient');
            $idMaxMessage = $this->input->post('idMaxMessage');

            if (empty($isChat) && !is_numeric($isChat))
                throw new RuntimeException('Не указан тип получателя');

            if (empty($recipient))
                throw new RuntimeException('Не указан получатель');

            if (empty($idMaxMessage) && !is_numeric($idMaxMessage))
                throw new RuntimeException('Не указан максимальный ID сообщения');

            $this->json_response([
                "status" => 1, 
                'records' => $this->getMessageModel()->chatNewMessageGetList($this->getUserID(), $isChat, $recipient, $idMaxMessage, $this->getUserRole()),
                'recipient_chat_info' => $this->getMessageModel()->chatTimeRead($isChat, $this->getUserID(), $recipient)
            ]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    /** Выстовить метку прочтения */
    public function read() {
        try {
            $isChat = $this->input->post('isChat');
            $recipient = $this->input->post('recipient');
            $idMaxMessage = $this->input->post('idMaxMessage');

            if (empty($isChat) && !is_numeric($isChat))
                throw new RuntimeException('Не указан тип получателя');

            if (empty($recipient))
                throw new RuntimeException('Не указан получатель');

            if (empty($idMaxMessage) && !is_numeric($idMaxMessage))
                throw new RuntimeException('Не указан максимальный ID сообщения');

            $this->getMessageModel()->chatMessageRead($this->getUserID(), $isChat, $recipient, $idMaxMessage);

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function messages() {
        try {
            $sender = $this->input->post('sender');
            $isChat = (int) $this->input->post('isChat');
            $recipient = $this->input->post('recipient');
            $limit = $this->input->post('limit');

            if (empty($isChat) && !is_numeric($isChat))
                throw new RuntimeException('Не указан тип получателя');

            if (empty($recipient) && !is_numeric($recipient))
                throw new RuntimeException('Не указан получатель сообщения');

            $data = $this->getMessageModel()->chatMessageGetList($isChat, $recipient, $limit, ($sender ? $sender : $this->getUserID()), $this->getUserRole());

            $this->json_response([
                "status" => 1,
                'records' => $data,
                'recipient_chat_info' => $this->getMessageModel()->chatTimeRead($isChat, $this->getUserID(), $recipient)
            ]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function history() {
        try {
            $sender = $this->input->post('sender');
            $isChat = $this->input->post('isChat');
            $recipient = $this->input->post('recipient');
            $min = $this->input->post('min');
            $limit = $this->input->post('limit');

            if (empty($recipient) && !is_numeric($recipient))
                throw new RuntimeException('Не указан получатель сообщения');

            $data = $this->getMessageModel()->chatMessageGetList($isChat, $recipient, $limit, ($sender ? $sender : $this->getUserID()), $this->getUserRole(), $min);

            $this->json_response(["status" => 1, 'records' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function upload() {
        // 1. Обработка формы
        if (!empty($_FILES)) {
            try {
                $recipient = $this->input->post('recipient');
                $isChat = $this->input->post('isChat');

                $image = $this->getImage();

                $url = base_url('thumb') . '/?src=/files/images/' . $image['id'].'.'.$image['ext'];

                $message = '<a href="'.$url.'" data-lightbox="MessageImage"><img src="'.$url.'&w=100"></a>';

                $message = $this->getMessageModel()->chatMessageSend($isChat, $this->getUserID(), $recipient, $message);
                $this->getMessageModel()->messageImageInsert($message['id'], $image['id']); // TODO: Подумать, создать таблицу или эту использовать?

                $this->json_response(array("status" => 1, "message" => $message));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        // 2. Загрузка шаблона // TODO: Проверить - убрать?
        $this->load->view('form/message/upload');
    }

    public function avatar() {
        // 1. Обработка формы
        if (!empty($_FILES)) {
            try {
                if (!$this->isDirector())
                    show_error('Данный раздел доступен только для директора', 403, 'Доступ запрещен');

                $recipient = $this->input->post('recipient');

                $image = $this->getImage();

                // TODO: Подумать - сделать сразу сохрвнение?

                $this->json_response(array("status" => 1, "avatar" => $image['id'], "FileName" => $image['id'] . '.' . $image['ext']));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }
    }

    private function getImage() {
        if (!empty($_FILES)) {
            $file = $_FILES['upload'];

            if (!isset($file['error']) || is_array($file['error']))
                throw new RuntimeException('Ошибка загрузки файла на сервер');

            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    return null;
//                    throw new RuntimeException('Файл не загружен на сервер');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Превышен размер файла');
                default:
                    throw new RuntimeException('Неизвестная ошибка');
            }

            $ext = $this->assertFileType($file['tmp_name']);

            return [
                'id' => $this->getImageModel()->imageInsert($this->getFileContent($file['tmp_name']), $ext),
                'ext' => $ext
            ];
        }

        return null;
    }

    protected function getFileTypes() {
        return array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    }

//    public function import() {
//        $this->getMessageModel()->importChatLastSystem();
//    }

}
