<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_History extends MY_Controller {

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->historyGetList($CustomerID);

            $this->json_response(array("status" => 1, 'history' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}