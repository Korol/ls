<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TaskComment extends MY_Controller {

    public function add($idTask) {
        try {
            $comment = $this->input->post('comment');

            if (empty($comment))
                throw new Exception('Не указан текст комментария');

            $record = $this->getTaskModel()->insertTaskComment($this->getUserID(), $idTask, $comment);

            $this->json_response(['status' => 1, 'record' => $record]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function read($idTask) {
        try {
            $this->getTaskModel()->commentRead($this->getUserID(), $idTask);

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
