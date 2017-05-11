<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services_Delivery extends MY_Controller {

    public function data() {
        try {
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $employee = $this->input->post('employee');

            $isAdmin = IS_LOVE_STORY
                ? ($this->isDirector() || $this->isSecretary())
                : ($this->isDirector() || $this->isSecretary());

            $records = $this->getServiceModel()->deliveryGetList($isAdmin ? $employee : $this->getUserID(), $start, $end, $isAdmin);

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
                $site = $this->input->post('site');
                $userTranslate = $this->input->post('userTranslate');
                $men = $this->input->post('men');
                $girl = $this->input->post('girl');
                $delivery = $this->input->post('delivery');
                $gratitude = $this->input->post('gratitude');

                if (empty($date))
                    throw new RuntimeException("Не указана дата");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (empty($userTranslate))
                    throw new RuntimeException("Не указан переводчик");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($delivery))
                    throw new RuntimeException("Не указана доставка");

                if (empty($gratitude))
                    throw new RuntimeException("Не указана благодарность");

                $id = $this->getServiceModel()->deliveryInsert($this->getUserID(), $date, $girl, $men, $site, $userTranslate, $delivery, $gratitude);

                $this->json_response(array("status" => 1, 'id' => $id));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'translators' => $this->getEmployeeModel()->employeeTranslatorGetList(),
            'sites' => $this->getSiteModel()->getRecords()
        );

        // 2. Загрузка шаблона
        $this->load->view('form/services/add_delivery', $data);
    }

    public function edit($id) {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $date = $this->input->post('date');
                $site = $this->input->post('site');
                $userTranslate = $this->input->post('userTranslate');
                $men = $this->input->post('men');
                $girl = $this->input->post('girl');
                $delivery = $this->input->post('delivery');
                $gratitude = $this->input->post('gratitude');

                if (empty($date))
                    throw new RuntimeException("Не указана дата");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (empty($userTranslate))
                    throw new RuntimeException("Не указан переводчик");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($delivery))
                    throw new RuntimeException("Не указана доставка");

                if (empty($gratitude))
                    throw new RuntimeException("Не указана благодарность");

                $this->getServiceModel()->deliveryUpdate($id, $date, $girl, $men, $site, $userTranslate, $delivery, $gratitude);

                $this->json_response(array("status" => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'translators' => $this->getEmployeeModel()->employeeTranslatorGetList(),
            'sites' => $this->getSiteModel()->getRecords(),
            'record' => $this->getServiceModel()->deliveryGet($id)
        );

        // 2. Загрузка шаблона
        $this->load->view('form/services/edit_delivery', $data);
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

            $this->getServiceModel()->deliveryDone($id);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}
