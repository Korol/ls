<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_Correspondence extends MY_Controller {

    public function meta() {
        try {
            $year = $this->input->post('year');
            $month = $this->input->post('month');
            $site = $this->input->post('SiteID');
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $data = [
                'info' => $this->getReportModel()->reportCorrespondenceInfo($employee, $site, $year, $month)
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

            $data = $this->getReportModel()->reportCorrespondence($employee, $site, $year, $month);

            $this->json_response(["status" => 1, 'records' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function save() {
        try {
            $dateRecord = $this->input->post('dateRecord');
            $idRecord = $this->input->post('idRecord');

            if (empty($dateRecord)) {
                $year = $this->input->post('year');
                $month = $this->input->post('month');


                if (empty($idRecord)) {
                    $es2c = $this->input->post('es2c');
                    $offset = $this->input->post('offset');
                    $this->getReportModel()->reportCorrespondenceInfoInsert($es2c, $year, $month, $offset);
                } else {
                    $idInfo = $this->input->post('idInfo');
                    $idMenInfo = $this->input->post('idMenInfo');
                    $menInfo = $this->input->post('menInfo');

                    $this->getReportModel()->reportCorrespondenceInfoUpdate($idRecord, $idInfo, $idMenInfo, $menInfo);
                }
            } else {
                $value = $this->input->post('value');

                $record = $this->getReportModel()->reportCorrespondenceFind($dateRecord, $idRecord);

                if (empty($record)) {
                    $this->getReportModel()->reportCorrespondenceInsert($dateRecord, $idRecord, $value);
                } else {
                    $this->getReportModel()->reportCorrespondenceUpdate($record['id'], $value);
                }
            }

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function remove() {
        try {
            $idRecord = $this->input->post('record');

            $this->getReportModel()->reportCorrespondenceRemove($idRecord);

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function customers() {
        try {
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();
            $SiteID = $this->input->post('SiteID');

            $this->json_response(["status" => 1, 'records' => $this->getEmployeeModel()->findEmployeeSiteCustomerBySiteID($employee, $SiteID)]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }
}
