<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * работа с Финансовой таблицей
 */
// TODO: структура БД для оптимального хранения и получения информации о финансовых операциях
// TODO: получение и сохранение новых операций (из модального окна)
// TODO: сбор и передача суммарной статистики по всем типам операций за период (в таблицу)
// TODO: сбор и передача суммарной статистики по одному типу операций за период (в модальное окно)

class Finance extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->assertUserRight();
    }

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'])
            show_error('Данный раздел доступен только для роли "Директор"', 403, 'Доступ запрещен');
    }

    public function index()
    {
        $data['currencies'] = $this->currencies;
        $data['sites'] = $this->getSiteModel()->getRecords();
        $data['cards'] = $this->getCardModel()->getCardsList();
        $data['employees'] = $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole());
        $this->viewHeader(array(), 'header_wide');
        $this->view('form/finance/index', $data);
        $this->viewFooter();
    }

    public function data() {
        try {
            $from = $this->input->post('from', true);
            $to = $this->input->post('to', true);
            if (empty($from) || empty($to))
                throw new RuntimeException("Не указана дата для запроса");

            $records = $this->proceedData();

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function proceedData()
    {
        return array();
    }
}