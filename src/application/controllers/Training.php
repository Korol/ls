<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Training extends MY_Controller {

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'])
            show_error('Данный раздел доступен только для роли "Директор"', 403, 'Доступ запрещен');
    }

    public function index($idFolder = 0) {
        $data = array(
            'Parent' => $idFolder
        );

        $this->viewHeader($data);
        $this->view('form/training/index');
        $this->viewFooter([
            'js_array' => [
                'public/js/assol.training.js'
            ]
        ]);
    }

    public function data() {
        try {
            $parent = $this->input->post('Parent');

            $data = array(
                'bread' => $this->getTrainingModel()->breadGetList($parent)
            );

            $isFullAccess = IS_LOVE_STORY && $this->isDirector(); // Для директора LoveStory полный доступ

            if ($isFullAccess || $this->getTrainingModel()->checkRights($parent, $this->getUserID())) {
                $data['data'] = [];

                $objects = $this->getTrainingModel()->trainingGetList($parent);

                foreach ($objects as $object) {
                    // Если нет прав, то пропускаем
                    if (!$isFullAccess && !$this->getTrainingModel()->checkRights($object['ID'], $this->getUserID()))
                        continue;

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

                $id = $this->getTrainingModel()->trainingInsert($Name, $Parent, $this->getUserID());
                $this->getTrainingModel()->trainingRightInsert($id, $Employees);

                $res = array('status' => 1, 'id' => $id);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        $data = array(
            'folders' => $this->getTrainingModel()->folderGetList(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );

        // 3. Загрузка шаблона
        $this->load->view('form/training/add_folder', $data);
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

                $this->getTrainingModel()->folderUpdate($id, $Name, $Parent);
                $this->getTrainingModel()->trainingRightUpdate($id, $Employees, $IsSub);

                $res = array('status' => 1, 'id' => $id);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        $data = array(
            'record' => $this->getTrainingModel()->trainingGet($id),
            'rights' => $this->getTrainingModel()->getFolderRights($id),
            'folders' => $this->getTrainingModel()->folderGetList(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );

        // 3. Загрузка шаблона
        $this->load->view('form/training/edit_folder', $data);
    }

    public function add($idFolder = 0) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $Parent = $this->input->post('Parent');
                $TrainingName = $this->input->post('TrainingName');
                $TrainingContent = $this->input->post('TrainingContent');
                $Employees = $this->input->post('Employees');

                if (!empty($Employees) && !in_array($this->getUserID(), $Employees)) {
                    $Employees[] = $this->getUserID(); // Добавление текущего пользователя к объекту прав
                }

                if (empty($TrainingName))
                    throw new Exception('Не указано имя статьи');

                $id = $this->getTrainingModel()->trainingInsert($TrainingName, $Parent, $this->getUserID(), false, $TrainingContent);
                $this->getTrainingModel()->trainingRightInsert($id, $Employees);

                $res = array('status' => 1, 'id' => $id);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        // 3. Загрузка шаблона
        $data = array(
            'Parent' => $idFolder,
            'bread' => $this->getTrainingModel()->breadGetList($idFolder),
            'folders' => $this->getTrainingModel()->folderGetList(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );

        $this->viewHeader($data);
        $this->view('form/training/add_training');
        $this->viewFooter([
            'isWysiwyg' => true,
            'js_array' => [
                'public/js/assol.training.article.js'
            ]
        ]);
    }

    public function edit($idFolder, $idRecord) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $Parent = $this->input->post('Parent');
                $TrainingName = $this->input->post('TrainingName');
                $TrainingContent = $this->input->post('TrainingContent');
                $Employees = $this->input->post('Employees');

                if (!empty($Employees) && !in_array($this->getUserID(), $Employees)) {
                    $Employees[] = $this->getUserID(); // Добавление текущего пользователя к объекту прав
                }

                if (empty($TrainingName))
                    throw new Exception('Не указано имя статьи');

                $this->getTrainingModel()->trainingUpdate($idRecord, $TrainingName, $Parent, $TrainingContent);
                $this->getTrainingModel()->trainingRightUpdate($idRecord, $Employees);

                $res = array('status' => 1, 'id' => $idRecord);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        // 3. Загрузка шаблона
        $data = array(
            'record' => $this->getTrainingModel()->trainingGet($idRecord),
            'bread' => $this->getTrainingModel()->breadGetList($idFolder),
            'Parent' => $idFolder,
            'rights' => $this->getTrainingModel()->getFolderRights($idRecord),
            'folders' => $this->getTrainingModel()->folderGetList(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );

        $this->viewHeader($data);
        $this->view('form/training/edit_training');
        $this->viewFooter([
            'isWysiwyg' => true,
            'js_array' => [
                'public/js/assol.training.article.js'
            ]
        ]);
    }

    public function show($idFolder, $idRecord) {
        $isFullAccess = IS_LOVE_STORY && $this->isDirector(); // Для директора LoveStory полный доступ

        // 1. Проверка прав доступа
        if (!$isFullAccess && !$this->getTrainingModel()->checkRights($idRecord, $this->getUserID())) {
            show_error('Доступ для пользователя закрыт', 403, 'Доступ запрещен');
        }

        $data = [
            'bread' => $this->getTrainingModel()->breadGetList($idFolder),
            'record' => $this->getTrainingModel()->trainingGet($idRecord),
            'Parent' => $idFolder
        ];

        $this->viewHeader($data);
        $this->view('form/training/show_training');
        $this->viewFooter();
    }

    public function remove() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            $id = $this->input->post('id');

            if (!isset($id))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getTrainingModel()->trainingDelete($id);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}
