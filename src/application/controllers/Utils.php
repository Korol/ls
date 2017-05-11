<?php
!defined('IS_PRODUCTION') OR exit('No direct script access allowed');
defined('BASEPATH') OR exit('No direct script access allowed');

class Utils extends MY_Controller {

    const SECURITY_KEY_TABLE_INIT = 'FC49C607-168B-4C0B-B683-91C61163BECC';
    const SECURITY_KEY_TABLE_DROP = '13E88CA1-55A3-4E11-B0D3-D821BE6F760D';

    private $models = array();

    function __construct() {
        parent::__construct(FALSE);

        $this->models = array(
            $this->getSiteModel(),
            $this->getImageModel(),
            $this->getEmployeeModel(),
            $this->getCustomerModel(),
            $this->getScheduleModel(),
            $this->getDocumentModel(),
            $this->getTrainingModel(),
            $this->getReferencesModel(),
            $this->getCalendarModel(),
            $this->getServiceModel(),
            $this->getReportModel(),
            $this->getTaskModel(),
            $this->getMessageModel(),
            $this->getNewsModel(),
            $this->getSettingModel()
        );
    }

    public function index() {
        $actions = [
            'database-init'  => 'FC49C607-168B-4C0B-B683-91C61163BECC',
            'database-drop'  => '13E88CA1-55A3-4E11-B0D3-D821BE6F760D',
            'create-admin'   => '32EE697C-2076-4B54-BB50-1D83E4C8B577',
            'reset-admin' => 'BB43DA67-A481-42BD-83B6-892E41BCACCC'
        ];

        $data = [];

        if (!empty($_POST)) {
            try {
                $action = $this->input->post('action');
                $params = $this->input->post('params');
                $key = $this->input->post('key');

                if (empty($action))
                    throw new Exception('Не указана операция!');

                if (!isset($actions[$action]))
                    throw new Exception('Не найдена указанная операция!');

                if (empty($key))
                    throw new Exception('Не указан секретный ключ!');

                if ($actions[$action] != $key)
                    throw new Exception('Указан некорректный ключ!');

                switch ($action) {
                    case 'database-init':
                        $data['message'] = $this->table_init();
                        break;
                    case 'database-drop':
                        $data['message'] = $this->table_drop();
                        break;
                    case 'create-admin':
                        $data['message'] = $this->create_admin();
                        break;
                    case 'reset-admin':
                        $data['message'] = $this->reset_admin($params);
                        break;
                }

            } catch (Exception $e) {
                $data['message'] = $e->getMessage();
            }
        }

        $this->load->view('form/utils', $data);
    }

//    public function agreement() {
//        $limit = 5;
//        $offset = 0;
//
//        $agreements = $this->getCustomerModel()->agreementList($limit, $offset);
//
//        while (!empty($agreements)) {
//            foreach($agreements as $agreement) {
//                file_put_contents('./files/customer/agreement/'.$agreement['ID'].'.'.$agreement['ext'], $agreement['File']);
//            }
//
//            $offset += $limit;
//            $agreements = $this->getCustomerModel()->agreementList($limit, $offset);
//        }
//
//        echo 'ok!';
//    }

    private function table_init(){
        foreach($this->models as $model)
            $model->initDataBase();
        return 'База успешно создана!';
    }

    private function table_drop(){
        foreach($this->models as $model)
            $model->dropTables();
        return 'База успешно удалена!';
    }

    private function create_admin() {
        $id = $this->getEmployeeModel()->employeeInsert('admin', 'admin', 'admin');
        $this->getEmployeeModel()->employeeUpdate($id, ['UserRole' => USER_ROLE_DIRECTOR]);
        $employee = $this->getEmployeeModel()->employeeGet($id);

        return 'Создан пользователь с ролью "Директор". Логин: '.$id.' | Пароль: '.$employee['Password'];
    }

    private function reset_admin($params) {
        $id = trim($params);

        if (!is_numeric($id))
            throw new Exception('В поле "Параметры" не указан ID пользователя!');

        $employee = $this->getEmployeeModel()->employeeGet($id);

        if (empty($employee))
            throw new Exception('В поле "Параметры" указан несуществующий ID пользователя!');

        $this->getEmployeeModel()->employeeUpdate($id, [
            'UserRole' => USER_ROLE_DIRECTOR,
            'Password' => $this->getEmployeeModel()->generatePassword()
        ]);

        $employee = $this->getEmployeeModel()->employeeGet($id);

        return 'Обновлен пользователь с ролью "Директор". Логин: '.$id.' | Пароль: '.$employee['Password'];
    }

}
