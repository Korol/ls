<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с каледарем
 */
class Calendar_model extends MY_Model {

    private $table = "CREATE TABLE IF NOT EXISTS `assol_calendar_event` (
                          `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
                          `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
                          `title` VARCHAR(256) NOT NULL COMMENT 'Название события',
                          `start` TIMESTAMP NOT NULL COMMENT 'Дата и время начала',
                          `end` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата и время завершения',
                          `remind` INT(11) NOT NULL COMMENT 'Количество минут за которое необходимо напомнить о событии',
                          `description` TEXT NULL DEFAULT NULL COMMENT 'Описание',
                          `completed` TINYINT(1) DEFAULT 0 COMMENT 'Флаг выполнения',
                          PRIMARY KEY (`ID`),
                          FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
                            ON UPDATE NO ACTION ON DELETE CASCADE
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Календарь';";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_CALENDAR_EVENT_NAME, TRUE);
    }

    /**
     * Получить список событий
     *
     * @param string $idEmployee ID сотрудника
     * @param string|bool $dtBegin начало периода. Если не указано, то выборка за текущий день
     * @param string|bool $dtEnd окончание периода. Если не указано, то выборка за текущий день
     *
     * @return array
     */
    public function calendarGet($idEmployee, $dtBegin = false, $dtEnd = false) {
        $this->initCalendarQuery($idEmployee, $dtBegin, $dtEnd);
        return $this->db()->get()->result_array();
    }

    /**
     * Получить количество невыполненных событий
     *
     * @param string $idEmployee ID сотрудника
     * @param string|bool $dtBegin начало периода. Если не указано, то выборка за текущий день
     * @param string|bool $dtEnd окончание периода. Если не указано, то выборка за текущий день
     *
     * @return int
     */
    public function calendarCount($idEmployee, $dtBegin = false, $dtEnd = false) {
        $this->initCalendarQuery($idEmployee, $dtBegin, $dtEnd);
        $this->db()->where('completed', 0);
        return $this->db()->count_all_results();
    }

    private function initCalendarQuery($idEmployee, $dtBegin = false, $dtEnd = false) {
        $this->db()
            ->select('id, title, start, end, description, completed, remind')
            ->from(self::TABLE_CALENDAR_EVENT_NAME)
            ->where('EmployeeID', $idEmployee);

        /* Выбираем запись если:
            1. Если задача полностью входит в рамки $dtBegin...$dtEnd
            2. $dtBegin входит в диапазон `start` ... `end`
            3. $dtEnd входит в диапазон `start` ... `end`
        */
        $this->db()->group_start();
        if (!empty($dtBegin) && !empty($dtEnd)) {
            $this->db()->where("(`start` >= '$dtBegin' AND `end` <= '$dtEnd')
                OR ('$dtBegin' BETWEEN `start` AND `end`)
                OR ('$dtEnd' BETWEEN `start` AND `end`)", NULL, FALSE);
        } else {
            $this->db()->where("(`start` >= DATE_FORMAT(NOW(),'%Y-%m-%d') AND `end` <=  DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY),'%Y-%m-%d'))
                OR (DATE_FORMAT(NOW(),'%Y-%m-%d') BETWEEN `start` AND `end`)
                OR (DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY),'%Y-%m-%d') BETWEEN `start` AND `end`)", NULL, FALSE);
        }
        $this->db()->group_end();
    }

    /**
     * Получить количество напоминаний для невыполненных событий
     *
     * @param string $idEmployee ID сотрудника
     *
     * @return int
     */
    public function calendarRemind($idEmployee) {
        $this->db()
            ->from(self::TABLE_CALENDAR_EVENT_NAME)
            ->where('EmployeeID', $idEmployee)
            ->where('completed', 0)
            /*
                Выбираем запись если:
                1. Напоминание установлено
                2. И дата события еще не наступила
                3. И дата события минус интервал превышает текущее время
             */
            ->group_start()
            ->where("(`remind` > 0)
                AND (`start` > NOW())
                AND (DATE_ADD(`start`, INTERVAL -`remind` MINUTE) < NOW())", NULL, FALSE)
            ->group_end();

        return $this->db()->count_all_results();
    }

    /**
     * Добавление нового события
     *
     * @param int       $userID         ID сотрудника
     * @param string    $title          Название события
     * @param string    $description    Описание события
     * @param string    $start          Дата и время начала события
     * @param string    $end            Дата и время завершения события
     * @param int       $remind         Количество минут за которое необходимо напомнить о событии
     *
     * @return int ID новой записи
     */
    public function eventInsert($userID, $title, $description, $start, $end, $remind) {
        $data = array(
            'EmployeeID' => $userID,
            'title' => $title,
            'description' => $description,
            'start' => $start,
            'end' => $end,
            'remind' => $remind
        );

        $this->db()->insert(self::TABLE_CALENDAR_EVENT_NAME, $data);

        return $this->db()->insert_id();
    }

    /**
     * Обновление события
     *
     * @param int       $id             ID события
     * @param string    $title          Название события
     * @param string    $description    Описание события
     * @param string    $start          Дата и время начала события
     * @param string    $end            Дата и время завершения события
     * @param int       $remind         Количество минут за которое необходимо напомнить о событии
     */
    public function eventUpdate($id, $title, $description, $start, $end, $remind) {
        $data = array(
            'title' => $title,
            'description' => $description,
            'start' => $start,
            'end' => $end,
            'remind' => $remind
        );

        $this->db()->update(self::TABLE_CALENDAR_EVENT_NAME, $data, array('id' => $id));
    }


    /**
     * Завершения события
     *
     * @param int $id ID события
     */
    public function eventDone($id) {
        $data = array(
            'completed' => 1
        );

        $this->db()->update(self::TABLE_CALENDAR_EVENT_NAME, $data, array('id' => $id));
    }

}