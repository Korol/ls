<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services_Western extends MY_Controller {

    public function data() {
        try {
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $employee = $this->input->post('employee');

            $isAdmin = IS_LOVE_STORY
                ? ($this->isDirector() || $this->isSecretary())
                : $this->isDirector();

            $records = $this->getServiceModel()->westernGetList($isAdmin ? $employee : $this->getUserID(), $start, $end, $isAdmin);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function add() {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $date = $this->input->post('date');
                $girl = $this->input->post('girl');
                $men = $this->input->post('men');
                $site = $this->input->post('site');
                $sum = $this->input->post('sum');
                $code = $this->input->post('code');
                $isSend = $this->input->post('isSend');
                $isPer = $this->input->post('isPer');

                if (empty($date))
                    throw new RuntimeException("Не указана дата");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (empty($sum))
                    throw new RuntimeException("Не указана сумма");

                if (empty($code))
                    throw new RuntimeException("Не указан код");

                $id = $this->getServiceModel()->westernInsert($this->getUserID(), $date, $girl, $men, $site, $sum, $code, $isSend, $isPer);

                $this->json_response(array("status" => 1, 'id' => $id));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'sites' => $this->getSiteModel()->getRecords()
        );

        // 2. Загрузка шаблона
        $this->load->view('form/services/add_western', $data);
    }

    public function edit($id) {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $date = $this->input->post('date');
                $girl = $this->input->post('girl');
                $men = $this->input->post('men');
                $site = $this->input->post('site');
                $sum = $this->input->post('sum');
                $code = $this->input->post('code');
                $isSend = $this->input->post('isSend');
                $isPer = $this->input->post('isPer');

                if (empty($date))
                    throw new RuntimeException("Не указана дата");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (empty($sum))
                    throw new RuntimeException("Не указана сумма");

                if (empty($code))
                    throw new RuntimeException("Не указан код");

                $this->getServiceModel()->westernUpdate($id, $date, $girl, $men, $site, $sum, $code, $isSend, $isPer);

                $this->json_response(array("status" => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'sites' => $this->getSiteModel()->getRecords(),
            'record' => $this->getServiceModel()->westernGet($id)
        );

        // 2. Загрузка шаблона
        $this->load->view('form/services/edit_western', $data);
    }

    public function done() {
        $isAdmin = IS_LOVE_STORY
            ? ($this->isDirector() || $this->isSecretary())
            : ($this->isDirector() || $this->isSecretary());

        if (!$isAdmin)
            show_error('Данный раздел не доступен для текущего пользователя', 403, 'Доступ запрещен');

        try {
            $id = $this->input->post('id');

            if (empty($id))
                throw new Exception('Нет данных для сохранения');

            $this->getServiceModel()->westernDone($id);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function send() {
        $isAdmin = IS_LOVE_STORY
            ? ($this->isDirector() || $this->isSecretary())
            : ($this->isDirector() || $this->isSecretary());

        if (!$isAdmin)
            show_error('Данный раздел не доступен для текущего пользователя', 403, 'Доступ запрещен');

        try {
            $id = $this->input->post('id');
            $isSend = $this->input->post('isSend');

            if (empty($id) || !is_numeric($isSend))
                throw new Exception('Нет данных для сохранения');

            $this->getServiceModel()->westernSend($id, $isSend);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function per() {
        $isAdmin = IS_LOVE_STORY
            ? ($this->isDirector() || $this->isSecretary())
            : ($this->isDirector() || $this->isSecretary());

        if (!$isAdmin)
            show_error('Данный раздел не доступен для текущего пользователя', 403, 'Доступ запрещен');

        try {
            $id = $this->input->post('id');
            $isPer = $this->input->post('isPer');

            if (empty($id) || !is_numeric($isPer))
                throw new Exception('Нет данных для сохранения');

            $this->getServiceModel()->westernPer($id, $isPer);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}
