<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Children extends MY_Controller {

    public function data($EmployeeID) {
        try {
            if (!isset($EmployeeID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getEmployeeModel()->childrenGetList($EmployeeID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($EmployeeID) {
        try {
            $RecordID = $this->input->post('RecordID');
            $SexID = $this->input->post('SexID');
            $FIO = $this->input->post('FIO');
            $DOB = $this->input->post('DOB');

            if (!isset($EmployeeID, $RecordID, $SexID, $FIO, $DOB))
                throw new RuntimeException("Не указан обязательный параметр");

            if ($RecordID) {
                $this->getEmployeeModel()->childrenUpdate($RecordID, $SexID, $FIO, $DOB);
            } else {
                $this->getEmployeeModel()->childrenInsert($EmployeeID, $SexID, $FIO, $DOB);
            }

            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Children']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($EmployeeID, $idRecord) {
        try {
            if (!isset($idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getEmployeeModel()->childrenDelete($idRecord);
            $this->getEmployeeModel()->employeeUpdateNote($this->getUserID(), $EmployeeID, ['Children']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}