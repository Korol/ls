<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services_Meeting extends MY_Controller {

    public function data() {
        try {
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $employee = $this->input->post('employee');

            $isAdmin = IS_LOVE_STORY
                ? ($this->isDirector() || $this->isSecretary())
                : $this->isDirector();

            $records = $this->getServiceModel()->meetingGetList($isAdmin ? $employee : $this->getUserID(), $start, $end, $isAdmin);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function add() {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $dateIn = $this->input->post('dateIn');
                $dateOut = $this->input->post('dateOut');
                $girl = $this->input->post('girl');
                $men = $this->input->post('men');
                $site = $this->input->post('site');
                $userTranslate = $this->input->post('userTranslate');
                $userTranslateOrganizer = $this->input->post('userTranslateOrganizer');
                $userTranslateDuring = $this->input->post('userTranslateDuring');
                $city = $this->input->post('city');
                $transfer = $this->input->post('transfer');
                $housing = $this->input->post('housing');
                $translate = $this->input->post('translate');

                if (empty($dateIn))
                    throw new RuntimeException("Не указана дата приезда");

                if (empty($dateOut))
                    throw new RuntimeException("Не указана дата отъезда");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (!IS_LOVE_STORY && empty($userTranslate))
                    throw new RuntimeException("Не указан переводчик");

                if (empty($city))
                    throw new RuntimeException("Не указан город");

                if (empty($transfer))
                    throw new RuntimeException("Не указан трансфер");

                if (empty($housing))
                    throw new RuntimeException("Не указано жилье");

                if (empty($translate))
                    throw new RuntimeException("Не указан перевод");

                $id = $this->getServiceModel()->meetingInsert($this->getUserID(), $dateIn, $dateOut, $girl, $men, $site,
                    $userTranslate, $city, $transfer, $housing, $translate, $userTranslateOrganizer, $userTranslateDuring);

                $this->json_response(array("status" => 1, 'id' => $id));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'sites' => $this->getSiteModel()->getRecords()
        );

        if (IS_LOVE_STORY) {
            $data['translators'] = $this->getEmployeeModel()->employeeTranslatorGetList();
            $data['employee'] = $this->getEmployeeModel()->employeeGet($this->getUserID());
        }

        // 2. Загрузка шаблона
        $this->load->view('form/services/add_meeting', $data);
    }

    public function edit($id) {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $dateIn = $this->input->post('dateIn');
                $dateOut = $this->input->post('dateOut');
                $girl = $this->input->post('girl');
                $men = $this->input->post('men');
                $site = $this->input->post('site');
                $userTranslate = $this->input->post('userTranslate');
                $userTranslateOrganizer = $this->input->post('userTranslateOrganizer');
                $userTranslateDuring = $this->input->post('userTranslateDuring');
                $city = $this->input->post('city');
                $transfer = $this->input->post('transfer');
                $housing = $this->input->post('housing');
                $translate = $this->input->post('translate');

                if (empty($dateIn))
                    throw new RuntimeException("Не указана дата приезда");

                if (empty($dateOut))
                    throw new RuntimeException("Не указана дата отъезда");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (!IS_LOVE_STORY && empty($userTranslate))
                    throw new RuntimeException("Не указан переводчик");

                if (empty($city))
                    throw new RuntimeException("Не указан город");

                if (empty($transfer))
                    throw new RuntimeException("Не указан трансфер");

                if (empty($housing))
                    throw new RuntimeException("Не указано жилье");

                if (empty($translate))
                    throw new RuntimeException("Не указан перевод");

                $this->getServiceModel()->meetingUpdate($id, $dateIn, $dateOut, $girl, $men, $site, $userTranslate, $city,
                    $transfer, $housing, $translate, $userTranslateOrganizer, $userTranslateDuring);

                $this->json_response(array("status" => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'sites' => $this->getSiteModel()->getRecords(),
            'record' => $this->getServiceModel()->meetingGet($id)
        );

        if (IS_LOVE_STORY) {
            $data['translators'] = $this->getEmployeeModel()->employeeTranslatorGetList();
            $data['employee'] = $this->getEmployeeModel()->employeeGet($data['record']['EmployeeID']);
        }

        // 2. Загрузка шаблона
        $this->load->view('form/services/edit_meeting', $data);
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

            $this->getServiceModel()->meetingDone($id);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}
