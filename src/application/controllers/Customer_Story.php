<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Story extends MY_Controller {

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->storyGetList($CustomerID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($CustomerID) {
        try {
            $RecordID = $this->input->post('RecordID');
            $Date = $this->input->post('Date');
            $StorySite = $this->input->post('StorySite');
            $Name = $this->input->post('Name');
            $Note = $this->input->post('Note');

            if (empty($CustomerID) || empty($Date) || empty($Name))
                throw new RuntimeException("Не указан обязательный параметр");

            $Date = date_format(date_create_from_format('d.m.Y', $Date), 'Y-m-d');

            if ($RecordID) {
                $this->getCustomerModel()->storyUpdate($RecordID, $Date, $StorySite, $Name, $Note, $this->getImage());
            } else {
                $this->getCustomerModel()->storyInsert($CustomerID, $Date, $StorySite, $Name, $Note, $this->getImage());
            }

            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Story']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->storyDelete($idRecord);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Story']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    private function getImage() {
        if (!empty($_FILES)) {
            $file = $_FILES['thumb'];

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

            return $this->getImageModel()->imageInsert($this->getFileContent($file['tmp_name']), $ext);
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

}