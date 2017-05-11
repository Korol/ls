<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Email extends MY_Controller {

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->emailGetList($CustomerID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($CustomerID) {
        try {
            $RecordID = $this->input->post('RecordID');
            $Email = $this->input->post('Email');
            $Note = $this->input->post('Note');

            if (!isset($CustomerID, $RecordID, $Email))
                throw new RuntimeException("Не указан обязательный параметр");

            if ($RecordID) {
                $this->getCustomerModel()->emailUpdate($RecordID, $Email, $Note);
            } else {
                $this->getCustomerModel()->emailInsert($CustomerID, $Email, $Note);
            }

            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Email']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->emailDelete($idRecord);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Email']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}