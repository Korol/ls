<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule extends MY_Controller {

    public function index() {
        $data = array(
            'schedule' => $this->getScheduleModel()->scheduleGet($this->getUserID()),
            'schedule_list' => $this->getScheduleModel()->scheduleGetList($this->getUserID(), $this->getUserRole())
        );

        $this->viewHeader($data);
        $this->view('form/schedule');
        $this->viewFooter();
    }

    public function data() {
        try {

            $this->json_response([
                "status" => 1,
                'schedule' => $this->getScheduleModel()->scheduleGet($this->getUserID()),
                'schedules' => $this->getScheduleModel()->scheduleGetList($this->getUserID(), $this->getUserRole())
            ]);
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save() {
        try {
            $employee = $this->input->post('employee');
            $data = $this->input->post('data');

            $employeeID = empty($employee) ? $this->getUserID() : $employee;

            if (empty($data))
                throw new RuntimeException("Не указан обязательный параметр");

            $schedule = $this->getScheduleModel()->scheduleGet($employeeID);

            if ($schedule) {
                $this->getScheduleModel()->scheduleUpdate($schedule['ID'], $data);
            } else {
                $this->getScheduleModel()->scheduleInsert($employeeID, $data);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }
}
