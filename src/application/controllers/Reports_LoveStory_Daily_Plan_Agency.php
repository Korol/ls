<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_LoveStory_Daily_Plan_Agency extends MY_Controller {

    public function save() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
            $idSite = $this->input->post('idSite');
            $idEmployee = $this->input->post('idEmployee');
            $value = $this->input->post('value');

            $record = $this->getReportModel()->reportLoveStoryMountPlanAgencyFind($idSite, $idEmployee, $year, $month);

            if (empty($record)) {
                $this->getReportModel()->reportLoveStoryMountPlanAgencyInsert($year, $month, $idSite, $idEmployee, $value);
            } else {
                $this->getReportModel()->reportLoveStoryMountPlanAgencyUpdate($record['id'], $value);
            }

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }
}
