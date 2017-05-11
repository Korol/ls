<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Video extends MY_Controller {

    public function data($CustomerID, $type) {
        try {
            if (!isset($CustomerID, $type))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->videoGetList($CustomerID, $type);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function add($CustomerID) {
        try {
            $link = $this->input->post('Link');
            $type = $this->input->post('Type');

            if (!isset($CustomerID, $link, $type))
                throw new RuntimeException("Не указан обязательный параметр");

            $query = parse_url($link, PHP_URL_QUERY);

            if ($query) {
                $link .= '&rel=0';
            } else {
                $link .= '?rel=0';
            }

            $id = $this->getCustomerModel()->videoInsert($CustomerID, $link, $type);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Video']);

            $this->json_response(array("status" => 1, 'id' => $id));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->videoDelete($idRecord);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Video']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}