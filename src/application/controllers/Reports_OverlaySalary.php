<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_OverlaySalary extends MY_Controller {

    public function data() {
        try {
            $siteID = $this->input->post('SiteID');
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $data = $this->getReportModel()->reportOverlaySalary($siteID, $year, $month);

            $this->json_response(["status" => 1, 'records' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function save() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
            $idSite = $this->input->post('SiteID');
            $idEmployee = $this->input->post('idEmployee');
            $type = $this->input->post('type');
            $value = $this->input->post('value');

            $record = $this->getReportModel()->reportOverlaySalaryFind($idEmployee, $idSite, $year, $month);

            if (empty($record)) {
                if (IS_LOVE_STORY) {
                    $this->getReportModel()->reportOverlaySalaryInsert($idSite, $idEmployee, $year, $month, $type, $value);
                } else {
                    throw new Exception('Нет данных от переводчика за указанный месяц');
                }
            } else {
                $this->getReportModel()->reportOverlaySalaryUpdate($record['id'], $type, $value);
            }

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function close() {
        try {
            $idSite = $this->input->post('SiteID');
            $employee = $this->input->post('employee');
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $record = $this->getReportModel()->reportOverlaySalaryFind($employee, $idSite, $year, $month);

            if (empty($record)) {
                throw new RuntimeException('Не найдены данные для подтверждения');
            } else {
                $this->getReportModel()->reportOverlaySalaryClose($record['id']);
            }

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
