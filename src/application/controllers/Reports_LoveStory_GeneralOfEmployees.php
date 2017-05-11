<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_LoveStory_GeneralOfEmployees extends MY_Controller {

    public function meta() {
        try {
            $site = $this->input->post('site');

            $data = [
                'translators' => $this->getEmployeeModel()->findTranslatorBySite($site)
            ];

            $this->json_response(array("status" => 1, 'records' => $data));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function data() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
            $site = $this->input->post('site');

            $data = [
                'plans' => $this->getReportModel()->reportLoveStoryMountGeneralPlan($year, $month, $site),
                'total' => $this->getReportModel()->reportLoveStoryMountGeneralTotal($year, $month, $site),
                'agent' => $this->getReportModel()->reportLoveStoryMountPlanAgency($year, $month, $site),
                'report' => $this->getReportModel()->reportLoveStoryMountGeneralGroup($year, $month, $site)
            ];

            $this->json_response(array("status" => 1, 'records' => $data));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}
