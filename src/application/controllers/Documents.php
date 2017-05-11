<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Documents extends MY_Controller {

    public function index() {
        $this->viewHeader();
        $this->view('form/documents/index');
        $this->viewFooter([
            'js_array' => [
                'public/js/assol.document.js'
            ]
        ]);
    }

    public function data() {
        try {
            $parent = $this->input->post('Parent');

            $data = array(
                'bread' => $this->getDocumentModel()->breadGetList($parent)
            );

            $isFullAccess = IS_LOVE_STORY && $this->isDirector(); // Для директора LoveStory полный доступ

            if ($isFullAccess || $this->getDocumentModel()->checkRights($parent, $this->getUserID())) {

                $data['data'] = [];

                $objects = $this->getDocumentModel()->documentGetList($parent);

                foreach ($objects as $object) {
                    if ($object['IsFolder'] > 0) {
                        // Если на папку нет прав, то пропускаем ее
                        if (!$isFullAccess && !$this->getDocumentModel()->checkRights($object['ID'], $this->getUserID()))
                            continue;
                    }

                    $data['data'][] = $object;
                }


            } else {
                $data['AccessDenied'] = true;
            }

            $this->json_response(array("status" => 1, 'records' => $data));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'] && !$this->role['isSecretary'])
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');
    }

    public function add_folder() {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $Name = $this->input->post('Name');
                $Parent = $this->input->post('Parent');
                $Employees = $this->input->post('Employees');

                if (!empty($Employees) && !in_array($this->getUserID(), $Employees)) {
                    $Employees[] = $this->getUserID(); // Добавление текущего пользователя к объекту прав
                }

                if (empty($Name))
                    throw new Exception('Не указано имя папки');

                $id = $this->getDocumentModel()->documentInsert($Name, $Parent, $this->getUserID());
                $this->getDocumentModel()->folderRightInsert($id, $Employees);

                $res = array('status' => 1, 'id' => $id);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        $data = array(
            'folders' => $this->getDocumentModel()->folderGetList(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );

        // 3. Загрузка шаблона
        $this->load->view('form/documents/add_folder', $data);
    }

    public function edit_folder($id) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $Name = $this->input->post('Name');
                $Parent = $this->input->post('Parent');
                $Employees = $this->input->post('Employees');
                $IsSub = $this->input->post('IsSub');

                if (!empty($Employees) && !in_array($this->getUserID(), $Employees)) {
                    $Employees[] = $this->getUserID(); // Добавление текущего пользователя к объекту прав
                }

                if (empty($Name))
                    throw new Exception('Не указано имя папки');

                $this->getDocumentModel()->folderUpdate($id, $Name, $Parent);
                $this->getDocumentModel()->folderRightUpdate($id, $Employees, $IsSub);

                $res = array('status' => 1, 'id' => $id);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        $data = array(
            'record' => $this->getDocumentModel()->documentGet($id),
            'rights' => $this->getDocumentModel()->getFolderRights($id),
            'folders' => $this->getDocumentModel()->folderGetList(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );

        // 3. Загрузка шаблона
        $this->load->view('form/documents/edit_folder', $data);
    }

    public function remove() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            $id = $this->input->post('id');

            if (!isset($id))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getDocumentModel()->documentDelete($id);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function load($id) {
        try {
            if (!isset($id))
                throw new RuntimeException("Не указан обязательный параметр");

            $file = $this->getDocumentModel()->documentGet($id);

            if (empty($file))
                throw new RuntimeException("Не найден документ");

            $info = new SplFileInfo($file['Name']);
            $this->file_response(file_get_contents('./files/document/'.$file['ID']), $info->getExtension(), $file['Name']);
        } catch (Exception $e) {
            $this->json_response(array("status" => 0, "err" => $e->getMessage()));
        }
    }

    public function delete($id) {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            if (!isset($id))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getDocumentModel()->documentDelete($id);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function upload($parent) {
        $this->load->view('form/documents/upload', ['parent' => $parent]);
    }

    public function server($parent) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $data = $this->getDocumentModel()->documentGetList($parent);
                $records = [];

                foreach ($data as $value) {
                    // Пропуск папок
                    if ($value['IsFolder'] > 0)
                        continue;

                    $records[] = $value;
                }

                foreach ($records as $key => $value) {
                    // Пропуск файлов
                    if ($value['IsFolder'] > 0) continue;

                    $records[$key]['deleteType'] = 'DELETE';
                    $records[$key]['deleteUrl'] = base_url("documents/delete/".$value['ID']);
                    $records[$key]['name'] = $value['Name'];
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

                        $id = $this->getDocumentModel()->documentInsert($file['name'][0], $parent, $this->getUserID(), false, $this->getFileContent($file['tmp_name'][0]));

                        $records = [];
                        $records[] = [
                            'deleteType' => 'DELETE',
                            'deleteUrl' => base_url("documents/delete/$id"),
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

}
