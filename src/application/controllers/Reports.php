<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

    public function index() {
        $script_prefix = IS_LOVE_STORY ? 'lovestory' : 'assol';

        $js = [
            "public/js/$script_prefix.report.translate.js"
        ];

        $data = array(
            'employee' => $this->getEmployeeModel()->employeeGet($this->getUserID()),
            'sites' => $this->getSiteModel()->getRecords(),
            'translators' => $this->getEmployeeModel()->employeeTranslatorGetList()
        );

        if ($this->isDirector() || $this->isSecretary()) {
            // данные для таблицы Клиенты <–> Сайты
            $data['cs_customers'] = $this->getCustomerModel()->getListCustomersSites();//var_dump($data['cs_customers']);

            $js[] = "public/js/$script_prefix.report.director.js";
            $js[] = $this->isDirector()
                ? "public/js/$script_prefix.report.list.director.js"
                : "public/js/report.list.secretary.js";
        } else {
            $js[] = "public/js/$script_prefix.report.list.translate.js";
        }

        $this->viewHeader($data);
        $this->view('form/reports');
        $this->viewFooter(['js_array' => $js]);
    }

    public function data() {
        try {
            function folder($employee) {
                return [
                    'ID' => $employee['ID'],
                    'Name' => $employee['SName'].' '.$employee['FName'],
                    'Level' => 2
                ];
            }

            $this->json_response(["status" => 1, 'records' => array_map("folder", $this->getEmployeeModel()->employeeTranslatorGetList())]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function sites() {
        try {
            $employee = $this->isDirector()
                ? $this->input->post('employee')
                : $this->getUserID();

            $this->json_response(["status" => 1, 'records' => $this->getEmployeeModel()->siteGetList($employee)]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function savestat()
    {
        $res = 0;
        $cell = $this->input->post('cell', true); // ячейка (cell_12_34)
        $text = $this->input->post('text', true); // текст в ячейке
        if(!empty($cell)){
            $text = (!empty($text)) ? strip_tags($text) : '';
            $cell_info = explode('_', $cell);
            $customerID = (!empty($cell_info[1])) ? (int)$cell_info[1] : 0; // клиент
            $siteID = (!empty($cell_info[2])) ? (int)$cell_info[2] : 0; // сайт
            $res = $this->getCustomerModel()->updateCustomerSiteComment($customerID, $siteID, $text);
        }

        echo $res;
        return;
    }
}
