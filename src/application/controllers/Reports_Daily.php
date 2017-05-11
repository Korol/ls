<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_Daily extends MY_Controller {

    public function meta() {
        try {
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $data = [
                'work_sites' => $this->getEmployeeModel()->siteGetList($employee),
                'customers' => $this->getEmployeeModel()->employeeCustomerGetList($employee)
            ];

            $this->json_response(array("status" => 1, 'records' => $data));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function data() {
        try {
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
            $day = $this->input->post('day');

            if (empty($day)) {
                $data = $this->getReportModel()->reportDailyGroupMonth($employee, $year, $month);
            } else {
                $date = date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
                $data = $this->getReportModel()->reportDaily($employee, $date);
            }

            $this->json_response(array("status" => 1, 'records' => $data));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save() {
        try {
            $dateRecord = $this->input->post('dateRecord');
            $idCross = $this->input->post('idCross');
            $mails = $this->input->post('mails');
            $chat = $this->input->post('chat');

            $record = $this->getReportModel()->reportDailyFind($dateRecord, $idCross);

            if (empty($record)) {
                $this->getReportModel()->reportDailyInsert($dateRecord, $idCross, $mails, $chat);
            } else {
                $this->getReportModel()->reportDailyUpdate($record['id'], $mails, $chat);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }
}
