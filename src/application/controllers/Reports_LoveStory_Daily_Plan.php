<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_LoveStory_Daily_Plan extends MY_Controller {


    public function data() {
        try {
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $data = $this->getReportModel()->reportLoveStoryMountPlan($employee, $year, $month);

            $this->json_response(array("status" => 1, 'records' => $data));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
            $idCross = $this->input->post('idCross');
            $mails = $this->input->post('mails');
            $chat = $this->input->post('chat');

            $record = $this->getReportModel()->reportLoveStoryMountPlanFind($idCross, $year, $month);

            if (empty($record)) {
                $this->getReportModel()->reportLoveStoryMountPlanInsert($year, $month, $idCross, $mails, $chat);
            } else {
                $this->getReportModel()->reportLoveStoryMountPlanUpdate($record['id'], $mails, $chat);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }
}
