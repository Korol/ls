<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_Mailing extends MY_Controller {

    public function meta() {
        try {
            $site = $this->input->post('SiteID');
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $data = [
                'customers' => $this->getEmployeeModel()->siteCustomerGetList($employee, $site)
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

            $site = $this->input->post('SiteID');
            $year = $this->input->post('year');
            $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11

            $data = [
                'days' => $this->getReportModel()->reportMailing($employee, $site, $year, $month),
                'info' => $this->getReportModel()->reportMailingInfo($employee, $site, $year, $month)
            ];

            $this->json_response(["status" => 1, 'records' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function save() {
        try {
            $idCross = $this->input->post('idCross');
            $dateRecord = $this->input->post('dateRecord');

            if (empty($dateRecord)) {
                $year = $this->input->post('year');
                $month = $this->input->post('month') + 1; // Месяца на клиенте 0-11
                $id = $this->input->post('id');
                $age = $this->input->post('age');

                $record = $this->getReportModel()->reportMailingInfoFind($year, $month, $idCross);

                if (empty($record)) {
                    $this->getReportModel()->reportMailingInfoInsert($year, $month, $idCross, $id, $age);
                } else {
                    $this->getReportModel()->reportMailingInfoUpdate($record['id'], $id, $age);
                }
            } else {
                $value = $this->input->post('value');

                $record = $this->getReportModel()->reportMailingFind($dateRecord, $idCross);

                if (empty($record)) {
                    $this->getReportModel()->reportMailingInsert($dateRecord, $idCross, $value);
                } else {
                    $this->getReportModel()->reportMailingUpdate($record['id'], $value);
                }
            }

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }
}
