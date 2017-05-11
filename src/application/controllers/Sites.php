<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sites extends MY_Controller {

    /**
     * Запрос данных для вывода списка в таблицу
     */
    public function data() {
        $this->json_response(array('rows' => $this->getSiteModel()->getRecords()));
    }

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

        $data = array(
            'isTable' => true,
            'records' => $this->getSiteModel()->getRecords()
        );

        $this->viewHeader($data);
        $this->view('form/sites/index');
        $this->viewFooter();
    }

    public function add() {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $name = $this->input->post('name');
                $domen = $this->input->post('domen');
                $note = $this->input->post('note');
                $isDealer = $this->input->post('IsDealer');

                if (empty($name))
                    throw new Exception('Не указано название сайта');

                if (empty($domen))
                    throw new Exception('Не указан домен сайта');

                $this->getSiteModel()->add($name, $domen, $note, $isDealer);

                $this->json_response(array('status' => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        // 3. Загрузка шаблона
        $this->load->view('form/sites/add');
    }

    public function edit($id) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $siteID = $this->input->post('siteID');
                $name = $this->input->post('name');
                $domen = $this->input->post('domen');
                $note = $this->input->post('note');
                $isDealer = $this->input->post('IsDealer');

                if (empty($name))
                    throw new Exception('Не указано название сайта');

                if (empty($domen))
                    throw new Exception('Не указан домен сайта');

                $this->getSiteModel()->update($siteID, $name, $domen, $note, $isDealer);

                $this->json_response(array('status' => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        // 3. Загрузка шаблона
        $data = array('id' => $id, 'record' => $this->getSiteModel()->getRecord($id));
        $this->load->view('form/sites/edit', $data);
    }

    public function remove($id) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $siteID = $this->input->post('siteID');
                $this->getSiteModel()->remove($siteID);

                $this->json_response(array('status' => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        // 3. Загрузка шаблона
        $data = array('id' => $id, 'record' => $this->getSiteModel()->getRecord($id));
        $this->load->view('form/sites/remove', $data);
    }
}
