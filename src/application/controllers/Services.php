<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends MY_Controller {

    public function index() {
        $data = array(
            'sites' => $this->getSiteModel()->getRecords(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );

        $this->viewHeader($data);
        $this->view('form/services/index');
        $this->viewFooter([
            'js_array' => [
                'public/js/assol.services.js'
            ]
        ]);
    }
}
