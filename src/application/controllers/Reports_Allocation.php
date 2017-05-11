<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_Allocation extends MY_Controller {

    public function data() {
        try {
            function fio($person) {
                return $person['SName'].' '.$person['FName'];
            }

            $employeeSiteList = $this->getEmployeeModel()->siteGetList($this->getUserID());

            foreach ($employeeSiteList as $key => $employeeSite)
                $employeeSiteList[$key]['customers'] = implode(", ", array_map('fio',
                    $this->getEmployeeModel()->findEmployeeSiteCustomerBySiteID($this->getUserID(), $employeeSite['SiteID'])));

            $this->json_response(["status" => 1, 'records' => $employeeSiteList]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
