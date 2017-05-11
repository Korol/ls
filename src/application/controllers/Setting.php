<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends MY_Controller {

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'])
            show_error('Данный раздел доступен только для роли "Директор"', 403, 'Доступ запрещен');
    }

    public function index() {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Загрузка шаблона
        $data = ['email' => $this->getSettingModel()->get('ReportEmail')];
        $this->viewHeader($data);
        $this->view('form/setting');
        $this->viewFooter();
    }

    public function save() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            // 2. Обработка данных формы
            if (!empty($_POST)) {
                $this->getSettingModel()->save('ReportEmail', $this->input->post('ReportEmail'));
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}
