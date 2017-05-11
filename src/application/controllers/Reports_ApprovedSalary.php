<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_ApprovedSalary extends MY_Controller {

    public function data() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $data = $this->getReportModel()->reportApprovedSalary($this->getUserID(), $year, $month);

            $this->json_response(["status" => 1, 'records' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
