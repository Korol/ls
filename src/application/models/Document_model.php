<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с документами
 */
class Document_model extends MY_Model {

    private $table_document =
        "CREATE TABLE IF NOT EXISTS `assol_document` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `Name` VARCHAR(256) NOT NULL COMMENT 'Название файла/папки',
            `IsFolder` TINYINT(1) DEFAULT 0 COMMENT 'Флаг папки',
            `Parent` INT(11) NOT NULL DEFAULT 0 COMMENT 'ID родительского каталога',
            `EmployeeID` INT(11) NOT NULL COMMENT 'ID сотрудника загрузившего файл',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата создания',
            `DateUpdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата последнего редактирования',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Документы';";

    private $table_document_rights =
        "CREATE TABLE IF NOT EXISTS `assol_document_rights` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `DocumentID` INT(11) NOT NULL COMMENT 'ID папки',
            `EmployeeID` INT(11) NOT NULL COMMENT 'ID сотрудника',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`DocumentID`) REFERENCES `assol_document` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Права доступа к папкам';";


    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table_document);
        $this->db()->query($this->table_document_rights);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_DOCUMENT_RIGHTS_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_DOCUMENT_NAME, TRUE);
    }

    /**
     * Получить список документов и папок в указанной папке
     *
     * @param int $parent ID родительского каталога
     *
     * @return mixed
     */
    public function documentGetList($parent) {
        return $this->db()
            ->from(self::TABLE_DOCUMENT_NAME)
            ->where('Parent', $parent ? $parent : 0)
            ->select('ID, Name, IsFolder')
            ->order_by('IsFolder', 'DESC')
            ->order_by('Name', 'ASC')
            ->get()->result_array();
    }

    public function checkRights($idFolder, $idUser) {
        $query = $this->db()->query('
            SELECT `assol_document_rights`.`EmployeeID` AS \'ID\' FROM
                `assol_document`
            INNER JOIN `assol_document_rights` ON
                `assol_document_rights`.`DocumentID`= `assol_document`.`ID`
            WHERE `assol_document`.`ID`='.$idFolder
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
            SELECT `assol_document_rights`.`EmployeeID` AS \'ID\' FROM
                `assol_document`
            INNER JOIN `assol_document_rights` ON
                `assol_document_rights`.`DocumentID`= `assol_document`.`ID`
            WHERE `assol_document`.`ID`=?', array($idFolder)
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
            $folder = $this->db()->get_where(self::TABLE_DOCUMENT_NAME, array('ID' => $parent))->row_array();

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
        return $this->db()->get_where(self::TABLE_DOCUMENT_NAME, $data)->result_array();
    }

    /**
     * Получение указанный файл или папку
     *
     * @param string $id ID записи
     */
    public function documentGet($id) {
        return $this->db()->get_where(self::TABLE_DOCUMENT_NAME, array('ID' => $id))->row_array();
    }

    /**
     * Сохранение в базу данных
     *
     * @param string    $name       Название файла/папки
     * @param int       $parent     ID родительского каталога
     * @param bool      $isFolder   Флаг папки
     * @param int       $idEmployee ID сотрудника загрузившего файл / папку
     * @param string    $content    Содержимое файла
     *
     * @return int ID записи
     */
    public function documentInsert($name, $parent, $idEmployee, $isFolder = true, $content = null) {
        $data = array(
            'Name' => $name,
            'Parent' => $parent ? $parent : 0,
            'IsFolder' => $isFolder,
            'EmployeeID' => $idEmployee
        );
        $this->db()->set('DateCreate', 'NOW()', FALSE);

        if (empty($isFolder)) {
            // Открываем транзакция
            $this->db()->trans_start();

            // Вставляем информацию о файле
            $this->db()->insert(self::TABLE_DOCUMENT_NAME, $data);
            $id = $this->db()->insert_id();

            // Пытаемся сохранить в файл
            if (file_put_contents("./files/document/$id", $content) === FALSE) {
                $this->db()->trans_rollback(); // Отменяем транзакцию если ошибка
            } else {
                $this->db()->trans_complete(); // Завершаем транзакцию если успешно
            }

            return $id;
        } else {
            $this->db()->insert(self::TABLE_DOCUMENT_NAME, $data);
            return $this->db()->insert_id();
        }
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
        $this->db()->update(self::TABLE_DOCUMENT_NAME, $data);
    }


    public function folderRightInsert($idDocument, $employees) {
        if (is_array($employees)) {
            foreach ($employees as $idEmployee) {
                $this->db()->insert(self::TABLE_DOCUMENT_RIGHTS_NAME,
                    array('DocumentID' => $idDocument, 'EmployeeID' => $idEmployee));
            }
        }
    }

    public function folderRightUpdate($idDocument, $employees, $IsSub) {
        // 1. Удаление прошлых прав
        $this->db()->delete(self::TABLE_DOCUMENT_RIGHTS_NAME, array('DocumentID' => $idDocument));
        // 2. Добавление новых прав
        if (is_array($employees)) {
            foreach ($employees as $idEmployee) {
                // Если указан флаг IsSub - выставляем права пользователя для вложенных папок
                if ($IsSub == 1) {
                    $this->subFolderRightUpdate($idDocument, $idEmployee);
                }

                // Добавляем права для пользователя
                $this->db()->insert(self::TABLE_DOCUMENT_RIGHTS_NAME,
                    array('DocumentID' => $idDocument, 'EmployeeID' => $idEmployee));
            }
        }
    }

    private function subFolderRightUpdate($idDocument, $idEmployee) {
        // Обработка вложенных папок
        $subFolders = $this->folderGetList($idDocument);

        foreach ($subFolders as $folder) {
            // Проверяем есть ли ограничения на папку
            $record = $this->db()->limit(1)->get_where(self::TABLE_DOCUMENT_RIGHTS_NAME, ['DocumentID' => $folder['ID']])->row_array();
            // Если ограничений нет, то пропускаем папку
            if (empty($record)) continue;

            // Получение текущих прав пользователя
            $record = $this->db()->get_where(self::TABLE_DOCUMENT_RIGHTS_NAME,
                ['DocumentID' => $folder['ID'], 'EmployeeID' => $idEmployee])->row_array();

            // Добавляем права для пользователя если их нет
            if (empty($record)) {
                $this->db()->insert(self::TABLE_DOCUMENT_RIGHTS_NAME,
                    array('DocumentID' => $folder['ID'], 'EmployeeID' => $idEmployee));
            }

            // Обрабатываем рекурсивно вложенные папки
            $this->subFolderRightUpdate($folder['ID'], $idEmployee);
        }
    }

    /**
     * Удалить документ из системы
     *
     * @param int $id ID документа в системе
     */
    public function documentDelete($id) {
        $record = $this->db()->get_where(self::TABLE_DOCUMENT_NAME, ['ID' => $id])->row_array();

        if ($record && empty($record['IsFolder'])) {
            $file = './files/document/'.$record['ID'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_DOCUMENT_NAME, ['ID' => $id]); // Удаление записи из таблицы
        } else {
            // Рекурсивное удаление вложенных каталогов и документов
            $children = $this->documentGetList($id);
            foreach($children as $child) {
                $this->documentDelete($child['ID']);
            }

            $this->db()->delete(self::TABLE_DOCUMENT_NAME, array('ID' => $id));
        }
    }

    public function documentList($limit, $offset) {
        return $this->db()
            ->from(self::TABLE_DOCUMENT_NAME)
            ->where('IsFolder', 0)
            ->limit($limit, $offset)
            ->get()->result_array();
    }



}