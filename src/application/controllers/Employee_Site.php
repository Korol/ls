<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Site extends MY_Controller {

    public function data($EmployeeID) {
        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getEmployeeModel()->siteGetList($EmployeeID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($EmployeeID) {
        try {
            $data = $this->input->post('data');
            $sites = $data['sites'];
            $insert = false;

            if (!isset($EmployeeID, $sites))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getEmployeeModel()->siteGetList($EmployeeID);

            foreach($sites as $idSite) {
                $key = array_search($idSite, array_column($records, 'SiteID'));
                if (false === $key) {
                    if (empty($insert)) {
                        $insert = array();
                    }
                    $insert[] = $this->getEmployeeModel()->siteSave($EmployeeID, $idSite);
                }
            }

            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Site']);

            $this->json_response(array("status" => 1, "sites" => $sites, "insert" => $insert));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($EmployeeID, $idWorkSite) {
        try {
            if (!isset($EmployeeID, $idWorkSite))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getEmployeeModel()->siteDelete($idWorkSite);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Site']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}