<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с графиком работы сотрудников
 */
class Schedule_model extends MY_Model {

    private $table = "CREATE TABLE IF NOT EXISTS `assol_schedule` (
                          `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
                          `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
                          `Monday` VARCHAR(128) COMMENT 'Понедельник',
                          `MondayNote` VARCHAR(512) COMMENT 'Понедельник - Примечание',
                          `Tuesday` VARCHAR(128) COMMENT 'Вторник',
                          `TuesdayNote` VARCHAR(512) COMMENT 'Вторник - Примечание',
                          `Wednesday` VARCHAR(128) COMMENT 'Среда',
                          `WednesdayNote` VARCHAR(512) COMMENT 'Среда - Примечание',
                          `Thursday` VARCHAR(128) COMMENT 'Четверг',
                          `ThursdayNote` VARCHAR(512) COMMENT 'Четверг - Примечание',
                          `Friday` VARCHAR(128) COMMENT 'Пятница',
                          `FridayNote` VARCHAR(512) COMMENT 'Пятница - Примечание',
                          `Saturday` VARCHAR(128) COMMENT 'Суббота',
                          `SaturdayNote` VARCHAR(512) COMMENT 'Суббота - Примечание',
                          `Sunday` VARCHAR(128) COMMENT 'Воскресенье',
                          `SundayNote` VARCHAR(512) COMMENT 'Воскресенье - Примечание',
                          PRIMARY KEY (`ID`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='График работы';";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_SCHEDULE_NAME, TRUE);
    }

    /**
     * Получение графика для указанного сотрудника
     *
     * @param string $idEmployee ID сотрудника
     */
    public function scheduleGet($idEmployee) {
        return $this->db()->get_where(self::TABLE_SCHEDULE_NAME, array('EmployeeID' => $idEmployee))->row_array();
    }

    /**
     * Получение списка сотрудников с графиками работы за исключением текущего сорудника
     *
     * @param string $EmployeeID ID сотрудника для исключения из списка
     * @param int $EmployeeRole роль текущего пользователя
     */
    public function scheduleGetList($EmployeeID, $EmployeeRole) {
        $this->db()
            ->select('s.*, e.FName, e.SName, IFNULL(eo.DateOnline > DATE_ADD(NOW(), INTERVAL -10 SECOND), 0) as IsOnline')
            ->from(self::TABLE_EMPLOYEE_NAME.' AS e')
            ->join(self::TABLE_SCHEDULE_NAME.' AS s', 'e.ID = s.EmployeeID', 'left')
            // Подключение информации об онлайн
            ->join(self::TABLE_EMPLOYEE_ONLINE_NAME.' AS eo', 'e.ID = eo.EmployeeID', 'left')

            // Фильтруем список по активности пользователей
            ->where('e.IsDeleted', 0)
            ->where('e.IsBlocked', 0);

        // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
        if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$EmployeeID AND rgt.TargetEmployeeID=e.ID", 'left')
                ->group_start()
                    ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                    ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                ->group_end();
        }

        return $this->db()
            ->where('e.ID !=', $EmployeeRole)
            ->order_by('e.SName, e.FName', 'ASC')
            ->get()->result_array();
    }

    /**
     * Добавление новой записи
     *
     * @param string $idEmployee ID сотрудника
     * @param array $data поля для сохранения
     *
     * @return int ID вставленной записи
     */
    public function scheduleInsert($idEmployee, $data) {
        $data['EmployeeID'] = $idEmployee;
        $this->db()->insert(self::TABLE_SCHEDULE_NAME, $data);

        return $this->db()->insert_id();
    }

    /**
     * Сохранение записи
     *
     * @param int $id записи
     * @param array $data поля для сохранения
     */
    public function scheduleUpdate($id, $data) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SCHEDULE_NAME, $data);
    }

}