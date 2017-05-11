<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_Salary extends MY_Controller {

    public function data() {
        try {
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $data = $this->getReportModel()->reportSalary($employee, $year, $month);

            $this->json_response(["status" => 1, 'records' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function save() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
            $idEmployeeSite = $this->input->post('idEmployeeSite');
            $type = $this->input->post('type');
            $value = $this->input->post('value');

//            $record = $this->getReportModel()->reportOverlaySalaryFind($this->getUserID(), $year, $month);
//
//            if (empty($record)) {
                $record = $this->getReportModel()->reportSalaryFind($year, $month, $idEmployeeSite);

                if (empty($record)) {
                    $this->getReportModel()->reportSalaryInsert($year, $month, $idEmployeeSite, $type, $value);
                } else {
                    $this->getReportModel()->reportSalaryUpdate($record['id'], $type, $value);
                }
//            } else {
//                throw new RuntimeException('Данные уже отправлены в сводную таблицу');
//            }

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function close() {
        try {
            $employee = $this->getUserID();
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $this->getReportModel()->reportSalaryClose($employee, $year, $month);

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
