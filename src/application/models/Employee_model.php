<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с сотрудниками
 */
class Employee_model extends MY_Model {

    private $table_employee =
        "CREATE TABLE IF NOT EXISTS `assol_employee` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',            
            `SName` VARCHAR(64) NOT NULL COMMENT 'Фамилия',
            `FName` VARCHAR(64) NOT NULL COMMENT 'Имя',
            `MName` VARCHAR(64) COMMENT 'Отчество',
            `DOB` DATE COMMENT 'Дата рождения',
            `CardNumber` VARCHAR(64) COMMENT 'Номер карточки',
            `Avatar` INT(11) NOT NULL DEFAULT 0 COMMENT 'ID картинки аватара',
            `Forming` INT(11) COMMENT 'Образование (значение из справочника)',
            `FormingFormStudy` INT(11) COMMENT 'Форма обучения (значение из справочника)',
            `FormingNameInstitution` VARCHAR(128) COMMENT 'Название заведения',
            `FormingFaculty` VARCHAR(128) COMMENT 'Факультет',
            `WorkOccupation` VARCHAR(256) COMMENT 'Последнее место работы',
            `WorkReasonLeaving` VARCHAR(128) COMMENT 'Причина увольнения',
            `WorkLatestDirector` VARCHAR(128) COMMENT 'Директор последней работы',
            `Country` VARCHAR(128) COMMENT 'Страна (только Love Story)',
            `City` VARCHAR(128) COMMENT 'Город',
            `HomeAddress` VARCHAR(256) COMMENT 'Домашний адрес',
            `MaritalStatus` INT(11) COMMENT 'Семейное положение (значение из справочника)',
            `Smoking` INT(11) COMMENT 'Курение',
            `NameSatellite` VARCHAR(256) COMMENT 'ФИО спутника',
            `NameFather` VARCHAR(256) COMMENT 'ФИО отца',
            `NameMother` VARCHAR(256) COMMENT 'ФИО матери',
            `OccupationSatellite` VARCHAR(256) COMMENT 'Род деятельности спутника',
            `OccupationFather` VARCHAR(256) COMMENT 'Род деятельности отца',
            `OccupationMother` VARCHAR(256) COMMENT 'Род деятельности матери',
            `VideoConfirm` VARCHAR(512) COMMENT 'Ссылка на видео youtube',
            `Login` VARCHAR(64) NOT NULL COMMENT 'Логин для авторизации в системе',
            `Password` VARCHAR(64) NOT NULL COMMENT 'Пароль для авторизации в системе',
            `UserRole` INT(11) NOT NULL DEFAULT 10004 COMMENT 'Роль/Должность в системе',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата создания профиля',
            `DateUpdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата последнего редактирования',
            `DateDeleted` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата удаления',
            `Note` TEXT COMMENT 'Примечание',
            `IsBlocked` TINYINT(1) DEFAULT 0 COMMENT 'Флаг блокировки',
            `IsDeleted` TINYINT(1) DEFAULT 0 COMMENT 'Флаг удаления',
            `WhoDeleted` INT(11) COMMENT 'Кто удалил',
            `ReasonForDeleted` TEXT COMMENT 'Причина удаления',
            `ReasonForBlocked` TEXT COMMENT 'Причина блокировки',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сотрудники';";

    /** Таблица с историей изменений */
    private $table_employee_history =
        "CREATE TABLE IF NOT EXISTS `assol_employee_history` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `employee` INT(11) COMMENT 'ID сотрудника',            
            `author` INT(11) COMMENT 'ID сотрудника изменившего поле',            
            `field` TEXT COMMENT 'Название редактируемого поля',
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата редактирования поля',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='История изменений карточки сотрудника';";

    private $table_employee_online =
        "CREATE TABLE IF NOT EXISTS `assol_employee_online` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `DateOnline` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Временная отметка сотрудника',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Онлайн - метка пользователя сотрудников';";

    /** Таблица договоров с сотрудником */
    private $table_employee_agreement =
        "CREATE TABLE IF NOT EXISTS `assol_employee_agreement` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Name` VARCHAR(256) NULL COMMENT 'Название файла',
            `ext` VARCHAR(10) NOT NULL COMMENT 'Расширение файла',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Договора сотрудников';";

    /** Таблица с сканами паспорта сотрудника */
    private $table_employee_passport_scan =
        "CREATE TABLE IF NOT EXISTS `assol_employee_passport_scan` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Name` VARCHAR(256) NULL COMMENT 'Название файла',
            `ext` VARCHAR(10) NOT NULL COMMENT 'Расширение файла',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сканы паспорта сотрудника';";

    /** Таблица с детьмя сотрудников */
    private $table_employee_children =
        "CREATE TABLE IF NOT EXISTS `assol_employee_children` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `SexID` INT(11) NOT NULL COMMENT 'ID пола ребенка',
            `FIO` VARCHAR(128) COMMENT 'ФИО',
            `DOB` DATE COMMENT 'Дата рождения',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Дети сотрудника';";

    /** Таблица с родственниками сотрудников */
    private $table_employee_relative =
        "CREATE TABLE IF NOT EXISTS `assol_employee_relative` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `FIO` VARCHAR(128) COMMENT 'ФИО',
            `Occupation` VARCHAR(256) COMMENT 'Род дейтельности',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Родственники сотрудника';";

    /** Таблица с телефонами сотрудников */
    private $table_employee_phone =
        "CREATE TABLE IF NOT EXISTS `assol_employee_phone` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Phone` VARCHAR(32) COMMENT 'Телефон',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Телефоны сотрудника';";

    /** Таблица с E-Mail сотрудников */
    private $table_employee_email =
        "CREATE TABLE IF NOT EXISTS `assol_employee_email` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Email` VARCHAR(320) COMMENT 'E-Mail',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='E-Mail сотрудника';";

    /** Таблица с Skype сотрудников */
    private $table_employee_skype =
        "CREATE TABLE IF NOT EXISTS `assol_employee_skype` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Skype` VARCHAR(256) COMMENT 'логин Skype',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Skype сотрудника';";

    /** Таблица с ссылками на профили соц сетей сотрудников */
    private $table_employee_socnet =
        "CREATE TABLE IF NOT EXISTS `assol_employee_socnet` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Profile` VARCHAR(512) COMMENT 'Ссылка на профиль соцсети',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Соц сети сотрудника';";

    /** Таблица с сайтами сотрудника */
    private $table_employee_site =
        "CREATE TABLE IF NOT EXISTS `assol_employee_site` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `IsDeleted` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Флаг удаления',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сайты сотрудника';";

    /** Таблица с привязкой клиентов к сайтами сотрудника */
    private $table_employee_site_customer =
        "CREATE TABLE IF NOT EXISTS `assol_employee_site_customer` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `EmployeeSiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `IsDeleted` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Флаг удаления',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`CustomerID`) REFERENCES `assol_customer` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`EmployeeSiteID`) REFERENCES `assol_employee_site` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Связка клиентов с сайтами сотрудника';";

    private $table_employee_rights =
        "CREATE TABLE IF NOT EXISTS `assol_employee_rights` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'ID сотрудника',
            `TargetEmployeeID` INT(11) NOT NULL COMMENT 'ID сотрудника, к которому открываем доступ',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`TargetEmployeeID`) REFERENCES `assol_employee` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Права доступа к сотрудникам';";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table_employee);
        $this->db()->query($this->table_employee_history);
        $this->db()->query($this->table_employee_agreement);
        $this->db()->query($this->table_employee_passport_scan);
        $this->db()->query($this->table_employee_children);
        $this->db()->query($this->table_employee_relative);
        $this->db()->query($this->table_employee_phone);
        $this->db()->query($this->table_employee_email);
        $this->db()->query($this->table_employee_skype);
        $this->db()->query($this->table_employee_site);
        $this->db()->query($this->table_employee_socnet);
        $this->db()->query($this->table_employee_site_customer);
        $this->db()->query($this->table_employee_online);
        $this->db()->query($this->table_employee_rights);
    }

    /** Удаление таблиц */
    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_AGREEMENT_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_CHILDREN_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_RELATIVE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_PHONE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_EMAIL_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_SKYPE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_SITE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_SOCNET_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_ONLINE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_RIGHTS_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_HISTORY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_EMPLOYEE_NAME, TRUE);
    }

    /**
     * Получить список сотрудников
     *
     * @param int|bool $EmployeeID текущий пользователь
     * @param int|bool $EmployeeRole роль текущего пользователя
     * @param array|bool $data
     * @return mixed
     */
    public function employeeGetList($EmployeeID = false, $EmployeeRole = false, $data = false) {
        if (is_array($data)) {
            // Указываем таблицу
            $this->db()
                ->select("e.*, p.Phone, eml.Email, IFNULL(eo.DateOnline > DATE_ADD(NOW(), INTERVAL -10 SECOND), 0) as IsOnline, eo.DateOnline, CONCAT(img.ID, '.', img.ext) as 'FileName'", false)
                ->from(self::TABLE_EMPLOYEE_NAME.' AS e')
                ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left');

            // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
            if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
                $this->db()
                    ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$EmployeeID AND rgt.TargetEmployeeID=e.ID", 'left')
                    ->group_start()
                        ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                        ->or_where('e.ID', $EmployeeID)
                        ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                    ->group_end();
            }

            // Фильтрация списка по статусу
            if (isset($data['Status'])) {
                switch($data['Status']) {
                    case 1:
                        $this->db()->where('e.IsDeleted', 0);
                        $this->db()->where('e.IsBlocked', 0);
                        break;
                    case 2:
                        $this->db()->where('e.IsBlocked', 1);
                        break;
                    case 3:
                        $this->db()->where('e.IsDeleted', 1);
                        break;
                }
            } else {
                $this->db()->where('e.IsDeleted', 0);
                $this->db()->where('e.IsBlocked', 0);
            }

            // Фильтрация списка по роли
            if (isset($data['UserRole']) && ($data['UserRole'] > 0)) {
                $this->db()->where('e.UserRole', $data['UserRole']);
            }

            // Поиск по ФИО
            if (isset($data['FIO']) && $data['FIO']) {
                $this->db()
                    ->group_start()
                        ->like('e.FName', $data['FIO'])
                        ->or_like('e.SName', $data['FIO'])
                    ->group_end();
            }

            // Фильтрация списка по сайтам
            if (isset($data['Site']) && $data['Site']) {
                $this->db()->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                    'e.ID = es.EmployeeID AND es.IsDeleted = 0 AND es.SiteID='.$data['Site'], 'inner');
            }

            // Подключение информации об онлайн
            $this->db()->join(self::TABLE_EMPLOYEE_ONLINE_NAME.' AS eo',
                'e.ID = eo.EmployeeID', 'left');

            // Подключение информации об номере телефона
            $this->db()->join(self::TABLE_EMPLOYEE_PHONE_NAME.' AS p',
                'e.ID = p.EmployeeID', 'left');

            // Подключение информации об E-Mail
            $this->db()->join(self::TABLE_EMPLOYEE_EMAIL_NAME.' AS eml',
                'e.ID = eml.EmployeeID', 'left');

            // Групировка по сотрудникам
            $this->db()->group_by('e.ID');

            return array(
                'count' => $this->db()->count_all_results('', FALSE),
                'records' => $this->db()
                                ->limit($data['Limit'], $data['Offset'])
                                ->order_by('IsOnline', 'DESC')
                                ->get()->result_array()
            );
        } else {
            return $this->db()
                ->select("e.*, CONCAT(img.ID, '.', img.ext) as 'FileName'")
                ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
                ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left')
                ->get()->result_array();
        }
    }

    /**
     * Получить список сотрудников (без метки удаления) с фильтрацией по правам доступа
     *
     * @param int|bool $EmployeeID текущий пользователь
     * @param int|bool $EmployeeRole роль текущего пользователя
     *
     * @return mixed
     */
    public function employeeGetActiveList($EmployeeID, $EmployeeRole) {
        $this->db()
            ->select('e.ID, e.SName, e.FName, e.MName')
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e');

        // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
        if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$EmployeeID AND rgt.TargetEmployeeID=e.ID", 'left')
                ->group_start()
                    ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                    ->or_where('e.ID', $EmployeeID)
                    ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                ->group_end();
        }

        return $this->db()
            ->where('e.IsDeleted', 0)
            ->order_by('e.SName, e.FName', 'ASC')
            ->group_by('e.ID')
            ->get()->result_array();
    }

    /**
     * Получить список сотрудников за исключением указанного
     *
     * @param int $idEmployee
     * @param int $employeeRole
     *
     * @return mixed
     */
    public function employeeOtherGetList($idEmployee, $employeeRole) {
        // Для сайтов разный порядок сортировки полей
        if (IS_LOVE_STORY) {
            $field_1 = 'e.SName';
            $field_2 = 'e.FName';
        } else {
            $field_1 = 'e.FName';
            $field_2 = 'e.SName';
        }

        $this->db()
            ->select("e.ID, e.SName, e.FName, e.MName")
            ->select("CONCAT(img.ID, '.', img.ext) as 'FileName'")
            ->select("MAX(msg.id) as 'MaxMessageID', 0 as 'isChat'")
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left')
            ->join(self::TABLE_MESSAGE_NAME . ' AS msg', '(msg.sender = e.ID OR msg.recipient = e.ID)', 'left', FALSE)
            ->where('e.ID !=', $idEmployee)
            ->where('e.IsDeleted', 0);

        // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
        if (in_array($employeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$idEmployee AND rgt.TargetEmployeeID=e.ID", 'left')
                ->group_start()
                ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                ->group_end();
        }

        return $this->db()
            ->order_by($field_1, 'ASC')
            ->order_by($field_2, 'ASC')
            ->group_by('e.id')
            ->get()->result_array();
    }

    /**
     * Поиск сотрудников прекрепленных к указанным сайтам
     *
     * @param array $sites список сайтов
     *
     * @return array список сотрудников
     */
    public function findEmployeeBySites($sites) {
        return $this->db()
            ->select('e.ID')
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es', 'e.ID = es.EmployeeID AND es.IsDeleted = 0', 'inner')
            ->where_in('es.SiteID', $sites)
            ->where('e.IsDeleted', 0)
            ->where('e.IsBlocked', 0)
            ->group_by('e.ID')
            ->get()->result_array();
    }

    /**
     * Получить информацию о сотруднике
     *
     * @param int $id ID сотрудника в системе
     *
     * @return mixed
     */
    public function employeeGet($id) {
        return $this->db()
            ->select("e.*, IFNULL(eo.DateOnline > DATE_ADD(NOW(), INTERVAL -10 SECOND), 0) as IsOnline, CONCAT(img.ID, '.', img.ext) as 'FileName'", false)
            ->from(self::TABLE_EMPLOYEE_NAME.' AS e')
            ->join(self::TABLE_EMPLOYEE_ONLINE_NAME.' AS eo',
                'e.ID = eo.EmployeeID', 'left')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left')
            ->where('e.ID', $id)
            ->get()->row_array();
    }

    /**
     * Добавление нового сотрудника
     *
     * @param string $sName фамилия
     * @param string $fName имя
     * @param string $mName отчество
     *
     * @return int ID нового сотрудника
     */
    public function employeeInsert($sName, $fName, $mName) {
        $data = array(
            'SName' => $sName,
            'FName' => $fName,
            'MName' => $mName,
            'Password' => $this->generatePassword()
        );
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_EMPLOYEE_NAME, $data);

        return $this->db()->insert_id();
    }

    public function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    /**
     * Сохранение информации о сотруднике
     *
     * @param int $id сотрудника
     * @param array $data массив полей для сохранения. Например: array('SName' => 'Иванова', 'FName' => 'Аня')
     */
    public function employeeUpdate($id, $data) {
        $this->db()
            ->where('ID', $id)
            ->update(self::TABLE_EMPLOYEE_NAME, $data);
    }

    /**
     * Удалить сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function employeeDelete($id) {
        $this->db()->delete(self::TABLE_EMPLOYEE_NAME, array('ID' => $id));
    }

    /**
     * Получить список договоров для сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function agreementGetList($idEmployee) {
        $this->db()->select('ID, Name, ext');
        return $this->db()->get_where(self::TABLE_EMPLOYEE_AGREEMENT_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Получить договор по ID
     *
     * @param int $idAgreement ID записи в базе
     */
    public function agreementGet($idAgreement) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_AGREEMENT_NAME, array('ID' => $idAgreement))->row_array();
    }

    /**
     * Получить информацию о договоре (без содержимого)
     *
     * @param int $idAgreement ID записи в базе
     */
    public function agreementGetMeta($idAgreement) {
        $this->db()->select('ID, Name, EmployeeID, ext');
        return $this->db()->get_where(self::TABLE_EMPLOYEE_AGREEMENT_NAME, array('ID' => $idAgreement))->row_array();
    }

    /**
     * Сохранение договора в базу данных
     *
     * @param int $idEmployee ID сотрудника
     * @param string $name Название файла
     * @param string $content Содержимое файла
     * @param string $ext Расширение файла
     *
     * @return int ID записи
     */
    public function agreementInsert($idEmployee, $name, $content, $ext) {
        // Открываем транзакция
        $this->db()->trans_start();

        // Вставляем информацию о файле
        $this->db()->insert(self::TABLE_EMPLOYEE_AGREEMENT_NAME, ['EmployeeID' => $idEmployee, 'Name' => $name, 'ext' => $ext]);
        $id = $this->db()->insert_id();

        // Пытаемся сохранить в файл
        if (file_put_contents("./files/employee/agreement/$id.$ext", $content) === FALSE) {
            $this->db()->trans_rollback(); // Отменяем транзакцию если ошибка
        } else {
            $this->db()->trans_complete(); // Завершаем транзакцию если успешно
        }

        return $id;
    }

    /**
     * Удалить договор из базы
     *
     * @param int $idAgreement ID записи в базе
     */
    public function agreementDelete($idAgreement) {
        $agreement = $this->db()->get_where(self::TABLE_EMPLOYEE_AGREEMENT_NAME, ['ID' => $idAgreement])->row_array();

        if ($agreement) {
            $file = './files/employee/agreement/'.$agreement['ID'].'.'.$agreement['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_EMPLOYEE_AGREEMENT_NAME, ['ID' => $idAgreement]); // Удаление записи из таблицы
        }
    }

    /**
     * Получить список сканов паспорта для сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function passportGetList($idEmployee) {
        $this->db()->select('ID, Name, ext');
        return $this->db()->get_where(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Получить скан паспорта по ID
     *
     * @param int $idPassport ID записи в базе
     */
    public function passportGet($idPassport) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME, array('ID' => $idPassport))->row_array();
    }

    /**
     * Получить информацию о сканах паспорта (без содержимого)
     *
     * @param int $idPassport ID записи в базе
     */
    public function passportGetMeta($idPassport) {
        $this->db()->select('ID, Name, EmployeeID, ext');
        return $this->db()->get_where(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME, array('ID' => $idPassport))->row_array();
    }

    /**
     * Сохранение скана паспорта в базу данных
     *
     * @param int $idEmployee ID сотрудника
     * @param string $name Название файла
     * @param string $content Содержимое файла
     * @param string $ext Расширение файла
     *
     * @return int ID записи
     */
    public function passportInsert($idEmployee, $name, $content, $ext) {
        // Открываем транзакция
        $this->db()->trans_start();

        // Вставляем информацию о файле
        $this->db()->insert(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME, ['EmployeeID' => $idEmployee, 'Name' => $name, 'ext' => $ext]);
        $id = $this->db()->insert_id();

        // Пытаемся сохранить в файл
        if (file_put_contents("./files/employee/passport/$id.$ext", $content) === FALSE) {
            $this->db()->trans_rollback(); // Отменяем транзакцию если ошибка
        } else {
            $this->db()->trans_complete(); // Завершаем транзакцию если успешно
        }

        return $id;
    }

    /**
     * Удалить скан паспорта из базы
     *
     * @param int $idPassport ID записи в базе
     */
    public function passportDelete($idPassport) {
        $record = $this->db()->get_where(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME, ['ID' => $idPassport])->row_array();

        if ($record) {
            $file = './files/employee/passport/'.$record['ID'].'.'.$record['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME, ['ID' => $idPassport]); // Удаление записи из таблицы
        }
    }

    /**
     * Получить список детей сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function childrenGetList($idEmployee) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_CHILDREN_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Добавление ребенка сотруднику
     *
     * @param int $idEmployee ID сотрудника
     * @param int $idChildrenSex ID пола ребенка из справочника
     * @param string $fio ФИО ребенка
     * @param string $dob Дата рождения ребенка
     *
     * @return int ID записи
     */
    public function childrenInsert($idEmployee, $idChildrenSex, $fio, $dob) {
        $this->db()->insert(self::TABLE_EMPLOYEE_CHILDREN_NAME,
            array('EmployeeID' => $idEmployee, 'SexID' => $idChildrenSex, 'FIO' => $fio, 'DOB' => $dob));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о ребенке
     *
     * @param int $id ID записи
     * @param int $idChildrenSex ID пола ребенка из справочника
     * @param string $fio ФИО ребенка
     * @param string $dob Дата рождения ребенка
     */
    public function childrenUpdate($id, $idChildrenSex, $fio, $dob) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_EMPLOYEE_CHILDREN_NAME, array('SexID' => $idChildrenSex, 'FIO' => $fio, 'DOB' => $dob));
    }

    /**
     * Удалить ребенка сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function childrenDelete($id) {
        $this->db()->delete(self::TABLE_EMPLOYEE_CHILDREN_NAME, array('ID' => $id));
    }

    /**
     * Получить список родственников сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function relativeGetList($idEmployee) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_RELATIVE_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Добавление родственника сотруднику
     *
     * @param int $idEmployee ID сотрудника
     * @param string $fio ФИО родственника
     * @param string $occupation Род деятельности родственника
     *
     * @return int ID записи
     */
    public function relativeInsert($idEmployee, $fio, $occupation) {
        $this->db()->insert(self::TABLE_EMPLOYEE_RELATIVE_NAME,
            array('EmployeeID' => $idEmployee, 'FIO' => $fio, 'Occupation' => $occupation));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о родственнике
     *
     * @param int $id ID записи
     * @param string $fio ФИО родственника
     * @param string $occupation Род деятельности родственника
     */
    public function relativeUpdate($id, $fio, $occupation) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_EMPLOYEE_RELATIVE_NAME, array('FIO' => $fio, 'Occupation' => $occupation));
    }

    /**
     * Удалить родственника сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function relativeDelete($id) {
        $this->db()->delete(self::TABLE_EMPLOYEE_RELATIVE_NAME, array('ID' => $id));
    }

    /**
     * Получить список телефонов сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function phoneGetList($idEmployee) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_PHONE_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Добавление телефона сотруднику
     *
     * @param int $idEmployee ID сотрудника
     * @param string $phone телефон
     *
     * @return int ID записи
     */
    public function phoneInsert($idEmployee, $phone) {
        $this->db()->insert(self::TABLE_EMPLOYEE_PHONE_NAME,
            array('EmployeeID' => $idEmployee, 'Phone' => $phone));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о телефоне
     *
     * @param int $id ID записи
     * @param string $phone телефон
     */
    public function phoneUpdate($id, $phone) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_EMPLOYEE_PHONE_NAME, array('Phone' => $phone));
    }

    /**
     * Удалить телефон сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function phoneDelete($id) {
        $this->db()->delete(self::TABLE_EMPLOYEE_PHONE_NAME, array('ID' => $id));
    }

    /**
     * Получить список email сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function emailGetList($idEmployee) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_EMAIL_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Добавление email сотруднику
     *
     * @param int $idEmployee ID сотрудника
     * @param string $email E-Mail
     *
     * @return int ID записи
     */
    public function emailInsert($idEmployee, $email) {
        $this->db()->insert(self::TABLE_EMPLOYEE_EMAIL_NAME,
            array('EmployeeID' => $idEmployee, 'Email' => $email));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о email
     *
     * @param int $id ID записи
     * @param string $email E-Mail
     */
    public function emailUpdate($id, $email) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_EMPLOYEE_EMAIL_NAME, array('Email' => $email));
    }

    /**
     * Удалить email сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function emailDelete($id) {
        $this->db()->delete(self::TABLE_EMPLOYEE_EMAIL_NAME, array('ID' => $id));
    }

    /**
     * Получить список сайтов сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function siteGetList($idEmployee) {
        return $this->db()
            ->from(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es')
            ->select('es.*')
            ->join(self::TABLE_SITE_NAME . ' AS s', 's.ID = es.SiteID', 'inner')
            ->where('es.EmployeeID', $idEmployee)
            ->where('es.IsDeleted', 0)
            ->order_by('s.Name', 'ASC')
            ->get()->result_array();
    }

    /**
     * Получить список сайтов всех сотрудников
     */
    public function siteAllEmployeeGetList() {
        return $this->db()
            ->select('es.*')
            ->from(self::TABLE_EMPLOYEE_SITE_NAME . " AS es")
            ->join(self::TABLE_SITE_NAME . " AS s", 's.ID = es.SiteID', 'inner')
            ->where('es.IsDeleted', 0)
            ->order_by('s.Name', 'ASC')
            ->group_by('es.SiteID')
            ->get()->result_array();
    }

    /**
     * Получить список всех связок сайтов с сотрудниками
     */
    public function siteCrossGetList() {
        return $this->db()
            ->select('es.*')
            ->from(self::TABLE_EMPLOYEE_SITE_NAME . " AS es")
            ->join(self::TABLE_SITE_NAME . " AS s", 's.ID = es.SiteID', 'inner')
            ->where('es.IsDeleted', 0)
            ->get()->result_array();
    }

    /**
     * Получить сайт сотрудника
     *
     * @param int $idEmployeeSite ID сайта сотрудника
     */
    public function siteGet($idEmployeeSite) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_SITE_NAME, ['ID' => $idEmployeeSite])->row_array();
    }

    /**
     * Добавление сайтов сотруднику
     *
     * @param int $idEmployee ID сотрудника
     * @param string $idSite сайт
     *
     * @return int ID записи
     */
    public function siteSave($idEmployee, $idSite) {
        $data = ['EmployeeID' => $idEmployee, 'SiteID' => $idSite];

        $row = $this->db()->get_where(self::TABLE_EMPLOYEE_SITE_NAME, $data)->row_array();

        if (empty($row)) {
            $this->db()->insert(self::TABLE_EMPLOYEE_SITE_NAME, $data);
        } else {
            $this->db()->update(self::TABLE_EMPLOYEE_SITE_NAME, ['IsDeleted' => 0], ['ID' => $row['ID']]);
        }

        return $this->db()->insert_id();
    }

    /**
     * Удалить сайт сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function siteDelete($id) {
        $this->db()->update(self::TABLE_EMPLOYEE_SITE_NAME, ['IsDeleted' => 1], ['ID' => $id]);
    }

    /**
     * Получить список клиентов привязанных к сотруднику
     *
     * @param int $idEmployee ID сотрудника
     */
    public function employeeCustomerGetList($idEmployee) {
        return $this->db()
            ->select("c.ID as 'CustomerID', c.FName, c.SName")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es.EmployeeID = '.$idEmployee.' AND es.IsDeleted = 0 AND es2c.EmployeeSiteID = es.ID', 'inner')
            ->order_by('c.SName, c.FName', 'ASC')
            ->group_by('c.ID')
            ->get()->result_array();
    }

    /**
     * Получить список клиентов привязанных к сотруднику
     */
    public function allEmployeeCustomerGetList() {
        return $this->db()
            ->select("c.ID as 'CustomerID', c.FName, c.SName")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0', 'inner')
            ->order_by('c.SName, c.FName', 'ASC')
            ->group_by('c.ID')
            ->get()->result_array();
    }

    /**
     * Получить список клиентов привязанных к сайту
     *
     * @param int $employee ID сотрудника
     * @param int $idSite ID сайта
     * @return
     */
    public function siteCustomerGetList($employee, $idSite) {
        return $this->db()
            ->select("es2c.ID as 'es2cID', c.FName, c.SName")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0 AND es.EmployeeID='.$employee.' AND es.SiteID='.$idSite, 'inner')
            ->order_by('c.SName, c.FName', 'ASC')
            ->get()->result_array();
    }

    /**
     * Получить список клиентов привязанных к сайту сотрудника
     *
     * @param int $idEmployeeSite ID связки сотрудника с сайтом
     */
    public function employeeSiteCustomerGetList($idEmployeeSite) {
        return $this->db()
            ->select('es2c.ID, c.FName, c.SName')
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0 AND es2c.EmployeeSiteID = '.$idEmployeeSite, 'inner')
            ->order_by('c.SName, c.FName', 'ASC')
            ->get()->result_array();
    }

    /**
     * Поиск список клиентов прикрепленных к рабочему сайту сотрудника
     *
     * @param int $employee ID сотрудника
     * @param int $SiteID ID сайта
     */
    public function findEmployeeSiteCustomerBySiteID($employee, $SiteID) {
        return $this->db()
            ->select('es2c.ID, c.FName, c.SName')
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0 AND es.EmployeeID = ' . $employee . ' AND es.SiteID = ' . $SiteID, 'inner')
            ->order_by('c.SName, c.FName', 'ASC')
            ->get()->result_array();
    }

    /**
     * Добавление клиента к сайту сотруднику
     *
     * @param int $idEmployeeSite ID сайта сотрудника
     * @param int $idCustomer ID клиента
     *
     * @return int ID записи
     */
    public function siteCustomerInsert($idEmployeeSite, $idCustomer) {
        $data = ['EmployeeSiteID' => $idEmployeeSite, 'CustomerID' => $idCustomer];

        $record = $this->db()->get_where(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME, $data)->row_array();

        if ($record) {
            $id = $record['ID'];
            $this->db()->update(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME, ['IsDeleted' => '0'], ['ID' => $id]);
        } else {
            $this->db()->insert(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME, $data);
            $id = $this->db()->insert_id();
        }

        return $id;
    }

    /**
     * Удалить клиента из сайта сотрудника
     *
     * @param int $id ID записи в базе
     */
    public function siteCustomerDelete($id) {
        $this->db()->update(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME, ['IsDeleted' => 1], ['ID' => $id]);
    }

    /**
     * Получить список skype сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function skypeGetList($idEmployee) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_SKYPE_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Добавление skype сотруднику
     *
     * @param int $idEmployee ID сотрудника
     * @param string $skype Skype
     *
     * @return int ID записи
     */
    public function skypeInsert($idEmployee, $skype) {
        $this->db()->insert(self::TABLE_EMPLOYEE_SKYPE_NAME,
            array('EmployeeID' => $idEmployee, 'Skype' => $skype));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о skype
     *
     * @param int $id ID записи
     * @param string $skype Skype
     */
    public function skypeUpdate($id, $skype) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_EMPLOYEE_SKYPE_NAME, array('Skype' => $skype));
    }

    /**
     * Удалить skype сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function skypeDelete($id) {
        $this->db()->delete(self::TABLE_EMPLOYEE_SKYPE_NAME, array('ID' => $id));
    }

    /**
     * Получить список соцсетей сотрудника
     *
     * @param int $idEmployee ID сотрудника
     */
    public function socnetGetList($idEmployee) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_SOCNET_NAME, array('EmployeeID' => $idEmployee))->result_array();
    }

    /**
     * Добавление соцсети сотруднику
     *
     * @param int $idEmployee ID сотрудника
     * @param string $profile Ссылка на профиль соцсети
     *
     * @return int ID записи
     */
    public function socnetInsert($idEmployee, $profile) {
        $this->db()->insert(self::TABLE_EMPLOYEE_SOCNET_NAME,
            array('EmployeeID' => $idEmployee, 'Profile' => $profile));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о соцсети
     *
     * @param int $id ID записи
     * @param string $profile Ссылка на профиль соцсети
     */
    public function socnetUpdate($id, $profile) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_EMPLOYEE_SOCNET_NAME, array('Profile' => $profile));
    }

    /**
     * Удалить соцсеть сотрудника из базы
     *
     * @param int $id ID записи в базе
     */
    public function socnetDelete($id) {
        $this->db()->delete(self::TABLE_EMPLOYEE_SOCNET_NAME, array('ID' => $id));
    }

    /**
     * Авторизация пользователя
     *
     * @param $userLogin string логин пользователя
     * @param $password string пароль пользователя
     * @return bool результат авторизации
     *
     * @throws Exception
     */
    public function userAuthorization($userLogin, $password) {
        $row = $this->db()
            ->select("e.*, CONCAT(img.ID, '.', img.ext) as 'AvatarFileName'")
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left')
            ->where('e.ID', $userLogin)
            ->get()->row_array();

        if(!$row)
            throw new Exception("Не найден указанный пользователь");

        if ($row['Password'] != $password)
            throw new Exception("Указан неверный логин или пароль");

        if (!in_array($row['UserRole'], array(USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY, USER_ROLE_EMPLOYEE, USER_ROLE_TRANSLATE)) )
            throw new Exception("Пользователю не задана роль");

        if ($row['IsDeleted'])
            throw new Exception("Пользователь был удален из системы");

        if ($row['IsBlocked'])
            throw new Exception("Пользователь заблокирован администратором");

        return array(
            'record' => array(
                "ID" => $row['ID'],
                "role" => $row['UserRole'],
                "Avatar" => $row['AvatarFileName'],
                "FName" => $row['FName'],
                "SName" => $row['SName']
            ),
            'errorMessage' => ""
        );
    }

    /**
     * Получить список дней рождения
     *
     * @param int $EmployeeID ID текущего сотрудника
     * @param int $EmployeeRole роль текущего сотрудника
     * @param string|bool $dtBegin начало периода. Если не указано, то выборка за текущий день
     * @param string|bool $dtEnd окончание периода. Если не указано, то выборка за текущий день
     *
     * @return array
     */
    public function getBirthdays($EmployeeID, $EmployeeRole, $dtBegin = false, $dtEnd = false) {
        $this->initBirthdaysQuery($EmployeeID, $EmployeeRole, $dtBegin, $dtEnd);
        return $this->db()->get()->result_array();
    }

    /**
     * Получить количество дней рождения
     *
     * @param int $EmployeeID ID текущего сотрудника
     * @param int $EmployeeRole роль текущего сотрудника
     * @param string|bool $dtBegin начало периода. Если не указано, то выборка за текущий день
     * @param string|bool $dtEnd окончание периода. Если не указано, то выборка за текущий день
     *
     * @return int
     */
    public function getBirthdaysCount($EmployeeID, $EmployeeRole, $dtBegin = false, $dtEnd = false) {
        $this->initBirthdaysQuery($EmployeeID, $EmployeeRole, $dtBegin, $dtEnd);
        return $this->db()->count_all_results();
    }

    private function initBirthdaysQuery($EmployeeID, $EmployeeRole, $dtBegin = false, $dtEnd = false) {
        $this->db()
            ->select('e.ID, e.DOB, e.FName, e.SName')
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->where('e.ID !=', $EmployeeID)
            ->where('e.IsDeleted', 0);

        if (!empty($dtBegin) && !empty($dtEnd)) {
            $startYear = (new DateTime($dtBegin))->format('Y');
            $endYear = (new DateTime($dtEnd))->format('Y');

            $this->db()->where(
                "(IF((MONTH('$dtBegin') < MONTH('$dtEnd')) OR ((MONTH('$dtBegin') = MONTH('$dtEnd')) AND ('$dtBegin' <= '$dtEnd')),
                        DATE_FORMAT(e.`DOB`, '%m-%d') BETWEEN DATE_FORMAT('$dtBegin', '%m-%d') AND DATE_FORMAT('$dtEnd', '%m-%d'),
                        (
                            (DATE_FORMAT(e.`DOB`, '%m-%d') BETWEEN DATE_FORMAT('$dtBegin', '%m-%d') AND DATE_FORMAT(CONCAT('$startYear', '-12-31'), '%m-%d'))
                                OR
                            (DATE_FORMAT(e.`DOB`, '%m-%d') BETWEEN DATE_FORMAT(CONCAT('$endYear', '-01-01'), '%m-%d') AND DATE_FORMAT('$dtEnd', '%m-%d'))
                        )
                ) = 1)", NULL, FALSE);
        } else {
            $this->db()->where("DATE_FORMAT(e.`DOB`,'%m-%d')", "DATE_FORMAT(NOW(),'%m-%d')", FALSE);
        }

        // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
        if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$EmployeeID AND rgt.TargetEmployeeID=e.ID", 'left')
                ->group_start()
                    ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                    ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                ->group_end()
                ->group_by('e.ID');
        }
    }

    /**
     * Получить список переводчиков
     */
    public function employeeTranslatorGetList() {
        return $this->db()
            ->order_by('SName, FName', 'ASC')
            ->get_where(self::TABLE_EMPLOYEE_NAME, ['UserRole' => USER_ROLE_TRANSLATE, 'IsDeleted' => 0])
            ->result_array();
    }

    /**
     * Получить список переводчиков на сайте
     * @param int $siteID сайт
     * @return
     */
    public function findTranslatorBySite($siteID) {
        return $this->db()
            ->select('e.*')
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es',
                'e.ID = es.EmployeeID AND es.IsDeleted = 0 AND es.SiteID = ' . $siteID, 'inner')
            ->where('e.UserRole', USER_ROLE_TRANSLATE)
            ->where('e.IsDeleted', 0)
            ->order_by('e.SName, e.FName', 'ASC')
            ->get()->result_array();
    }

    /**
     * Получить список сотрудников указанных ролей
     *
     * @param int $idEmployee ID сотрудника для исключения из выборки
     * @param array $roles список ролей для выборки
     *
     * @return
     */
    public function employeeGetFilterRoleList($idEmployee, $roles) {
        return $this->db()
            ->from(self::TABLE_EMPLOYEE_NAME)
            ->where_in('UserRole', $roles)
            ->where('ID !=', $idEmployee)
            ->where('IsDeleted', 0)
            ->order_by('SName, FName', 'ASC')
            ->get()
            ->result_array();
    }

    public function findEmployeeBySite($siteID) {
        return $this->db()
            ->select('e.ID, e.FName, e.SName')
            ->from(self::TABLE_EMPLOYEE_NAME.' AS e')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'e.ID = es.EmployeeID AND es.IsDeleted = 0 AND es.SiteID='.$siteID, 'inner')
            ->where('e.IsDeleted', 0)
            ->where('e.IsBlocked', 0)
            ->order_by('e.SName, e.FName', 'ASC')
            ->get()->result_array();
    }

    /**
     * Обновить временную метку онлайна для указанного пользователя
     *
     * @param int $employee ID сотрудника
     */
    public function onlineUpdate($employee) {
        $status = $this->db()->get_where(self::TABLE_EMPLOYEE_ONLINE_NAME, ['EmployeeID' => $employee])->row_array();

        $this->db()->set('DateOnline', 'NOW()', FALSE);

        if ($status)
            $this->db()->update(self::TABLE_EMPLOYEE_ONLINE_NAME, null, ['EmployeeID' => $employee]);
        else
            $this->db()->insert(self::TABLE_EMPLOYEE_ONLINE_NAME, ['EmployeeID' => $employee]);
    }

    public function onlineGetList() {
        return $this->db()
            ->select('EmployeeID as id', false)
            ->from(self::TABLE_EMPLOYEE_ONLINE_NAME)
            ->where('DateOnline > DATE_ADD(NOW(), INTERVAL -10 SECOND)', null, false)
            ->get()->result_array();
    }

    var $fields = [
        'SName' => 'Фамилия',
        'FName' => 'Имя',
        'MName' => 'Отчество',
        'DOB' => 'Дата рождения',
        'Avatar' => 'Аватар',
        'City' => 'Город',
        'Country' => 'Страна',
        'Forming' => 'Образование',
        'MaritalStatus' => 'Семейное положение',
        'FormingFormStudy' => 'Форма обучения',
        'FormingNameInstitution' => 'Название заведения',
        'FormingFaculty' => 'Факультет',
        'Smoking' => 'Курение',
        'WorkOccupation' => 'Последнее место работы',
        'WorkReasonLeaving' => 'Причина увольнения',
        'WorkLatestDirector' => 'Директор последней работы',
        'HomeAddress' => 'Домашний адрес',
        'NameSatellite' => 'ФИО спутника',
        'NameFather' => 'ФИО отца',
        'NameMother' => 'ФИО матери',
        'IsBlocked' => 'Флаг блокировки',
        'IsDeleted' => 'Флаг удаления',
        'ReasonForDeleted' => 'Причина удаления',
        'OccupationSatellite' => 'Род деятельности спутника',
        'OccupationFather' => 'Род деятельности отца',
        'OccupationMother' => 'Род деятельности матери',
        'VideoConfirm' => 'Видео',
        'Login' => 'Логин',
        'Password' => 'Пароль',
        'UserRole' => 'Должность',
        'Agreement' => 'Договора',
        'Children' => 'Дети',
        'Email' => 'E-Mail',
        'Passport' => 'Сканы паспорта',
        'Phone' => 'Телефон',
        'Relative' => 'Брат / Сестра',
        'Site' => 'Сайты',
        'Skype' => 'Skype',
        'Socnet' => 'Соцсети'
    ];

    public function rightsGetList($EmployeeID) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_RIGHTS_NAME, array('EmployeeID' => $EmployeeID))->result_array();
    }

    public function rightsGet($EmployeeID, $TargetEmployeeID) {
        return $this->db()->get_where(self::TABLE_EMPLOYEE_RIGHTS_NAME, array('EmployeeID' => $EmployeeID, 'TargetEmployeeID' => $TargetEmployeeID))->row_array();
    }

    public function rightsInsert($EmployeeID, $TargetEmployeeID) {
        $record = $this->rightsGet($EmployeeID, $TargetEmployeeID);

        if (empty($record)) {
            $this->db()->insert(self::TABLE_EMPLOYEE_RIGHTS_NAME, array('EmployeeID' => $EmployeeID, 'TargetEmployeeID' => $TargetEmployeeID));

            // Связка в обе стороны
            $this->rightsInsert($TargetEmployeeID, $EmployeeID);
        }
    }

    /**
     * Удаления прав, которые не указаны в списке $TargetEmployees
     *
     * @param int $EmployeeID пользователь
     * @param array $TargetEmployees новый список пользователей
     */
    public function rightsRemove($EmployeeID, $TargetEmployees) {
        // Выбираем записи для удаления
        $records = $this->db()
            ->where('EmployeeID', $EmployeeID)
            ->where_not_in('TargetEmployeeID', $TargetEmployees)
            ->get(self::TABLE_EMPLOYEE_RIGHTS_NAME)->result_array();

        // Удаление связок в обе стороны
        foreach ($records as $cross) {
            $this->db()->delete(self::TABLE_EMPLOYEE_RIGHTS_NAME, ['EmployeeID' => $cross['TargetEmployeeID'], 'TargetEmployeeID' => $cross['EmployeeID']]);
            $this->db()->delete(self::TABLE_EMPLOYEE_RIGHTS_NAME, ['EmployeeID' => $cross['EmployeeID'], 'TargetEmployeeID' => $cross['TargetEmployeeID']]);
        }
    }

    public function agreementList($limit = 5, $offset = 0) {
        return $this->db()
            ->from(self::TABLE_EMPLOYEE_AGREEMENT_NAME)
            ->limit($limit, $offset)
            ->get()->result_array();
    }

    public function passportList($limit, $offset) {
        return $this->db()
            ->from(self::TABLE_EMPLOYEE_PASSPORT_SCAN_NAME)
            ->limit($limit, $offset)
            ->get()->result_array();
    }

    private function getFieldDescription($field) {
        return $this->fields[$field];
    }

    public function employeeUpdateNote($author, $EmployeeID, $fields) {
        // Сохранение информации по полям
        foreach ($fields as $field) {
            $this->db()->insert(self::TABLE_EMPLOYEE_HISTORY_NAME,
                ['employee' => $EmployeeID, 'author' => $author, 'field' => $field]);
        }
        
        // Получаем историю обновления профиля (массив полей)
        $updateFields = $this->session->userdata('UpdateEmployeeFields');

        // Объеденение пришедших полей с историей и чистка дубликатов
        $updateFields = array_unique(array_merge($fields, $updateFields));

        // Обновляем примечание у сотрудника
        $this->employeeUpdate($EmployeeID, ['Note' => implode(', ', array_map([$this, 'getFieldDescription'], $updateFields))]);

        // Устанавливаем новое значение истории последних правок профиля
        $this->session->set_userdata(['UpdateEmployeeFields' => $updateFields]);
    }

    /**
     * Черный список сотрудников – ID заблокированных и удалённых
     * @return array
     */
    public function getBlackList()
    {
        $return = array();
        $res = $this->db()
            ->distinct()
            ->select('ID')
            ->from(self::TABLE_EMPLOYEE_NAME)
            ->where('IsBlocked', 1)
            ->or_where('IsDeleted', 1)
            ->get()->result_array();
        if(!empty($res)){
            foreach ($res as $row) {
                $return[] = $row['ID'];
            }
        }
        return $return;
    }

}