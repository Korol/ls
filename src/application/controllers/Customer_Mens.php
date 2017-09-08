<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Mens extends MY_Controller {

    /**
     * Сохраняем/обновляем информацию о мужчинах клиентки
     */
    public function save()
    {
        if(!empty($_POST)){
            $post = $this->input->post(null, true);
            $types = array('add', 'update');
            $cid = (!empty($post['CustomerID'])) ? $post['CustomerID'] : 0;
            if(!empty($post['CustomerID']) && in_array($post['type'], $types)){
                // загрузка фото
                if(!empty($_FILES['Photo']['tmp_name'])){
                    $photo = $this->uploadPhoto();
                }
                // новый мужчина
                if($post['type'] == 'add'){
                    $add = array(
                        'CustomerID' => $post['CustomerID'],
                        'SiteID' => (!empty($post['SiteID'])) ? $post['SiteID'] : 0,
                        'Name' => (!empty($post['Name'])) ? $post['Name'] : 'New man',
                        'Photo' => (!empty($photo)) ? $photo : '',
                        'Comment' => (!empty($post['Comment'])) ? $post['Comment'] : '',
                        'Age' => (!empty($post['Age'])) ? $post['Age'] : '',
                        'Nickname' => (!empty($post['Nickname'])) ? $post['Nickname'] : '',
                        'FromWhere' => (!empty($post['FromWhere'])) ? $post['FromWhere'] : '',
                        'IDonSite' => (!empty($post['IDonSite'])) ? $post['IDonSite'] : '',
                        'Added' => date('Y-m-d H:i:s'),
                        'EmployeeID' => $this->getUserID(),
                        'EmployeeName' => $this->getEmployeeName($this->getUserID()),
                        'Blacklist' => 0,
                    );
                    // добавляем
                    $this->getCustomerModel()->saveCustomerMen($add);
                    $this->getCustomerModel()->customerUpdateNote($post['CustomerID'], $this->getUserID(), array('AddMen'));
                } // обновляем существующего мужчину
                elseif(($post['type'] == 'update') && !empty($post['ID'])){
                    $men = $this->getCustomerModel()->getCustomerMen($post['ID']);
                    if(!empty($men)){
                        // удаляем старое фото - если есть и старое и новое
                        if(!empty($photo) && !empty($men['Photo'])){
                            $this->getImageModel()->remove($men['Photo']);
                        }
                        $update = array(
                            'SiteID' => (!empty($post['SiteID'])) ? $post['SiteID'] : 0,
                            'Name' => (!empty($post['Name'])) ? $post['Name'] : $men['Name'],
                            'Photo' => (!empty($photo)) ? $photo : $men['Photo'],
                            'Comment' => (!empty($post['Comment'])) ? $post['Comment'] : $men['Comment'],
                            'Blacklist' => (!empty($post['Blacklist'])) ? 1 : 0,
                            'Age' => (!empty($post['Age'])) ? $post['Age'] : '',
                            'Nickname' => (!empty($post['Nickname'])) ? $post['Nickname'] : '',
                            'FromWhere' => (!empty($post['FromWhere'])) ? $post['FromWhere'] : '',
                            'IDonSite' => (!empty($post['IDonSite'])) ? $post['IDonSite'] : '',
                            'EmployeeID' => (!empty($men['EmployeeID'])) ? $men['EmployeeID'] : $this->getUserID(),
                            'EmployeeName' => (!empty($men['EmployeeID'])) ? $this->getEmployeeName($men['EmployeeID']) : $this->getEmployeeName($this->getUserID()),
                        );
                        // обновляем
                        $this->getCustomerModel()->updateCustomerMen($post['ID'], $update);
                        $this->getCustomerModel()->customerUpdateNote($post['CustomerID'], $this->getUserID(), array('EditMen'));
                    }
                }
            }
        }
        if(!empty($cid))
            redirect(base_url('customer/' . $cid . '/profile#Mens'));
        else
            redirect(base_url('customers'));
    }

    /**
     * Типы файлов для загрузки
     * @return array
     */
    protected function getFileTypes() {
        return array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    }

    /**
     * Загрузка фото
     * @return string
     */
    public function uploadPhoto()
    {
        $file = $_FILES['Photo'];
        $ext = $this->assertFileType($file['tmp_name']);
        $file_id = $this->getImageModel()->imageInsert($this->getFileContent($file['tmp_name']), $ext);
        return (!empty($file_id)) ? $file_id . '.' . $ext : '';
    }

    /**
     * Удаление мужчины
     */
    public function remove()
    {
        $res = 0;
        $id = $this->input->post('ID', true);
        $CustomerID = $this->input->post('CustomerID', true);
        if(!empty($id)){
            $men = $this->getCustomerModel()->getCustomerMen($id);
            if(!empty($men)){
                // удаляем фото – если оно есть
                if(!empty($men['Photo'])){
                    $this->getImageModel()->remove($men['Photo']);
                }
                // удаляем запись из БД
                $res = $this->getCustomerModel()->removeCustomerMen($id);
                $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), array('RemoveMen'));
            }
        }
        echo $res;
    }

    /**
     * получаем список мужчин с учетом фильтров
     */
    public function filter()
    {
        $return = '';
        $CustomerID = $this->input->post('CustomerID', true);
        $SiteIDs = $this->input->post('SiteIDs', true);
        $Filters = $this->input->post('Filters', true);
        if(!empty($Filters) && is_array($Filters)){
            foreach ($Filters as $fk => $filter) {
                if($filter === ''){
                    unset($Filters[$fk]); // удаляем пустые значения из фильтра (только строки, для Blacklist оставляем 0 - Нет)
                }
            }
        }
        else{
            $Filters = array();
        }

        if(!empty($CustomerID)){

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

            // учитываем фильтрацию по сайтам
            $sites = (!empty($SiteIDs)) ? explode(',', $SiteIDs) : '';

            $return = $this->load->view('form/customers/profile_mens',
                array(
                    'customerID' => $CustomerID,
                    'isEditMens' => ($this->isDirector() || $this->isSecretary() || $this->isTranslate()),
                    'isDeleteMens' => ($this->isDirector() || $this->isSecretary()),
                    'mensList' => $this->getCustomerModel()->getCustomerMensBySites($CustomerID, $sites, $Filters),
                    'mensSitesList' => $this->getSitesForMens($CustomerID),
                    'sites' => $this->getSiteModel()->getRecords(),
                    'translators' => $this->getCMTranslators($CustomerID),
                ),
                true
            );
        }

        echo $return;
    }

    /**
     * список сайтов для добавления мужчин и отображения в таблице
     * @param $CustomerID
     * @return array
     */
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
     * получаем мужчину для редактирования в модальном окне
     */
    public function getman()
    {
        $ManID = $this->input->post('ManID', true);
        if(!empty($ManID)){
            $man = $this->getCustomerModel()->getCustomerMen($ManID);
            $this->json_response(array("status" => 1, 'man' => $man));
        }
        else{
            $this->json_response(array("status" => 0, 'error' => 'No Man ID!'));
        }
    }

    /**
     * получаем имя сотрудника
     * @param $EmployeeID
     * @return string
     */
    public function getEmployeeName($EmployeeID)
    {
        $name = '';
        $employee = $this->getEmployeeModel()->employeeGet($EmployeeID);
        if(!empty($employee)){
            $name .= $employee['SName'] . ' '
                . mb_substr($employee['FName'], 0, 1) . '. '
                . mb_substr($employee['MName'], 0, 1) . '.';
        }
        return $name;
    }

    /**
     * обновляем информацию о черном списке мужфин
     */
    public function blacklist()
    {
        $return = array('status' => 0);
        $MenID = $this->input->post('MenID', true);
        $Blacklist = (int)$this->input->post('Blacklist', true);
        if(!empty($MenID)){
            // обновляем
            $this->getCustomerModel()->updateCustomerMen($MenID, array('Blacklist' => $Blacklist));
            $return = array('status' => 1, 'id' => $MenID);
        }
        $this->json_response($return);
    }

    /**
     * получаем список переводчиков, для Директора и Секретаря, чтоб могли указать, кто добавил мужчину
     * @param $CustomerID
     */
    public function getCMTranslators($CustomerID)
    {
        // TODO: пока не делаю – если не потребуют
    }

}