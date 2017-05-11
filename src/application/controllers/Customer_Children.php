<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Children extends MY_Controller {

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->childrenGetList($CustomerID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($CustomerID) {
        try {
            $RecordID = $this->input->post('RecordID');
            $SexID = $this->input->post('SexID');
            $FIO = $this->input->post('FIO');
            $Reside = $this->input->post('Reside');
            $DOB = $this->input->post('DOB');

            if (!isset($CustomerID, $RecordID, $SexID, $FIO, $DOB))
                throw new RuntimeException("Не указан обязательный параметр");

            if ($RecordID) {
                $this->getCustomerModel()->childrenUpdate($RecordID, $SexID, $FIO, $DOB, $Reside);
            } else {
                $this->getCustomerModel()->childrenInsert($CustomerID, $SexID, $FIO, $DOB, $Reside);
            }

            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Children']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->childrenDelete($idRecord);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Children']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}