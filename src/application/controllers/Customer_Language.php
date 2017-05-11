<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Language extends MY_Controller {

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->languageGetList($CustomerID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($CustomerID) {
        try {
            $RecordID = $this->input->post('RecordID');
            $LanguageID = $this->input->post('LanguageID');
            $Level = $this->input->post('Level');

            if (!isset($CustomerID, $RecordID, $LanguageID, $Level))
                throw new RuntimeException("Не указан обязательный параметр");

            if ($RecordID) {
                $this->getCustomerModel()->languageUpdate($RecordID, $LanguageID, $Level);
            } else {
                $this->getCustomerModel()->languageInsert($CustomerID, $LanguageID, $Level);
            }

            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Language']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->languageDelete($idRecord);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Language']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}