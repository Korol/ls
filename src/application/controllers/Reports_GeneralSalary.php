<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_GeneralSalary extends MY_Controller {

    public function data() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $data = $this->getReportModel()->reportGeneralSalary($year, $month);
            $cross = $this->getEmployeeModel()->siteCrossGetList();

            $this->json_response(["status" => 1, 'records' => $data, 'cross' => $cross]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function save() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
            $idEmployee = $this->input->post('idEmployee');
            $idSite = $this->input->post('idSite');
            $value = $this->input->post('value');

            $record = $this->getReportModel()->reportGeneralSalaryFind($idEmployee, $idSite, $year, $month);

            if (empty($record)) {
                $this->getReportModel()->reportGeneralSalaryInsert($idEmployee, $idSite, $year, $month, $value);
            } else {
                $this->getReportModel()->reportGeneralSalaryUpdate($record['id'], $value);
            }

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function paid() {
        try {
            $idRecord = $this->input->post('idRecord');
            $paid = $this->input->post('paid');

            $this->getReportModel()->reportGeneralSalaryUpdatePaid($idRecord, $paid);

            $this->json_response(['status' => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
