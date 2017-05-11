<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_OverallAllocation extends MY_Controller {

    public function data() {
        try {
            $siteID = $this->input->post('SiteID');

            function fio($person) {
                return $person['SName'].' '.$person['FName'];
            }

            $data = [
                'freeCustomers' => implode(", ", array_map('fio', $this->getCustomerModel()->findFreeCustomerBySiteID($siteID))),
                'freeAllSitesCustomers' => implode(", ", array_map('fio', $this->getCustomerModel()->findFreeCustomerAllSites($siteID))),
                'employee' => []
            ];

            // Получаем список сотрудников прикрепленных к сайту
            $employees = $this->getEmployeeModel()->findEmployeeBySite($siteID);

            foreach ($employees as $employee) {
                // Получаем список клиентов прикрепленных к сайту сотрудника
                $employee['customers'] = implode(", ", array_map('fio', $this->getEmployeeModel()->findEmployeeSiteCustomerBySiteID($employee['ID'], $siteID)));

                $data['employee'][] = $employee;
            }

            // Флаг первой записи для удобства рендера
            if (!empty($data['employee']))
                $data['employee'][0]['IsFirst'] = true;

            $this->json_response(["status" => 1, 'records' => $data]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
