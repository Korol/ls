<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с обучающими материалами
 */
class Training_model extends MY_Model {

    private $table_training =
        "CREATE TABLE IF NOT EXISTS `assol_training` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `Name` VARCHAR(256) NOT NULL COMMENT 'Название файла/папки',
            `IsFolder` TINYINT(1) DEFAULT 0 COMMENT 'Флаг папки',
            `Parent` INT(11) NOT NULL DEFAULT 0 COMMENT 'ID родительского каталога',
            `Content` LONGTEXT COMMENT 'Содержимое файла',
            `EmployeeID` INT(11) NOT NULL COMMENT 'ID сотрудника загрузившего файл',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата создания',
            `DateUpdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата последнего редактирования',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Обучающие материалы';";

    private $table_training_rights =
        "CREATE TABLE IF NOT EXISTS `assol_training_rights` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `TrainingID` INT(11) NOT NULL COMMENT 'ID папки',
            `EmployeeID` INT(11) NOT NULL COMMENT 'ID сотрудника',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`TrainingID`) REFERENCES `assol_training` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Права доступа к папкам';";


    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table_training);
        $this->db()->query($this->table_training_rights);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_TRAINING_RIGHTS_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_TRAINING_NAME, TRUE);
    }

    /**
     * Получить список документов и папок в указанной папке
     *
     * @param int $parent ID родительского каталога
     *
     * @return mixed
     */
    public function trainingGetList($parent) {
        $this->db()->select('ID, Name, IsFolder');
        $this->db()->order_by('IsFolder', 'DESC');
        $this->db()->order_by('Name', 'ASC');
        return $this->db()->get_where(self::TABLE_TRAINING_NAME, array('Parent' => $parent ? $parent : 0))->result_array();
    }

    public function checkRights($idFolder, $idUser) {
        $query = $this->db()->query('
            SELECT `assol_training_rights`.`EmployeeID` AS \'ID\' FROM
                `assol_training`
            INNER JOIN `assol_training_rights` ON
                `assol_training_rights`.`TrainingID`= `assol_training`.`ID`
            WHERE `assol_training`.`ID`='.$idFolder
        );

        // Если на папку установлены права, то пытаемся найти текущего пользователя
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                if ($row->ID == $idUser) return true;
            }
            return false;
        }

        return true;
    }

    /**
     * Получить список разрешенных пользователей для папки
     *
     * @param int $idFolder ID папки
     *
     * @return mixed
     */
    public function getFolderRights($idFolder) {
        return $this->db()->query('
            SELECT `assol_training_rights`.`EmployeeID` AS \'ID\' FROM
                `assol_training`
            INNER JOIN `assol_training_rights` ON
                `assol_training_rights`.`TrainingID`= `assol_training`.`ID`
            WHERE `assol_training`.`ID`=?', array($idFolder)
        )->result_array();
    }

    /**
     * Получить путь к указанной папке
     *
     * @param int $parent ID родительского каталога
     *
     * @return mixed
     */
    public function breadGetList($parent) {
        $res = array();

        while ($parent > 0) {
            $this->db()->select('ID, Name, Parent');
            $folder = $this->db()->get_where(self::TABLE_TRAINING_NAME, array('ID' => $parent))->row_array();

            array_unshift($res, array('ID' => $folder['ID'], 'Name' => $folder['Name']));

            $parent = $folder['Parent'];
        }

        return empty($res) ? null : $res;
    }

    /**
     * Получить список папок
     *
     * @param bool $parent ID родительской папки. Если не указан то выбираются все папки
     *
     * @return mixed
     */
    public function folderGetList($parent = FALSE) {
        $this->db()->select('ID, Name');
        $data = array('IsFolder' => 1);
        if ($parent !== FALSE) {
            $data['Parent'] = $parent;
        }
        return $this->db()->get_where(self::TABLE_TRAINING_NAME, $data)->result_array();
    }

    /**
     * Получение указанный файл или папку
     *
     * @param string $id ID записи
     */
    public function trainingGet($id) {
        return $this->db()->get_where(self::TABLE_TRAINING_NAME, array('ID' => $id))->row_array();
    }

    /**
     * Сохранение в базу данных
     *
     * @param string    $name       Название файла/папки
     * @param int       $parent     ID родительского каталога
     * @param bool      $isFolder   Флаг папки
     * @param int       $idEmployee ID сотрудника загрузившего файл / папку
     * @param string    $Content    Содержимое материала
     *
     * @return int ID записи
     */
    public function trainingInsert($name, $parent, $idEmployee, $isFolder = true, $Content = null) {
        $data = array(
            'Name' => $name,
            'Parent' => $parent ? $parent : 0,
            'IsFolder' => $isFolder,
            'EmployeeID' => $idEmployee,
            'Content' => $Content
        );
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_TRAINING_NAME, $data);
        return $this->db()->insert_id();
    }

    /**
     * Сохранение записи
     *
     * @param int       $id     записи
     * @param string    $name   Название файла/папки
     * @param int       $parent ID родительского каталога
     *
     */
    public function folderUpdate($id, $name, $parent) {
        $data = array(
            'Name' => $name,
            'Parent' => $parent ? $parent : 0
        );

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_TRAINING_NAME, $data);
    }

    /**
     * Сохранение записи
     *
     * @param int       $id         записи
     * @param string    $name       Название файла/папки
     * @param int       $parent     ID родительского каталога
     * @param string    $Content    Содержимое материала
     *
     */
    public function trainingUpdate($id, $name, $parent, $Content = null) {
        $data = array(
            'Name' => $name,
            'Parent' => $parent ? $parent : 0,
            'Content' => $Content
        );

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_TRAINING_NAME, $data);
    }


    public function trainingRightInsert($idTraining, $employees) {
        if (is_array($employees)) {
            foreach ($employees as $idEmployee) {
                $this->db()->insert(self::TABLE_TRAINING_RIGHTS_NAME,
                    array('TrainingID' => $idTraining, 'EmployeeID' => $idEmployee));
            }
        }
    }

    public function trainingRightUpdate($idTraining, $employees, $IsSub) {
        // 1. Удаление прошлых прав
        $this->db()->delete(self::TABLE_TRAINING_RIGHTS_NAME, array('TrainingID' => $idTraining));
        // 2. Добавление новых прав
        if (is_array($employees)) {
            foreach ($employees as $idEmployee) {
                // Если указан флаг IsSub - выставляем права пользователя для вложенных папок
                if ($IsSub == 1) {
                    $this->subFolderRightUpdate($idTraining, $idEmployee);
                }

                // Добавляем права для пользователя
                $this->db()->insert(self::TABLE_TRAINING_RIGHTS_NAME,
                    array('TrainingID' => $idTraining, 'EmployeeID' => $idEmployee));
            }
        }
    }

    private function subFolderRightUpdate($idTraining, $idEmployee) {
        // Обработка вложенных папок и документов
        $subRecords = $this->trainingGetList($idTraining);

        foreach ($subRecords as $subRecord) {
            // Проверяем есть ли ограничения
            $record = $this->db()->limit(1)->get_where(self::TABLE_TRAINING_RIGHTS_NAME, ['TrainingID' => $subRecord['ID']])->row_array();
            // Если ограничений нет, то пропускаем
            if (empty($record)) continue;

            // Получение текущих прав пользователя
            $record = $this->db()->get_where(self::TABLE_TRAINING_RIGHTS_NAME,
                ['TrainingID' => $subRecord['ID'], 'EmployeeID' => $idEmployee])->row_array();

            // Добавляем права для пользователя если их нет
            if (empty($record)) {
                $this->db()->insert(self::TABLE_TRAINING_RIGHTS_NAME,
                    array('TrainingID' => $subRecord['ID'], 'EmployeeID' => $idEmployee));
            }

            // Обрабатываем рекурсивно вложенные папки
            if ($subRecord['IsFolder'] == 1)
                $this->subFolderRightUpdate($subRecord['ID'], $idEmployee);
        }
    }

    /**
     * Удалить документ из системы
     *
     * @param int $id ID документа в системе
     */
    public function trainingDelete($id) {
        // Рекурсивное удаление вложенных каталогов и документов
        $children = $this->trainingGetList($id);
        foreach($children as $child) {
            $this->trainingDelete($child['ID']);
        }

        $this->db()->delete(self::TABLE_TRAINING_NAME, array('ID' => $id));
    }

}