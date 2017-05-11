<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends MY_Controller {

    public function index() {
        // Для сайта Assol доступ есть только у директора и секретаря
        if ($this->isAssol() && !($this->isDirector() || $this->isSecretary()))
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');

        $data = array(
            'user_role' => $this->getReferencesModel()->getReference(REFERENCE_USER_ROLE),
            'sites' => $this->getSites()
        );

        $this->viewHeader($data);
        $this->view('form/employees/index');
        $this->viewFooter([
            'js_array' => [
                'public/js/assol.employee.list.js'
            ]
        ]);
    }

    public function data() {
        // Для сайта Assol доступ есть только у директора и секретаря
        if ($this->isAssol() && !($this->isDirector() || $this->isSecretary()))
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');

        try {
            $data = $this->input->post('data');

            $result = $this->getEmployeeModel()->employeeGetList($this->getUserID(), $this->getUserRole(), $data);

            // Для сайта LoveStory добавляем график работы в карточку сотрудника
            if (IS_LOVE_STORY) {
                foreach ($result['records'] as $key => $employee) {
                    $result['records'][$key]['schedule'] = $this->getScheduleModel()->scheduleGet($employee['ID']);
                }
            }

            $this->json_response(array("status" => 1, 'data' => $result));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'])
            show_error('Данный раздел доступен только для роли "Директор"', 403, 'Доступ запрещен');
    }

    public function add() {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $sName = $this->input->post('sName');
                $fName = $this->input->post('fName');
                $mName = $this->input->post('mName');

                if (empty($sName))
                    throw new Exception('Не указана фамилия сотрудника');

                if (empty($fName))
                    throw new Exception('Не указано имя сотрудника');

                // Добавление нового пользователя
                $id = $this->getEmployeeModel()->employeeInsert($sName, $fName, $mName);

                // Привязка общих чатов к новому пользователю
                $this->getMessageModel()->connectCommonChats($id);

                $res = array('status' => 1, 'id' => $id);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        // 3. Загрузка шаблона
        $this->load->view('form/employees/add');
    }

    public function update($id) {
        try {
            $data = $this->input->post('data');

            if (empty($data))
                throw new Exception('Нет данных для сохранения');

            // Сбор полей для истории
            $fields = [];
            foreach ($data as $key => $value)
                $fields[] = $key;

            $this->getEmployeeModel()->employeeUpdate($id, $data);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $id, $fields);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function avatar($EmployeeID) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        try {
            $data = $this->input->post('data');

            $image = $this->getImage();
            if (is_numeric($image['id'])) {
                $this->clearEmployeeAvatar($EmployeeID);
                $data = array('Avatar' => $image['id']);
            }

            if (empty($data))
                throw new Exception('Нет данных для сохранения');

            $this->getEmployeeModel()->employeeUpdate($EmployeeID, $data);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Avatar']);

            // Если пользователь меняет свой аватар, то обнавляем инфу в сессии для шапки сайта
            if ($image && ($this->getUserID() == $EmployeeID)) {
                $this->load->library('session');

                $this->user['Avatar'] = $image['id'].'.'.$image['ext'];
                $this->session->set_userdata(array('user' => $this->user));
            }

            $this->json_response(array('status' => 1, 'id' => $image['id'], 'FileName' => $image['id'].'.'.$image['ext']));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function profile($id) {
        // Для сайта Assol доступ есть только у директора и секретаря
        if ($this->isAssol() && !($this->isDirector() || $this->isSecretary()))
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');

        // Устанавливаем и обнуляем переменную для сборка истории последних правок профиля
        $this->session->set_userdata(['UpdateEmployeeFields' => []]);

        $data = array(
            'js_array' => array(
                'public/js/assol.employee.card.js'
            ),
            'employee' => $this->getEmployeeModel()->employeeGet($id),
            'employees' => $this->getEmployeeModel()->employeeGetFilterRoleList($id, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE]),
            'work_sites' => $this->getEmployeeModel()->siteGetList($id),
            'forming' => $this->getReferencesModel()->getReference(REFERENCE_FORMING),
            'forming_form' => $this->getReferencesModel()->getReference(REFERENCE_FORMING_FORM),
            'child_sex' => $this->getReferencesModel()->getReference(REFERENCE_CHILD_SEX),
            'marital' => $this->getReferencesModel()->getReference(REFERENCE_MARITAL),
            'user_role' => $this->getReferencesModel()->getReference(REFERENCE_USER_ROLE),
            'sites' => $this->getSiteModel()->getRecords(),
            'rights' => $this->getEmployeeModel()->rightsGetList($id)
        );

        // Для сайта LoveStory добавляем график работы в карточку сотрудника
        if (IS_LOVE_STORY) {
            $data['schedule'] = $this->getScheduleModel()->scheduleGet($id);
        }

        $this->viewHeader($data);
        $this->view('form/employees/profile');
        $this->viewFooter();
    }

    public function rights($EmployeeID) {
        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $employees = $this->input->post('Employees');

            // Отключенных прав, не пришедших в списке
            $this->getEmployeeModel()->rightsRemove($EmployeeID, $employees);

            // Добавление необходимых прав
            foreach ($employees as $employee) {
                $this->getEmployeeModel()->rightsInsert($EmployeeID, $employee);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($EmployeeID) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $isFull = $this->input->post('IsFull');

            if ($isFull) {
                $this->clearEmployeeAvatar($EmployeeID);
                $this->getEmployeeModel()->employeeDelete($EmployeeID);
            } else {
                $this->getEmployeeModel()->employeeUpdate($EmployeeID, array(
                    'DateDeleted' => date('Y-m-d'), 'IsDeleted' => 1, 'WhoDeleted' => $this->getUserID()));
                $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['IsDeleted']);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    private function clearEmployeeAvatar($EmployeeID) {
        $employee = $this->getEmployeeModel()->employeeGet($EmployeeID);

        if ($employee['Avatar'] > 0) {
            $this->getImageModel()->remove($employee['Avatar']);
        }
    }

    public function restore($EmployeeID) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getEmployeeModel()->employeeUpdate($EmployeeID, array(
                'ReasonForDeleted' => null, 'DateDeleted' => null, 'IsDeleted' => 0, 'WhoDeleted' =>  null));
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['IsDeleted']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /** Получить список ID онлайн пользователей */
    public function online() {
        $this->json_response(
            array_map(
                function ($item) {
                    return $item['id'];
                },
                $this->getEmployeeModel()->onlineGetList())
        );
    }

    private function getSites() {
        return array_merge(
            array(
                array(
                    'ID' => 0,
                    'Name' => 'Все',
                    'Domen' => 'Все',
                    'Note' => ''
                )
            ),
            $this->getSiteModel()->getRecords()
        );
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
}
