<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Agreement extends MY_Controller {

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'] && !$this->role['isSecretary'])
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');
    }

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->agreementGetList($CustomerID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function load($CustomerID, $idAgreement) {
        try {
            if (!isset($idAgreement))
                throw new RuntimeException("Не указан обязательный параметр");

            $record = $this->getCustomerModel()->agreementGet($idAgreement);

            if (empty($record))
                throw new RuntimeException("Не найден документ");

            $ext = $record['ext'];
            $fileName = (IS_LOVE_STORY || empty($record['Name']))
                ? "doc-$idAgreement.$ext"
                : $record['Name'];

            $this->file_response(file_get_contents('./files/customer/agreement/'.$record['ID'].'.'.$ext), $ext, $fileName);
        } catch (Exception $e) {
            $this->json_response(array("status" => 0, "err" => $e->getMessage()));
        }
    }

    public function upload($idCustomer) {
        $this->assertUserRight();

        $this->load->view('form/customers/agreements/upload', ['parent' => $idCustomer]);
    }

    public function server($idCustomer) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $records = $this->getCustomerModel()->agreementGetList($idCustomer);

                foreach ($records as $key => $value) {
                    $ext = $value['ext'];
                    $fileName = (IS_LOVE_STORY || empty($value['Name']))
                        ? "doc-".$value['ID'].".$ext"
                        : $value['Name'];

                    $records[$key]['deleteType'] = 'DELETE';
                    $records[$key]['deleteUrl'] = base_url("customer/$idCustomer/agreement/".$value['ID']."/remove");
                    $records[$key]['name'] = $fileName;
                    $records[$key]['size'] = '';
                }

                $this->json_response(array("status" => 1, 'files' => $records));
                break;
            case 'POST':
                // 2. Обработка данных формы
                if (!empty($_FILES)) {
                    try {
                        $file = $_FILES['files'];

                        if (!isset($file['error'][0]) || is_array($file['error'][0]))
                            throw new RuntimeException('Ошибка загрузки файла на сервер');

                        switch ($file['error'][0]) {
                            case UPLOAD_ERR_OK:
                                break;
                            case UPLOAD_ERR_NO_FILE:
                                throw new RuntimeException('Файл не загружен на сервер');
                            case UPLOAD_ERR_INI_SIZE:
                            case UPLOAD_ERR_FORM_SIZE:
                                throw new RuntimeException('Превышен размер файла');
                            default:
                                throw new RuntimeException('Неизвестная ошибка');
                        }

                        $ext = $this->assertFileType($file['tmp_name'][0]);

                        $id = $this->getCustomerModel()->agreementInsert($idCustomer, $file['name'][0], $this->getFileContent($file['tmp_name'][0]), $ext);
                        $this->getCustomerModel()->customerUpdateNote($idCustomer, $this->getUserID(), ['Agreement']);

                        $records = [];
                        $records[] = [
                            'deleteType' => 'DELETE',
                            'deleteUrl' => base_url("customer/$idCustomer/agreement/$id/remove"),
                            'name' => $file['name'][0],
                            'size' => $file['size'][0],
                        ];

                        $res = array('status' => 1, 'files' => $records);
                    } catch (Exception $e) {
                        $res = array('status' => 0, 'message' => $e->getMessage());
                    }

                    $this->json_response($res);
                }

                break;
        }
    }

    public function remove($CustomerID, $idAgreement) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка формы
        try {
            if (!isset($idAgreement))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->agreementDelete($idAgreement);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Agreement']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function show($idCustomer, $idRecord) {
        $record = $this->getCustomerModel()->agreementGetMeta($idRecord);

        $data = array(
            'record' => $record
        );

        $this->load->view('form/customers/agreements/record', $data);
    }

    protected function getFileTypes() {
        return array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
    }

}
