<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends MY_Controller {

    public function index() {
        $this->viewHeader();
        $this->view('form/customers/index');
        $this->viewFooter();
    }

    public function meta() {
        try {
            $data = [
                'minMaxAge' => $this->getCustomerModel()->getMinMaxAge(),
                'isLoveStoryCustomerCard' => IS_LOVE_STORY,
                'isAssolCustomerCard' => !IS_LOVE_STORY
            ];
            
            $this->json_response(["status" => 1, 'data' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function data() {
        try {
            $data = $this->input->post('data');
            if (IS_LOVE_STORY) {
                $filterUserId = $this->isTranslate() ? $this->getUserID() : FALSE;
            } else {
                $filterUserId = ($this->isTranslate() || $this->isEmployee()) ? $this->getUserID() : FALSE;
            }

            $this->json_response(array("status" => 1, 'data' => $this->getCustomerModel()->customerGetList($filterUserId, $data)));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (IS_LOVE_STORY) {
            if (!$this->isDirector() && !$this->isSecretary())
                show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');
        } else {
            if (!$this->isDirector() && !$this->isSecretary() && !$this->isTranslate())
                show_error('Данный раздел доступен только для ролей "Директор", "Секретарь, и "Переводчик""', 403, 'Доступ запрещен');
        }
    }

    public function add() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            $sName = $this->input->post('sName');
            $fName = $this->input->post('fName');
            $mName = $this->input->post('mName');

            if (empty($sName))
                throw new Exception('Не указана фамилия клиента');

            if (empty($fName))
                throw new Exception('Не указано имя клиента');

            $id = $this->getCustomerModel()->customerInsert($sName, $fName, $mName);

            $this->json_response(["status" => 1, 'id' => $id]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function avatar($CustomerID) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        try {
            $data = $this->input->post('data');

            $image = $this->getImage();
            if (is_numeric($image['id'])) {
                $this->clearAvatar($CustomerID);
                $data = array('Avatar' => $image['id']);
            }

            if (empty($data))
                throw new Exception('Нет данных для сохранения');

            $this->getCustomerModel()->customerUpdate($CustomerID, $data);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Avatar']);

            $this->json_response(array('status' => 1, 'id' => $image['id'], 'FileName' => $image['id'].'.'.$image['ext']));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function update($id) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        try {
            $data = $this->input->post('data');

            if (empty($data))
                throw new Exception('Нет данных для сохранения');

            // Предворительная обработка данных - обнуление пустых полей
            $nullFields = array('WishesForManAgeMin', 'WishesForManAgeMax', 'WishesForManWeight', 'WishesForManHeight', 'WishesForManText', 'DateRegister');
            foreach ($nullFields as $field) {
                if (isset($data[$field]) && empty($data[$field])) {
                    $data[$field] = null;
                }
            }

            // Сбор полей для истории
            $fields = [];
            foreach ($data as $key => $value)
                $fields[] = $key;

            // Обновление профиля
            $this->getCustomerModel()->customerUpdate($id, $data);
            $this->getCustomerModel()->customerUpdateNote($id, $this->getUserID(), $fields);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function profile($id) {
        $data = array(
            'customer' => $this->getCustomerModel()->customerGet($id),
            'work_sites' => $this->getCustomerModel()->siteGetList($id),
            'video_sites' => $this->getCustomerModel()->videoSiteGetList($id),
            'forming' => $this->getReferencesModel()->getReference(REFERENCE_FORMING),
            'eye_color' => $this->getReferencesModel()->getReference(REFERENCE_EYE_COLOR),
            'language' => $this->getReferencesModel()->getReference(REFERENCE_LANGUAGE),
            'child_sex' => $this->getReferencesModel()->getReference(REFERENCE_CHILD_SEX),
            'marital' => $this->getReferencesModel()->getReference(REFERENCE_MARITAL),
            'body_build' => $this->getReferencesModel()->getReference(REFERENCE_BODY_BUILD),
            'sites' => $this->getSiteModel()->getRecords(),
            'mensList' => $this->getCustomerModel()->getCustomerMens($id),
        );

        // Установка прав доступа к договорам и паспорту только для assol
        if (!IS_LOVE_STORY) {
            // Редактирование прав доступа к договорам и паспорту
            $data['isEditDocumentAccess'] = $this->isDirector() || $this->isSecretary();

            // Доступ к документам для ролей "Директор" и "Секретарь" + для пользователей с назначенными правами
            if ($data['isEditDocumentAccess']) {
                $data['employees'] = $this->getEmployeeModel()->employeeGetFilterRoleList($id, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE]);
                $data['rights'] = $this->getCustomerModel()->rightsGetList($id);
                $data['isDocAccess'] = true;
            } else {
                $right = $this->getCustomerModel()->rightsGet($id, $this->getUserID());
                $data['isDocAccess'] = !empty($right);
                $data['employees'] = [];
                $data['rights'] = [];
            }
        } else {
            $data['isEditDocumentAccess'] = false; // Скрываем форму настройки прав доступа для LoveStory
            $data['isDocAccess'] = true; // Открываем полный доступ к документам для LoveStory
            $data['employees'] = [];
            $data['rights'] = [];
        }

        $this->viewHeader($data);
        $this->view('form/customers/profile');
        $this->viewFooter([
            'js_array' => [
                'public/js/assol.customer.card.js'
            ]
        ]);
    }

    public function rights($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $employees = $this->input->post('Employees');

            // Отключенных прав, не пришедших в списке
            $this->getCustomerModel()->rightsRemove($CustomerID, $employees);

            // Добавление необходимых прав
            foreach ($employees as $employee) {
                $this->getCustomerModel()->rightsInsert($CustomerID, $employee);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $isFull = $this->input->post('IsFull');

            if ($isFull) {
                $this->clearAvatar($CustomerID);
                $this->getCustomerModel()->customerDelete($CustomerID);
            } else {
                $this->getCustomerModel()->customerUpdate($CustomerID, array(
                    'DateRemove' => date('Y-m-d'), 'IsDeleted' => 1, 'WhoDeleted' => $this->getUserID()));
                $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['IsDeleted']);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    private function clearAvatar($EmployeeID) {
        $customer = $this->getCustomerModel()->customerGet($EmployeeID);

        if ($customer['Avatar'] > 0) {
            $this->getImageModel()->remove($customer['Avatar']);
        }
    }

    public function restore() {
        try {
            $this->assertUserRight();

            $id = $this->input->post('id');

            if (!isset($id))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->customerUpdate($id, array(
                'ReasonForDeleted' => null, 'DateRemove' => null, 'IsDeleted' => 0, 'WhoDeleted' =>  null));
            $this->getCustomerModel()->customerUpdateNote($id, $this->getUserID(), ['IsDeleted']);

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
