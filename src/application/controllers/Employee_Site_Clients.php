<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Site_Clients extends MY_Controller {

    public function data($EmployeeID, $idWorkSite) {
        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getEmployeeModel()->employeeSiteCustomerGetList($idWorkSite);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function find($EmployeeID, $idSite, $idUser) {
        try {
            if (!isset($EmployeeID, $idSite, $idUser))
                throw new RuntimeException("Не указан обязательный параметр");

            $customer = $this->getCustomerModel()->customerGet($idUser);

            if ($customer) {
                $work_sites = $this->getCustomerModel()->siteGetList($idUser);
                $key = array_search($idSite, array_column($work_sites, 'SiteID'));
                $customer['SiteExists'] = (false !== $key);
            }

            $this->json_response(array("status" => 1, 'records' => $customer));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($EmployeeID, $EmployeeSiteID, $idCustomer) {
        try {
            if (!isset($EmployeeID, $EmployeeSiteID, $idCustomer))
                throw new RuntimeException("Не указан обязательный параметр");

            if ($idCustomer > 0) {
                $id = $this->getEmployeeModel()->siteCustomerInsert($EmployeeSiteID, $idCustomer);
            } else {
                $siteCross = $this->getEmployeeModel()->siteGet($EmployeeSiteID);

                $records = $this->getCustomerModel()->findCustomerBySiteID($siteCross['SiteID']);

                foreach ($records as $record) {
                    $this->getEmployeeModel()->siteCustomerInsert($EmployeeSiteID, $record['ID']);
                }

                $id = 0;
            }

            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Site']);

            $this->json_response(array("status" => 1, "id" => $id));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($EmployeeID, $idSite, $idCustomer) {
        try {
            if (!isset($EmployeeID, $idSite, $idCustomer))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getEmployeeModel()->siteCustomerDelete($idCustomer);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Site']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}