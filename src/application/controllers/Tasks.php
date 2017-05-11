<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends MY_Controller {

    public function index() {
        if (IS_LOVE_STORY)
            $this->getTaskModel()->taskArchiveClear();

        $data = array(
            'idEmployee' => $this->getUserID(),
            'employees' => $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole())
        );
        $this->viewHeader($data);
        $this->view('form/tasks/index');
        $this->viewFooter(['isWysiwyg' => true]);
    }

    public function comments() {
        try {
            $id = $this->input->post('id');

            if (empty($id))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->json_response(array("status" => 1, 'records' => $this->getTaskModel()->taskCommentGetList($id)));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function sites() {
        try {
            $this->json_response(array("status" => 1, 'records' => $this->getSiteModel()->getRecords()));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function data($EmployeeID = false) { // TODO: Убрать
        try {
            if (!isset($EmployeeID) || empty($_POST))
                throw new RuntimeException("Не указан обязательный параметр");

            $data = $this->input->post('data');

            $expired = $this->getTaskModel()->taskExpiredGetList($this->getUserID(), $data);

            switch($data['TypeTask']) {
                case 0;
                    $records = $this->getTaskModel()->taskInGetList($this->getUserID(), $data);
                    break;
                case 1;
                    $records = $this->getTaskModel()->taskOutGetList($this->getUserID(), $data);
                    break;
                case 2;
                    $records = $this->getTaskModel()->taskArchiveGetList($this->getUserID(), $data);
                    break;
                case 3;
                    $records = $expired;
                    break;
                default:
                    throw new RuntimeException("Не указан тип списка задач");
            }

            $this->json_response(array("status" => 1, 'records' => $records, 'expired' => count($expired)));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function add() {
        // 1. Обработка данных формы
        try {
            $title = $this->input->post('title');
            $deadline = $this->input->post('deadline');
            $description = $this->input->post('description');
            $employees = $this->input->post('employees');
            $sites = $this->input->post('sites');
            $confirmation = $this->input->post('confirmation');

            if (empty($title))
                throw new Exception('Не указано название задачи');

            if (empty($deadline))
                throw new Exception('Не указан крайний срок');

            if (empty($description))
                throw new Exception('Не указано описание задачи');

            if (empty($employees)) {
                if (empty($sites))
                    throw new Exception('Не указан список пользователей. Укажите список пользователей или рабочии сайты');

                $employeeBySites = $this->getEmployeeModel()->findEmployeeBySites($sites);
                foreach($employeeBySites as $employee)
                    $employees[] = $employee['ID'];

                if (empty($employees))
                    throw new Exception('К указанным сайтам нет прикрепленных сотрудников');
            }

            foreach($employees as $idEmployee)
                $this->getTaskModel()->insertTask($this->getUserID(), $idEmployee, $title, $deadline, $description, $confirmation);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function update($idTask) {
        try {
            $data = $this->input->post('data');

            if (empty($data))
                throw new Exception('Нет данных для сохранения');

            $this->getTaskModel()->taskUpdate($idTask, $data);

            $this->json_response(['status' => 1, 'task' => $this->getTaskModel()->taskGet($idTask)]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function remove($idTask) {
        try {
            if (empty($idTask))
                throw new Exception('Не указан обязательный параметр');

            $this->getTaskModel()->taskDelete($idTask);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function done($idTask) {
        try {
            $task = $this->getTaskModel()->taskGet($idTask);

            // Если статус задачи 0 и взведен флаг подтверждения и автор и исполнитель
            // не совпадают - выставляем статус 1 (на подтверждение), иначе статус 2 (выполнена)
            $isUserTask = ($task['AuthorID'] == $task['EmployeeID']);
            $newState = empty($task['State']) && !empty($task['Confirmation']) && !$isUserTask ? 1 : 2;

            $this->getTaskModel()->taskUpdate($idTask, array("State" => $newState));

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}
