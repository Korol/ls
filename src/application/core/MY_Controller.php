<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function toClientDate($date) {
    if (empty($date))
        return null;

    return date_format(date_create($date), 'd.m.Y');
}

function toClientDateTime($date) {
    if (empty($date))
        return null;

    return date_format(date_create($date), 'd.m.Y H:i:s');
}

class MY_Controller extends CI_Controller {

    var $user;
    var $role;

    function __construct($isInit = true) {
        parent::__construct();

        if (!$isInit) return;

        $this->load->helper('url');
        $this->load->library('session');

        // Авторизация для тестов
        $headers = $this->input->request_headers();
        if (isset($headers['Test-Api-Header'], $headers['Test-Role-Header'])) {
            $userRole = $headers['Test-Role-Header'];

            $this->role = array(
                'isDirector' => $userRole == USER_ROLE_DIRECTOR,
                'isSecretary' => $userRole == USER_ROLE_SECRETARY,
                'isTranslate' => $userRole == USER_ROLE_TRANSLATE,
                'isEmployee' => $userRole == USER_ROLE_EMPLOYEE
            );

            $this->userId = 0;

            return;
        }

        $logged_system = $this->session->userdata('logged_system');

        if ($logged_system == FALSE) {
            redirect(base_url('login'), 'refresh');
        } else {
            $this->user = $this->session->userdata('user');
            $this->checkBlackList();
            $this->user['role_description'] = array(
                "10001" => "Директор",
                "10002" => "Секретарь",
                "10003" => "Переводчик",
                "10004" => "Сотрудник"
            );

            if (!defined('IS_LOVE_STORY'))
                define('IS_LOVE_STORY', $this->session->userdata('IS_LOVE_STORY') === TRUE);

            $this->role = array(
                'isDirector' => $this->user['role'] == USER_ROLE_DIRECTOR,
                'isSecretary' => $this->user['role'] == USER_ROLE_SECRETARY,
                'isTranslate' => $this->user['role'] == USER_ROLE_TRANSLATE,
                'isEmployee' => $this->user['role'] == USER_ROLE_EMPLOYEE
            );
        }
    }

    public function viewHeader($data = array()) {
        $this->load->view('header', array_merge([
            'role' => $this->role,
            'user' => $this->user,
            'menu' => $this->getMenu(),
            'title' => TITLE_TEXT
        ], $data));
    }

    public function view($view, $data = array()) {
        if (IS_LOVE_STORY && $this->isBlocked()) {
            switch ($view) {
                case 'form/documents/index':    // Документация
                case 'form/reports':            // Отчеты
                    $this->load->view($view, $data);
                    break;
                default:
                    $this->load->view('form/blocked');
            }
        } else {
            $this->load->view($view, $data);
        }
    }

    public function viewFooter($data = array()) {
        $this->load->view('footer', $data);
    }

    public function getUserID() {
        return $this->user['ID'];
    }

    public function getUserRole() {
        return $this->user['role'];
    }

    public function isDirector() {
        return $this->role['isDirector'];
    }

    public function isSecretary() {
        return $this->role['isSecretary'];
    }

    public function isTranslate() {
        return $this->role['isTranslate'];
    }

    public function isEmployee() {
        return $this->role['isEmployee'];
    }

    public function isAssol() {
        return !IS_LOVE_STORY;
    }

    public function isLoveStory() {
        return IS_LOVE_STORY;
    }

    public function json_response($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data))
            ->_display();
        exit;
    }

    public function file_response($content, $ext, $filename) {
        $header = 'Content-Disposition: inline; filename="'.$filename.'";';
        $this->output->headers[] = array($header, TRUE);

        $this->output
            ->set_content_type($ext)
            ->set_output($content)
            ->_display();
        exit;
    }

    /**
     * Получить содержимое файла
     *
     * @param $path string путь до файла
     *
     * @return string
     */
    protected function getFileContent($path) {
        $fp      = fopen($path, 'r');
        $content = fread($fp, filesize($path));
        fclose($fp);

        return $content;
    }

    /**
     * Проверка формата файла
     *
     * @param $path string путь до файла
     *
     * @return string расширение файла
     *
     * @throws RuntimeException
     */
    protected function assertFileType($path) {
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);

        if (false === $ext = array_search(
                $fileInfo->file($path),
                $this->getFileTypes(),
                true
            )) {

            $mimes = '';
            foreach($this->getFileTypes() as $keyMime => $valueMime) {
                $mimes .= $keyMime . ' | ';
            }

            throw new RuntimeException('Неверный формат: ' . $fileInfo->file($path) . '. Для загрузки доступны следующие форматы: | '.$mimes);
        }

        return $ext;
    }

    protected function getFileTypes() {
        return get_mimes();
    }

    /**
     * @return News_model
     */
    protected function getNewsModel() {
        if (!isset($this->news_model))
            $this->load->model('news_model');

        return $this->news_model;
    }

    /**
     * @return Site_model
     */
    protected function getSiteModel() {
        if (!isset($this->site_model))
            $this->load->model('site_model');

        return $this->site_model;
    }

    /**
     * @return Image_model
     */
    protected function getImageModel() {
        if (!isset($this->image_model))
            $this->load->model('image_model');

        return $this->image_model;
    }

    /**
     * @return Customer_model
     */
    protected function getCustomerModel() {
        if (!isset($this->customer_model))
            $this->load->model('customer_model');

        return $this->customer_model;
    }

    /**
     * @return Employee_model
     */
    protected function getEmployeeModel() {
        if (!isset($this->employee_model))
            $this->load->model('employee_model');

        return $this->employee_model;
    }

    /**
     * @return References_model
     */
    protected function getReferencesModel() {
        if (!isset($this->references_model))
            $this->load->model('references_model');

        return $this->references_model;
    }

    /**
     * @return Report_model
     */
    protected function getReportModel() {
        if (!isset($this->report_model))
            $this->load->model('report_model');

        return $this->report_model;
    }

    /**
     * @return Schedule_model
     */
    protected function getScheduleModel() {
        if (!isset($this->schedule_model))
            $this->load->model('schedule_model');

        return $this->schedule_model;
    }

    /**
     * @return Document_model
     */
    protected function getDocumentModel() {
        if (!isset($this->document_model))
            $this->load->model('document_model');

        return $this->document_model;
    }

    /**
     * @return Training_model
     */
    protected function getTrainingModel() {
        if (!isset($this->training_model))
            $this->load->model('training_model');

        return $this->training_model;
    }

    /**
     * @return Task_model
     */
    protected function getTaskModel() {
        if (!isset($this->task_model))
            $this->load->model('task_model');

        return $this->task_model;
    }

    /**
     * @return Calendar_model
     */
    protected function getCalendarModel() {
        if (!isset($this->calendar_model))
            $this->load->model('calendar_model');

        return $this->calendar_model;
    }

    /**
     * @return Service_model
     */
    protected function getServiceModel() {
        if (!isset($this->service_model))
            $this->load->model('service_model');

        return $this->service_model;
    }

    /**
     * @return Message_model
     */
    protected function getMessageModel() {
        if (!isset($this->message_model))
            $this->load->model('message_model');

        return $this->message_model;
    }

    /**
     * @return Setting_model
     */
    protected function getSettingModel() {
        if (!isset($this->setting_model))
            $this->load->model('setting_model');

        return $this->setting_model;
    }

    /**
     * Статус блокировки для сайта LoveStory
     *
     *@return true если отчет не заполнялся несколько дней и текущий пользователь переводчик
     */
    private function isBlocked() {
        if ($this->isTranslate()) {
            $isExistMountReport = $this->getReportModel()->isExistMountReport($this->getUserID(), 4); // Поиск отчетов за 4 дня

            $sites = $this->getEmployeeModel()->siteGetList($this->getUserID());
            $isExistSite = !empty($sites); // Проверяем что у сотрудника есть сайты

            return !$isExistMountReport && $isExistSite;
        } else {
            return false;
        }
    }

    private function getMenu() {
        // 1. Общии пункты меню
        $menu = [
            ['controller'=>'news',      'description'=>'Новости'],
            ['controller'=>'calendar',  'description'=>'Календарь'],
            ['controller'=>'customer',  'description'=> IS_LOVE_STORY ? 'Клиентки' : 'Клиенты']
        ];

        // 2. Для сайта Assol пункт меню только для ролей "Директор" и "Секретарь"
        if (IS_LOVE_STORY) {
            $menu[] = ['controller'=>'employee',  'description'=>'Сотрудники'];
        } else {
            if ($this->isDirector() || $this->isSecretary())
                $menu[] = ['controller'=>'employee',  'description'=>'Сотрудники'];
        }

        // 3. Общии пункты меню
        $menu[] = ['controller'=>'tasks',     'description'=>'Задачи'];
        $menu[] = ['controller'=>'messages',  'description'=>'Сообщения'];

        // 4. Пункт меню для всех ролей кроме "Сотрудник"
        if (!$this->isEmployee())
            $menu[] = ['controller'=>'services', 'description'=>'Услуги'];

        // 5. Пункт меню для ролей "Директор", "Секретарь", "Переводчик"
        if ($this->isDirector() || $this->isSecretary() || $this->isTranslate())
            $menu[] = ['controller'=>'reports', 'description'=>'Отчеты'];

        // 6. Общии пункты меню
        $menu[] = ['controller'=>'documents', 'description'=>'Документация'];
        $menu[] = ['controller'=>'training', 'description'=>'Обучение'];

        // 7. Пункт меню для роли "Директор"
        if ($this->isDirector())
            $menu[] = ['controller'=>'sites', 'description'=>'Сайты'];


        // 8. Для сайта LoveStory пункт меню только для ролей "Директор" и "Секретарь"
        if (IS_LOVE_STORY) {
            if ($this->isDirector() || $this->isSecretary())
                $menu[] = array('controller'=>'schedule', 'description'=>'График работы');
        } else {
            $menu[] = array('controller'=>'schedule', 'description'=>'График работы');
        }

        // 9. Пункт меню для роли "Директор"
        if ($this->isDirector())
            $menu[] = ['controller'=>'setting', 'description'=>'Настройки'];

        return $menu;
    }

    /**
     * Проверка сотрудника по черному списку (заблокированные и удалённые)
     * если в списке – разлогиниваем
     *
     * @return mixed
     */
    public function checkBlackList()
    {
        $blackList = $this->getEmployeeModel()->getBlackList();
        if(!empty($blackList) && in_array($this->user['ID'], $blackList)){
            redirect(base_url('logout'));
        }
    }

}