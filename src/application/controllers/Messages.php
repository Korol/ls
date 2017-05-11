<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends MY_Controller {

//    public function index() {
//        $data = [
//            'role' => $this->role,
//            'user' => $this->user,
//            'allEmployees' => $this->getEmployeeModel()->employeeGetList(),
//            'employees' => $this->getEmployeeModel()->employeeOtherGetList($this->getUserID(), $this->getUserRole()),
//            'order' => $this->getMessageModel()->messageOrder($this->getUserID())
//        ];
//
//        $this->load->view('form/message/messages', $data);
//    }
//
//    public function data() {
//        try {
//            $sender = $this->input->post('sender');
//            $recipient = $this->input->post('recipient');
//            $limit = $this->input->post('limit');
//
//            if (empty($sender))
//                throw new RuntimeException('Не указан отправитель сообщения');
//
//            if (empty($recipient) && !is_numeric($recipient))
//                throw new RuntimeException('Не указан получатель сообщения');
//
//            $data = $this->getMessageModel()->messageGetList($sender, $recipient, $limit, $this->getUserID(), $this->getUserRole());
//
//            $this->json_response(["status" => 1, 'records' => $data]);
//        } catch (Exception $e) {
//            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
//        }
//    }
//
//    /** Получение списка чатов и пользователей */
//    public function chats() {
//        // Основной чат приложения
//        $mainChat = [['ID' => -1, 'name' => 'Общий чат', 'MaxMessageID' => 0, 'isChat' => 1]];
//        // Список чатов доступных пользователю
//        $userChats = $this->getMessageModel()->chatGetList($this->getUserID());
//
//        $this->json_response([
//            'chats' => array_merge($mainChat, $userChats),
//            'users' => $this->getEmployeeModel()->employeeOtherGetList($this->getUserID(), $this->getUserRole()),
//            'online' => array_map(
//                function ($item) {
//                    return $item['id'];
//                }, $this->getEmployeeModel()->onlineGetList()),
//            'unread' => $this->getMessageModel()->messageUnreadForEmployees($this->getUserID())
//        ]);
//    }
//
//    public function history() {
//        try {
//            $sender = $this->input->post('sender');
//            $recipient = $this->input->post('recipient');
//            $min = $this->input->post('min');
//            $limit = $this->input->post('limit');
//
//            if (empty($sender))
//                throw new RuntimeException('Не указан отправитель сообщения');
//
//            if (empty($recipient) && !is_numeric($recipient))
//                throw new RuntimeException('Не указан получатель сообщения');
//
//            $data = $this->getMessageModel()->messageHistoryGetList($sender, $recipient, $min, $limit, $this->getUserID(), $this->getUserRole());
//
//            $this->json_response(["status" => 1, 'records' => $data]);
//        } catch (Exception $e) {
//            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
//        }
//    }
//
//    public function now() {
//        try {
//            $recipient = $this->input->post('recipient');
//            $lastMessageID = $this->input->post('lastMessageID');
//
//            if (empty($recipient) && !is_numeric($recipient))
//                throw new RuntimeException('Не указан получатель сообщения');
//
//            $data = [
//                'messages' => $this->getMessageModel()->messageNowGetList($this->getUserID(), $recipient, $lastMessageID, $this->getUserID(), $this->getUserRole()),
//                'time_read' => $this->getMessageModel()->messageTimeRead($this->getUserID(), $recipient)
//            ];
//
//            $this->json_response(["status" => 1, 'records' => $data]);
//        } catch (Exception $e) {
//            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
//        }
//    }
//
//    public function read() {
//        try {
//            $recipient = $this->input->post('recipient');
//
//            if (empty($recipient))
//                throw new RuntimeException('Не указан получатель');
//
//            $this->getMessageModel()->messageRead($this->getUserID(), $recipient);
//
//            $this->json_response(["status" => 1]);
//        } catch (Exception $e) {
//            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
//        }
//    }
//
//    public function events() {
//        try {
//            $data = [
//                'messages' => $this->getMessageModel()->messageUnreadForEmployees($this->getUserID()),
//                'online' => $this->getEmployeeModel()->onlineGetList()
//            ];
//
//            $this->json_response(["status" => 1, 'records' => $data]);
//        } catch (Exception $e) {
//            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
//        }
//    }
//
//    public function unread() {
//        $this->json_response($this->getMessageModel()->messageUnreadForEmployees($this->getUserID()));
//    }
//
//    public function send() {
//        try {
//            $sender = $this->input->post('sender');
//            $recipient = $this->input->post('recipient');
//            $message = $this->input->post('message');
//
//            if (empty($sender))
//                throw new RuntimeException('Не указан отправитель сообщения');
//
//            if (empty($recipient) && !is_numeric($recipient))
//                throw new RuntimeException('Не указан получатель сообщения');
//
//            if (empty($message))
//                throw new RuntimeException('Не указан текст сообщения');
//
//            $id = $this->getMessageModel()->messageInsert($sender, $recipient, $message);
//
//            $this->json_response(array("status" => 1, "id" => $id));
//        } catch (Exception $e) {
//            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
//        }
//    }
//
//    public function upload() {
//        // 1. Обработка формы
//        if (!empty($_FILES)) {
//            try {
//                $recipient = $this->input->post('recipient');
//
//                $image = $this->getImage();
//
//                $url = base_url('thumb') . '/?src=/files/images/' . $image['id'].'.'.$image['ext'];
//
//                $message = '<a href="'.$url.'" data-lightbox="MessageImage"><img src="'.$url.'&w=100"></a>';
//
//                $id = $this->getMessageModel()->messageInsert($this->getUserID(), $recipient,  $message);
//                $this->getMessageModel()->messageImageInsert($id, $image['id']);
//
//                $this->json_response(array("status" => 1, 'id' => $id, 'message' => $message));
//            } catch (Exception $e) {
//                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
//            }
//        }
//
//        // 2. Загрузка шаблона
//        $this->load->view('form/message/upload');
//    }
//
//    private function getImage() {
//        if (!empty($_FILES)) {
//            $file = $_FILES['upload'];
//
//            if (!isset($file['error']) || is_array($file['error']))
//                throw new RuntimeException('Ошибка загрузки файла на сервер');
//
//            switch ($file['error']) {
//                case UPLOAD_ERR_OK:
//                    break;
//                case UPLOAD_ERR_NO_FILE:
//                    return null;
////                    throw new RuntimeException('Файл не загружен на сервер');
//                case UPLOAD_ERR_INI_SIZE:
//                case UPLOAD_ERR_FORM_SIZE:
//                    throw new RuntimeException('Превышен размер файла');
//                default:
//                    throw new RuntimeException('Неизвестная ошибка');
//            }
//
//            $ext = $this->assertFileType($file['tmp_name']);
//
//            return [
//                'id' => $this->getImageModel()->imageInsert($this->getFileContent($file['tmp_name']), $ext),
//                'ext' => $ext
//            ];
//        }
//
//        return null;
//    }
//
//    protected function getFileTypes() {
//        return array(
//            'jpg' => 'image/jpeg',
//            'png' => 'image/png',
//            'gif' => 'image/gif'
//        );
//    }

}
