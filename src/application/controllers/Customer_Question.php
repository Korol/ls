<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Question extends MY_Controller {

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'])
            show_error('Данный раздел доступен только для роли "Директор"', 403, 'Доступ запрещен');
    }

    public function template() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            $this->json_response(array("status" => 1, 'questions' => $this->getCustomerModel()->questionTemplateGetList()));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function template_add() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            $question = $this->input->post('question');

            if (empty($question))
                throw new RuntimeException("Не указан обязательный параметр");

            $id = $this->getCustomerModel()->questionTemplateInsert($question);

            $this->json_response(array("status" => 1, 'id' => $id));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function template_edit() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            $id = $this->input->post('id');
            $question = $this->input->post('question');

            if (empty($id) || empty($question))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->questionTemplateUpdate($id, $question);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function template_remove() {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            $id = $this->input->post('id');

            if (empty($id))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->questionTemplateDelete($id);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->questionAnswerGetList($CustomerID);

            $this->json_response(array("status" => 1, 'questions' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($CustomerID) {
        try {
            if (!($this->isDirector() || $this->isSecretary()))
                throw new Exception('Доступ запрещен');

            $id = $this->input->post('id');
            $answer = $this->input->post('answer');

            if (empty($CustomerID) || empty($id) || empty($answer))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->questionAnswerSave($CustomerID, $id, $answer);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Question'], 'Вопрос №'.$id);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}