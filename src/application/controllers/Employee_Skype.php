<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Skype extends MY_Controller {

    public function data($EmployeeID) {
        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getEmployeeModel()->skypeGetList($EmployeeID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($EmployeeID) {
        try {
            $RecordID = $this->input->post('RecordID');
            $Value = $this->input->post('Skype');

            if (!isset($EmployeeID, $RecordID, $Value))
                throw new RuntimeException("Не указан обязательный параметр");

            if ($RecordID) {
                $this->getEmployeeModel()->skypeUpdate($RecordID, $Value);
            } else {
                $this->getEmployeeModel()->skypeInsert($EmployeeID, $Value);
            }

            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Skype']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($EmployeeID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getEmployeeModel()->skypeDelete($idRecord);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Skype']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}