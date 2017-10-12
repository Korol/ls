<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Cards - работа с кредитками
 */
class Cards extends MY_Controller
{
    public $currencies = array(
        'USD' => 'USD',
        'EUR' => 'EUR',
        'UAH' => 'UAH',
    );

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
        $this->viewHeader();
        $this->view('form/cards/index', $data);
        $this->viewFooter();
    }

    public function save()
    {
        try {
            $isNew = $this->input->post('isNew', true);
            $Name = $this->input->post('Name', true);
            $Number = $this->input->post('Number', true);
            $Currency = $this->input->post('Currency', true);

            if (empty($Name))
                throw new RuntimeException("Не указано название");
            if (empty($Number))
                throw new RuntimeException("Не указан номер");
            if (empty($Currency))
                throw new RuntimeException("Не указана валюта");

            $data = array(
                'Name' => $Name,
                'Number' => $Number,
                'Currency' => $Currency,
            );

            if(!empty($isNew)){
                $data['Author'] = $this->getUserID();
                $data['Created'] = date('Y-m-d H:i:s');
                $data['Active'] = 1;
                $data['Deleted'] = 0;
                $result = $this->getCardModel()->insertCard($data);
            }
            else{
                $ID = $this->input->post('ID', true);
                $data['Active'] = $this->input->post('Active', true);
                if (empty($ID))
                    throw new RuntimeException("Не указана карта для редактирования");

                $result = $this->getCardModel()->updateCard($data, $ID);
            }

            $this->json_response(array("status" => $result));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function data() {
        try {
            $records = $this->getCardModel()->getCardsList(false);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function get()
    {
        try {
            $ID = $this->input->post('ID', true);
            if (empty($ID))
                throw new RuntimeException("Не указана карта для редактирования");

            $card = $this->getCardModel()->getCard($ID, false);

            $this->json_response(array("status" => 1, 'card' => $card));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove()
    {
        try {
            $ID = $this->input->post('ID', true);
            if (empty($ID))
                throw new RuntimeException("Не указана карта для удаления");

            $status = $this->getCardModel()->deleteCard($ID);

            $this->json_response(array("status" => $status));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}