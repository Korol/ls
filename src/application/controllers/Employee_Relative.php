<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Relative extends MY_Controller {

    public function data($EmployeeID) {
        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getEmployeeModel()->relativeGetList($EmployeeID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($EmployeeID) {
        try {
            $RecordID = $this->input->post('RecordID');
            $FIO = $this->input->post('FIO');
            $occupation = $this->input->post('Occupation');

            if (!isset($EmployeeID, $RecordID, $FIO, $occupation))
                throw new RuntimeException("Не указан обязательный параметр");

            if ($RecordID) {
                $this->getEmployeeModel()->relativeUpdate($RecordID, $FIO, $occupation);
            } else {
                $this->getEmployeeModel()->relativeInsert($EmployeeID, $FIO, $occupation);
            }

            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Relative']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($EmployeeID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getEmployeeModel()->relativeDelete($idRecord);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Relative']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}