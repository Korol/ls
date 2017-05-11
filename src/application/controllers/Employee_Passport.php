<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Passport extends MY_Controller {

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'] && !$this->role['isSecretary'])
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');
    }

    public function data($EmployeeID) {
        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getEmployeeModel()->passportGetList($EmployeeID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function load($EmployeeID, $idPassport) {
        try {
            if (!isset($idPassport))
                throw new RuntimeException("Не указан обязательный параметр");

            $record = $this->getEmployeeModel()->passportGet($idPassport);

            if (empty($record))
                throw new RuntimeException("Не найден документ");

            $ext = $record['ext'];
            $fileName = (IS_LOVE_STORY || empty($record['Name']))
                ? "scan-$idPassport.$ext"
                : $record['Name'];

            $this->file_response(file_get_contents('./files/employee/passport/'.$record['ID'].'.'.$ext), $ext, $fileName);
        } catch (Exception $e) {
            $this->json_response(array("status" => 0, "err" => $e->getMessage()));
        }
    }

    public function upload($idEmployee) {
        $this->assertUserRight();

        $this->load->view('form/employees/passports/upload', ['parent' => $idEmployee]);
    }

    public function server($idEmployee) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $records = $this->getEmployeeModel()->passportGetList($idEmployee);

                foreach ($records as $key => $value) {
                    $ext = $value['ext'];
                    $fileName = (IS_LOVE_STORY || empty($value['Name']))
                        ? "scan-".$value['ID'].".$ext"
                        : $value['Name'];

                    $records[$key]['deleteType'] = 'DELETE';
                    $records[$key]['deleteUrl'] = base_url("employee/$idEmployee/passport/".$value['ID']."/remove");
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

                        $id = $this->getEmployeeModel()->passportInsert($idEmployee, $file['name'][0], $this->getFileContent($file['tmp_name'][0]), $ext);
                        $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $idEmployee, ['Passport']);

                        $records = [];
                        $records[] = [
                            'deleteType' => 'DELETE',
                            'deleteUrl' => base_url("employee/$idEmployee/passport/$id/remove"),
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

    public function remove($EmployeeID, $idPassport) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка формы
        try {
            if (!isset($idPassport))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getEmployeeModel()->passportDelete($idPassport);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Passport']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function show($idEmployee, $idRecord) {
        $record = $this->getEmployeeModel()->passportGetMeta($idRecord);

        $data = array(
            'record' => $record
        );

        $this->load->view('form/employees/passports/record', $data);
    }

    protected function getFileTypes() {
        return array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    }

}
