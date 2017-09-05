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
//            'mensList' => $this->getCustomerModel()->getCustomerMens($id),
//            'mensSitesList' => $this->getSitesForMens($id),
            'employee_sites' => $this->getEmployeeSites(),
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
            // удаляем клиентку со всех её сайтов
            $this->removeFromSites($CustomerID);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /**
     * удаление клиентки со всех её сайтов
     * @param $CustomerID
     */
    public function removeFromSites($CustomerID)
    {
        $sites = $this->getCustomerModel()->siteGetList($CustomerID); // список её сайтов
        if(!empty($sites)){
            foreach ($sites as $site) {
                $this->getCustomerModel()->siteDelete($site['ID']); // ставим флаг удаления связи клиентки с сайтом
            }
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

            // восстанавливаем связи с сайтами
            $this->restoreToSites($id);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /**
     * восстанавливаем связи клиентки с её сайтами
     * @param $CustomerID
     */
    public function restoreToSites($CustomerID)
    {
        $sites = $this->getCustomerModel()->siteGetListAll($CustomerID); // список сайтов, с которыми связана клиентка
        if(!empty($sites)){
            foreach ($sites as $site) {
                $this->getCustomerModel()->siteUpdate($site['ID'], array('IsDeleted' => 0)); // снимаем флаг удаления связи клиентки с сайтом
            }
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

    public function getSitesForMens($CustomerID)
    {
        // sites
        $all_sites = $this->getSiteModel()->getRecords(); // все сайты проекта
        $customer_sites = $this->getCustomerModel()->siteGetList($CustomerID); // сайты, с которыми связана Клиентка
        $sites = array();
        if(!empty($all_sites) && !empty($customer_sites)){
            $sites_all = toolIndexArrayBy($all_sites, 'ID'); // индексируем по ID
            foreach ($customer_sites as $c_site) {
                if(!empty($sites_all[$c_site['SiteID']])){
                    $sites[$c_site['SiteID']] = array('ID' => $c_site['SiteID'], 'Name' => $sites_all[$c_site['SiteID']]['Name']);
                }
            }
        }
        // для Переводчиков
        if($this->role['isTranslate']){
            // фильтруем сайты Клиентки, оставляя только те, с которыми связан и Переводчик, и Клиентка
            $cs_ids = get_keys_array($customer_sites, 'SiteID'); // ID сайтов Клиентки
            $us_ids = get_keys_array($this->getEmployeeModel()->siteGetList($this->getUserID()), 'SiteID'); // ID сайтов Переводчика
            $intersect_ids = array_intersect($cs_ids, $us_ids);
            foreach($sites as $i_key => $i_site){
                if(!in_array($i_key, $intersect_ids)){
                    unset($sites[$i_key]); // удаляем сайт, не связанный с Переводчиком
                }
            }
        }
        return $sites;
    }

    /**
     * аоиск доставок по фамилии клиентки
     */
    public function getdelivery()
    {
        $return = '';
        $CustomerID = $this->input->post('CustomerID', true);
//        $SiteID = (int)$this->input->post('SiteID', true);
        $SiteIDs = $this->input->post('SiteIDs', true); // фильтр по нескольким сайтам
//        $CustomerID = 92;$SiteID = 0; // test
//        $CustomerID = 3;$SiteIDs = ''; // test

        // для первичного вывода и неактивного фильтра по сайтам
        // выводим для всех, кроме Директора и Секретаря, только по тем сайтам, с которыми они связаны
        if(empty($SiteIDs) && empty(($this->isDirector() || $this->isSecretary()))){
            $employee_sites = $this->getEmployeeModel()->siteGetList($this->getUserID()); // сайты, с которыми связан сотрудник
            if(!empty($employee_sites)){
                $st = array();
                foreach ($employee_sites as $employee_site) {
                    $st[] = $employee_site['SiteID'];
                }
                $SiteIDs = implode(',', $st); // строка с ID сайтов, с которыми связан сотрудник, like: 1,2,3,4,5
            }
            else{
                $SiteIDs = '654321'; // рыба, чтоб не срабатывала empty()
            }
        }

        if(!empty($CustomerID)){
            $customer = $this->getCustomerModel()->customerGet($CustomerID);
            if(!empty($customer)){
                $customerSNameEx = explode(' ', trim($customer['SName'])); // фамилия может быть из 2-х слов: Бабич Babich
                $customerSNameEx[1] = (!empty($customerSNameEx[1])) ? $customerSNameEx[1] : '';
                // ищем по всем частям фамилии + ID сайта
//                $records = $this->getServiceModel()->findDeliveryBySName($customerSNameEx[0], $customerSNameEx[1], $SiteID);
                // учитываем фильтрацию по сайтам
                $sites = (!empty($SiteIDs)) ? explode(',', $SiteIDs) : '';
                $records = $this->getServiceModel()->findDeliveryBySNameAndSites($customerSNameEx[0], $customerSNameEx[1], $sites);
                if(!empty($records)){
                    foreach ($records as $record) {
                        $countImages = $this->getServiceModel()->deliveryImageGetCount($record['ID']);
                        $modalUrl = base_url('services/delivery') . '/' . $record['ID'] . '/photos';
                        $return .= '<tr>';
                        $return .= '<td>' . trim($record['ESName']) . ' ' . mb_substr(trim($record['EFName']), 0, 1) . '. ' . mb_substr(trim($record['EFName']), 0, 1) . '.</td>';
                        $return .= '<td>' . date('d.m.Y', strtotime($record['Date'])) . '</td>';
                        $return .= '<td><span class="site-name">' . $record['SiteName'] . '</span></td>';
                        $return .= '<td>' . trim($record['E2SName']) . ' ' . mb_substr(trim($record['E2FName']), 0, 1) . '. ' . mb_substr(trim($record['E2FName']), 0, 1) . '.</td>';
                        $return .= '<td>' . $record['Men'] . '</td>';
                        $return .= '<td>' . $record['Girl'] . '</td>';
                        $return .= '<td>' . $record['Delivery'] . '</td>';
                        $return .= '<td style="padding-bottom: 10px;">' . $record['Gratitude'] . '</td>';
                        $return .= '<td><div class="open-delivery-modal"><a class="btn btn-default delivery-photo-modal" data-delid="' . $record['ID'] . '" data-url="' . $modalUrl . '">
                                        <span class="glyphicon glyphicon-' . (($countImages > 0) ? 'folder-open' : 'plus') . '" id="gl_icon_' . $record['ID'] . '" aria-hidden="true"></span>
                                    </a></div></td>';
                        $return .= '</tr>';
                    }
                }
            }
        }
        echo $return;
    }

    public function cntimages()
    {
        $DeliveryID = (int)$this->input->post('DeliveryID', true);
        $return = array('delivery' => $DeliveryID);
        if(!empty($DeliveryID)){
            $return['cnt'] = $this->getServiceModel()->deliveryImageGetCount($DeliveryID);
        }
        echo json_encode($return);
    }

    /**
     * список сайтов, доступных для сотрудника (используется в фильтрах)
     * Директор и Секретарь видят все сайты
     * остальные – видят только связанные с ними сайты
     */
    public function getEmployeeSites()
    {
        if($this->isDirector() || $this->isSecretary()){
            // все сайты
            $sites = $this->getSiteModel()->getRecords();
        }
        else{
            // только связанные сайты
            $sites = $this->getEmployeeModel()->siteGetListWithInfo($this->getUserID());
        }
        return $sites;
    }
}
