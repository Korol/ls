<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_LoveStory_Daily extends MY_Controller {

    public function meta() {
        try {
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $data = [
                'work_sites' => $this->getEmployeeModel()->siteGetList($employee)
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

            $data = $this->getReportModel()->reportLoveStoryMount($employee, $year, $month);

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

            $record = $this->getReportModel()->reportLoveStoryMountFind($dateRecord, $idCross);

            if (empty($record)) {
                $this->getReportModel()->reportLoveStoryMountInsert($dateRecord, $idCross, $mails, $chat);
            } else {
                $this->getReportModel()->reportLoveStoryMountUpdate($record['id'], $mails, $chat);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }
}
