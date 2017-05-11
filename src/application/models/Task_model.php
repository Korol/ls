<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с задачами
 */
class Task_model extends MY_Model {

    private $table_tasks =
        "CREATE TABLE IF NOT EXISTS `assol_tasks` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `Title` VARCHAR(256) NOT NULL COMMENT 'Название задачи',
            `AuthorID` INT(11) NOT NULL COMMENT 'Уникальный номер автора(сотрудника) задачи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Deadline` DATE NOT NULL COMMENT 'Крайний срок',
            `Description` LONGTEXT NOT NULL COMMENT 'Описание задачи',
            `Confirmation` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Требует подтверждение',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата и время создания задачи',
            `DateUpdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата и время последнего редактирования',
            `DateClose` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата и время закрытия задачи',
            `IsRead` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Флаг прочтения задачи исполнителем',
            `State` INT(11) NOT NULL DEFAULT '0' COMMENT 'Состояние задачи (0 - активна, 1 - на подтверждение, 2 - закрыта)',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Задачи';";

    private $table_task_comment =
        "CREATE TABLE IF NOT EXISTS `assol_task_comment` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `TaskID` INT(11) NOT NULL COMMENT 'Уникальный номер задачи',
            `AuthorID` INT(11) NOT NULL COMMENT 'Уникальный номер автора(сотрудника) комментария задачи',
            `Text` LONGTEXT NOT NULL COMMENT 'Текст комментария',
            `DateCreate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Временная метка',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`TaskID`) REFERENCES `assol_tasks` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Комментарии к задаче';";

    private $table_task_comment_read
        = "CREATE TABLE IF NOT EXISTS `assol_task_comment_read` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CommentID` INT(11) NOT NULL COMMENT 'Уникальный номер комментария к задаче',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`CommentID`) REFERENCES `assol_task_comment` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Состояние прочтения комментария к задаче';";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table_tasks);
        $this->db()->query($this->table_task_comment);
        $this->db()->query($this->table_task_comment_read);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_TASK_COMMENT_READ, TRUE);
        $this->dbforge->drop_table(self::TABLE_TASK_COMMENT, TRUE);
        $this->dbforge->drop_table(self::TABLE_TASK_NAME, TRUE);
    }

    /**
     * Добавление новой задачи
     *
     * @param int    $authorID      ID автора(сотрудника) задачи
     * @param int    $idEmployee    ID сотрудника
     * @param string $title         Название задачи
     * @param string $deadline      Крайний срок
     * @param string $description   Описание задачи
     * @param int    $confirmation  Требует подтверждение
     */
    public function insertTask($authorID, $idEmployee, $title, $deadline, $description, $confirmation) {
        $data = array(
            'AuthorID' => $authorID,
            'EmployeeID' => $idEmployee,
            'Title' => $title,
            'Deadline' => $deadline,
            'Description' => $description,
            'Confirmation' => $confirmation
        );
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_TASK_NAME, $data);
    }

    /**
     * Получить количество задач на подтверждение
     *
     * @param int $idEmployee ID сотрудника
     *
     * @return array количество задач на подтверждение
     */
    public function getCountConfirmationTask($idEmployee) {
        return $this->db()
                    ->from(self::TABLE_TASK_NAME)
                    ->where('AuthorID', $idEmployee)
                    ->where('State', 1)
                    ->count_all_results();
    }

    /**
     * Получить количество непрочитанных задач сотрудника
     *
     * @param int $idEmployee ID сотрудника
     *
     * @return array количество непрочитанных задач
     */
    public function getCountUnreadTask($idEmployee) {
        return $this->db()
            ->from(self::TABLE_TASK_NAME)
            ->where('EmployeeID', $idEmployee)
            ->where('IsRead', 0)
            ->where('State', 0)
            ->count_all_results();
    }

    /**
     * Получить количество невыполненных задач сотрудника
     *
     * @param int $idEmployee ID сотрудника
     *
     * @return array количество невыполненных задач
     */
    public function getCountUndoneTask($idEmployee) {
        return $this->db()
            ->from(self::TABLE_TASK_NAME)
            ->where('EmployeeID', $idEmployee)
            ->where('State', 0)
            ->count_all_results();
    }

    /**
     * Получить задачу
     *
     * @param int $idTask ID задачи
     *
     * @return mixed задача
     */
    public function taskGet($idTask) {
        return $this->db()
            ->select(
                "task.*,
                aut.FName as 'Author_FName', aut.SName as 'Author_SName',
                emp.FName as 'Employee_FName', emp.SName as 'Employee_SName'", false)
            ->from(self::TABLE_TASK_NAME . ' AS task')
            ->join(self::TABLE_EMPLOYEE_NAME." as aut", 'aut.ID = task.`AuthorID`', 'left')
            ->join(self::TABLE_EMPLOYEE_NAME." as emp", 'emp.ID = task.`EmployeeID`', 'left')
            ->where('task.ID', $idTask)
            ->get()->row_array();
    }

    /**
     * Получить список входящих задач для сотрудника
     *
     * @param int $idEmployee ID сотрудника
     * @param array $data фильтр
     *
     * @return array список задач
     */
    public function taskInGetList($idEmployee, $data = array()) {
        $this->db()
            ->select(
                "task.*,
                0 as 'TypeTasks',
                (COUNT(c.ID) - COUNT(cr.ID)) AS 'CountNewComment',
                aut.FName as 'Author_FName', aut.SName as 'Author_SName',
                emp.FName as 'Employee_FName', emp.SName as 'Employee_SName'", false)
            ->from(self::TABLE_TASK_NAME . ' AS task')
            ->join(self::TABLE_EMPLOYEE_NAME." as aut", 'aut.ID = task.`AuthorID`', 'left')
            ->join(self::TABLE_EMPLOYEE_NAME." as emp", 'emp.ID = task.`EmployeeID`', 'left')
            ->group_start()
                ->group_start()
                    ->where('task.EmployeeID', $idEmployee)
                    ->where('task.State', 0)
                ->group_end()
                ->or_group_start()
                    ->where('task.AuthorID', $idEmployee)
                    ->where('task.State', 1)
                ->group_end()
            ->group_end();

        if (isset($data['ByWhomTask']) && !empty($data['ByWhomTask']))
            $this->db()->where('task.AuthorID', $data['ByWhomTask']);

        // Подключение информации о непрочитанных комментариях
        $this->db()
            ->join(self::TABLE_TASK_COMMENT . ' AS c', 'c.TaskID = task.ID', 'left')
            ->join(self::TABLE_TASK_COMMENT_READ.' AS cr', "cr.EmployeeID = $idEmployee AND c.ID = cr.CommentID", 'left');

        return $this->db()
            ->order_by('task.Deadline ASC, task.ID DESC')
            ->group_by('task.ID')
            ->get()->result_array();
    }

    /**
     * Получить список просроченных задач для сотрудника
     *
     * @param int $idEmployee ID сотрудника
     * @param array $data фильтр
     *
     * @return array список задач
     */
    public function taskExpiredGetList($idEmployee, $data) {
        // Используем array_merge, так как, array_filter один элемент не обарачивает в массив. TODO: Разобраться почему так
        return array_merge(array_filter(
            $this->taskOutGetList($idEmployee, $data),
            function ($task) {
                // Если задача не выполнена и истек срок
                return empty($task['State']) && !empty($task['IsExpired']);
            }
        ), []);
    }

    /**
     * Получить список исходящих задач для сотрудника
     *
     * @param int $idEmployee ID сотрудника
     * @param array $data фильтр
     *
     * @return array список задач
     */
    public function taskOutGetList($idEmployee, $data) {
        $this->db()
            ->select(
                "task.*,
                1 as 'TypeTasks',
                (COUNT(c.ID) - COUNT(cr.ID)) AS 'CountNewComment',
                task.Deadline < NOW() as 'IsExpired',
                aut.FName as 'Author_FName', aut.SName as 'Author_SName',
                emp.FName as 'Employee_FName', emp.SName as 'Employee_SName'", false)
            ->from(self::TABLE_TASK_NAME . ' AS task')
            ->join(self::TABLE_EMPLOYEE_NAME." as aut", 'aut.ID = task.`AuthorID`', 'left')
            ->join(self::TABLE_EMPLOYEE_NAME." as emp", 'emp.ID = task.`EmployeeID`', 'left');

        $this->db()->group_start();
            // Если сотрудник автор и для LoveStory не прошел срок видимости выполенной задачи
            $this->db()->group_start();
                $this->db()->where('task.AuthorID', $idEmployee);

                // Для папки "Исходящие" ограничиваем срок видимости выполненных задач окончанием суток (00:00)
                if (IS_LOVE_STORY) {
                    $this->db()
                        ->group_start()
                            ->where('task.State !=', 2)
                            ->or_group_start()
                                ->where('task.State', 2)
                                ->where("DATE_FORMAT(DATE_ADD(task.DateClose, INTERVAL 1 DAY),'%Y-%m-%d') > NOW()", NULL, FALSE)
                            ->group_end()
                        ->group_end();
                }
            $this->db()->group_end();

            // Или задача отправленна на подтверждение
            $this->db()
                ->or_group_start()
                    ->where('task.State', 1)
                    ->where('task.EmployeeID', $idEmployee)
                ->group_end();

        $this->db()->group_end();

        if (isset($data['WhomTask']) && !empty($data['WhomTask']))
            $this->db()->where('task.EmployeeID', $data['WhomTask']);

        // Подключение информации о непрочитанных комментариях
        $this->db()
            ->join(self::TABLE_TASK_COMMENT . ' AS c', 'c.TaskID = task.ID', 'left')
            ->join(self::TABLE_TASK_COMMENT_READ.' AS cr', "cr.EmployeeID = $idEmployee AND c.ID = cr.CommentID", 'left');

        return $this->db()
            ->order_by('IsExpired DESC, task.ID DESC')
            ->group_by('task.ID')
            ->get()->result_array();
    }

    /**
     * Получить список архивных задач сотрудника
     *
     * @param int $idEmployee ID сотрудника
     * @param array $data фильтр
     *
     * @return array список задач
     */
    public function taskArchiveGetList($idEmployee, $data) {
        $this->db()
            ->select(
                "task.*,
                2 as 'TypeTasks',
                aut.FName as 'Author_FName', aut.SName as 'Author_SName',
                emp.FName as 'Employee_FName', emp.SName as 'Employee_SName'", false)
            ->from(self::TABLE_TASK_NAME . ' AS task')
            ->join(self::TABLE_EMPLOYEE_NAME." as aut", 'aut.ID = task.`AuthorID`', 'left')
            ->join(self::TABLE_EMPLOYEE_NAME." as emp", 'emp.ID = task.`EmployeeID`', 'left')
            ->where('task.State', 2)
            ->group_start()
                ->where('task.AuthorID', $idEmployee)
                ->or_where('task.EmployeeID', $idEmployee)
            ->group_end();

        if (isset($data['ByWhomTask']) && !empty($data['ByWhomTask']))
            $this->db()->where('task.AuthorID', $data['ByWhomTask']);

        if (isset($data['WhomTask']) && !empty($data['WhomTask']))
            $this->db()->where('task.EmployeeID', $data['WhomTask']);

        // Ограничение истории 1 месяц для LoveStory
        if (IS_LOVE_STORY)
            $this->db()->where("task.DateClose > DATE_ADD(NOW(), INTERVAL -1 MONTH)", null, false);

        return $this->db()
            ->order_by('task.DateClose DESC')
            ->get()->result_array();
    }

    /**
     * Обновление задачи
     *
     * @param int $idTask ID задачи
     * @param array $data поля для обновления
     */
    public function taskUpdate($idTask, $data) {
        // Если задача закрывается, то ставим временную метку закрытия
        if (isset($data['State'])) {
            if (IS_LOVE_STORY) {
                if ($data['State'] == 2)
                    $this->db()->set('DateClose', 'NOW()', FALSE);
            } else { // Для assol выставляем метку времени выполнения при отправке на подтверждение или выполнении
                if (($data['State'] == 1) || ($data['State'] == 2))
                    $this->db()->set('DateClose', 'NOW()', FALSE);
            }
        }

        $this->db()
            ->where('ID', $idTask)
            ->update(self::TABLE_TASK_NAME, $data);
    }

    /**
     * Функция удаления из базы задач старше 3-х месяцев (для LoveStory)
     */
    public function taskArchiveClear() {
        $this->db()->delete(self::TABLE_TASK_NAME, 'DateClose < DATE_ADD(NOW(), INTERVAL -3 MONTH)');
    }

    /**
     * Функция удаления из базы задачи по ID
     *
     * @param int $idTask ID задачи
     */
    public function taskDelete($idTask) {
        $this->db()->delete(self::TABLE_TASK_NAME, array('ID' => $idTask));
    }

    public function taskCommentGetList($idTask) {
        return $this->db()
            ->select('c.*, e.SName, e.FName')
            ->from(self::TABLE_TASK_COMMENT . ' AS c')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'c.AuthorID = e.ID', 'left')
            ->where('c.TaskID', $idTask)
            ->get()->result_array();
    }

    public function insertTaskComment($authorID, $idTask, $comment) {
        $data = array(
            'AuthorID' => $authorID,
            'TaskID' => $idTask,
            'Text' => $comment
        );
        $this->db()->insert(self::TABLE_TASK_COMMENT, $data);
        $idNewComment = $this->db()->insert_id();
        $this->db()->insert(self::TABLE_TASK_COMMENT_READ, ['EmployeeID' => $authorID, 'CommentID' => $idNewComment]);

        return $this->db()
            ->select('c.*, e.SName, e.FName')
            ->from(self::TABLE_TASK_COMMENT . ' AS c')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'c.AuthorID = e.ID', 'left')
            ->where('c.ID', $idNewComment)
            ->get()->row_array();
    }

    /**
     * Подготовка запроса непрочитанных комментариев
     *
     * @param int $employee ID сотрудника
     */
    private function initUnreadCommentQuery($employee) {
        $this->db()
            ->select('c.*')
            ->from(self::TABLE_TASK_COMMENT.' AS c')
            ->join(self::TABLE_TASK_NAME.' AS t', "t.ID=c.TaskID AND t.State<>2 AND (t.AuthorID=$employee OR t.EmployeeID=$employee)", 'inner', FALSE)
            ->join(self::TABLE_TASK_COMMENT_READ.' AS cr',
                "cr.EmployeeID = $employee AND c.ID = cr.CommentID", 'left');

        // Берем только записи без метки в таблице статусов
        $this->db()->where(['cr.ID' => null]);

        // Групировка комментариев по ID
        $this->db()->group_by('c.ID');
    }

    /**
     * Получить количество непрочитанных комментарий
     *
     * @param int $employee ID сотрудника
     * @return int
     */
    public function getCountUnreadComment($employee) {
        $this->initUnreadCommentQuery($employee);

        return $this->db()->count_all_results();
    }

    /**
     * Выставить метку прочтения комментарий
     *
     * @param int $employee ID сотрудника
     * @param int $idTask ID задачи
     */
    public function commentRead($employee, $idTask) {
        // Получения непрочитанных комментарий
        $this->initUnreadCommentQuery($employee);
        $commentList = $this->db()
            ->where('c.TaskID', $idTask)
            ->get()->result_array();

        // Ставим метку прочтения
        foreach ($commentList as $comment)
            $this->db()->insert(self::TABLE_TASK_COMMENT_READ, ['EmployeeID' => $employee, 'CommentID' => $comment['ID']]);
    }

}