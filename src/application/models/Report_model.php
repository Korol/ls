<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с отчетами
 */
class Report_model extends MY_Model {

    private $table_report_daily =
        "CREATE TABLE IF NOT EXISTS `assol_report_daily` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeSiteCustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер привязки к сайту сотрудника',
            `date` DATE NOT NULL COMMENT 'Дата отчетных данных',
            `emails` INT(11) NOT NULL COMMENT 'Количество писем',
            `chat` INT(11) NOT NULL COMMENT 'Количество сообщений в чате',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeSiteCustomerID`) REFERENCES `assol_employee_site_customer` (`ID`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Ежедневный отчет - Assol';";

    private $table_report_lovestory_mount =
        "CREATE TABLE IF NOT EXISTS `assol_report_lovestory_mount` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeSiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта сотрудника',
            `date` DATE NOT NULL COMMENT 'Дата отчетных данных',
            `emails` DECIMAL(10,2) NOT NULL COMMENT 'Количество писем',
            `chat` DECIMAL(10,2) NOT NULL COMMENT 'Количество сообщений в чате',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeSiteID`) REFERENCES `assol_employee_site` (`ID`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Ежемесячный отчет - LoveStory';";

    private $table_report_lovestory_mount_plan =
        "CREATE TABLE IF NOT EXISTS `assol_report_lovestory_mount_plan` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeSiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта сотрудника',
            `year` INT(11) NOT NULL COMMENT 'Год отчетных данных',
            `month` INT(11) NOT NULL COMMENT 'Месяц отчетных данных',
            `emails` DECIMAL(10,2) NOT NULL COMMENT 'Количество писем',
            `chat` DECIMAL(10,2) NOT NULL COMMENT 'Количество сообщений в чате',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeSiteID`) REFERENCES `assol_employee_site` (`ID`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Ежемесячный отчет: план - LoveStory';";

    private $table_report_lovestory_mount_plan_agency =
        "CREATE TABLE IF NOT EXISTS `assol_report_lovestory_mount_plan_agency` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Переводчик',
            `year` INT(11) NOT NULL COMMENT 'Год отчетных данных',
            `month` INT(11) NOT NULL COMMENT 'Месяц отчетных данных',
            `value` DECIMAL(10,2) NOT NULL COMMENT 'Общее количество (письма + чат)',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Ежемесячный отчет: план агенства - LoveStory';";

    private $table_report_mailing =
        "CREATE TABLE IF NOT EXISTS `assol_report_mailing` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeSiteCustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер привязки к сайту сотрудника',
            `date` DATE NOT NULL COMMENT 'Дата отчетных данных',
            `value` INT(11) NOT NULL COMMENT 'Значение',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeSiteCustomerID`) REFERENCES `assol_employee_site_customer` (`ID`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Отчет по рассылке';";

    private $table_report_mailing_info =
        "CREATE TABLE IF NOT EXISTS `assol_report_mailing_info` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeSiteCustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер привязки к сайту сотрудника',
            `year` INT(11) NOT NULL COMMENT 'Год отчетных данных',
            `month` INT(11) NOT NULL COMMENT 'Месяц отчетных данных',
            `id-info` INT(11) NOT NULL COMMENT 'ID',
            `age-info` VARCHAR(64) NOT NULL COMMENT 'Возраст',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeSiteCustomerID`) REFERENCES `assol_employee_site_customer` (`ID`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Отчет по рассылке - информация об ID и возрасте';";

    private $table_report_correspondence =
        "CREATE TABLE IF NOT EXISTS `assol_report_correspondence` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CorrespondenceInfoID` INT(11) NOT NULL COMMENT 'Уникальный номер строки отчета по прописке',
            `date` DATE NOT NULL COMMENT 'Дата отчетных данных',
            `value` VARCHAR(8) NOT NULL COMMENT 'Значение',
            PRIMARY KEY (`id`),
            FOREIGN KEY (`CorrespondenceInfoID`) REFERENCES `assol_report_correspondence_info` (`id`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Отчет по переписке';";

    private $table_report_correspondence_info =
        "CREATE TABLE IF NOT EXISTS `assol_report_correspondence_info` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeSiteCustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер привязки к сайту сотрудника',
            `year` INT(11) NOT NULL COMMENT 'Год отчетных данных',
            `month` INT(11) NOT NULL COMMENT 'Месяц отчетных данных',
            `id-info` INT(11) NOT NULL COMMENT 'ID',
            `men-info` VARCHAR(512) NOT NULL COMMENT 'Мужчина ФИО',
            `id-men-info` INT(11) NOT NULL COMMENT 'ID мужчины',
            `order-info` INT(11) NULL DEFAULT NULL COMMENT 'Порядок сортировки',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Отчет по переписке - информация об ID и мужчине';";

    private $table_report_salary =
        "CREATE TABLE IF NOT EXISTS `assol_report_salary` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeSiteID` INT(11) NOT NULL COMMENT 'Уникальный номер рабочего сайта сотрудника',
            `year` INT(11) NOT NULL COMMENT 'Год отчетных данных',
            `month` INT(11) NOT NULL COMMENT 'Месяц отчетных данных',
            `emailCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество писем',
            `emailAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по письмам',
            `chatCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество сообщений в чате',
            `chatAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по сообщениям в чате',
            `deliveryCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество доставок',
            `deliveryAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по доставкам',
            `dealerCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество дилерских',
            `dealerAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма дилерских',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeSiteID`) REFERENCES `assol_employee_site` (`ID`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Ежедневный отчет';";

    private $table_report_overlay_salary =
        "CREATE TABLE IF NOT EXISTS `assol_report_overlay_salary` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный ID сотрудника',
            `year` INT(11) NOT NULL COMMENT 'Год отчетных данных',
            `month` INT(11) NOT NULL COMMENT 'Месяц отчетных данных',
            `emailCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество писем',
            `emailCountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Количество писем - оригинальное значение присланное переводчиком',
            `emailAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по письмам',
            `emailAmountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по письмам - оригинальное значение присланное переводчиком',
            `chatCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество сообщений в чате',
            `chatCountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Количество сообщений в чате - оригинальное значение присланное переводчиком',
            `chatAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по сообщениям в чате',
            `chatAmountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по сообщениям в чате - оригинальное значение присланное переводчиком',
            `deliveryCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество доставок',
            `deliveryCountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Количество доставок - оригинальное значение присланное переводчиком',
            `deliveryAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по доставкам',
            `deliveryAmountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Сумма по доставкам - оригинальное значение присланное переводчиком',
            `dealerCount` DECIMAL(10,2) NOT NULL COMMENT 'Количество дилерских',
            `dealerCountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Количество дилерских - оригинальное значение присланное переводчиком',
            `dealerAmount` DECIMAL(10,2) NOT NULL COMMENT 'Сумма дилерских',
            `dealerAmountOriginal` DECIMAL(10,2) NOT NULL COMMENT 'Сумма дилерских - оригинальное значение присланное переводчиком',
            `confirmation` TINYINT(1) DEFAULT 0 COMMENT 'Флаг подтверждения',
            PRIMARY KEY (`ID`),
            UNIQUE KEY `unique_key` (`SiteID`,`EmployeeID`,`year`,`month`),
            FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
            ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сводная зарплатная таблица';";

    private $table_report_general_salary =
        "CREATE TABLE IF NOT EXISTS `assol_report_general_salary` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный ID сотрудника',
            `year` INT(11) NOT NULL COMMENT 'Год отчетных данных',
            `month` INT(11) NOT NULL COMMENT 'Месяц отчетных данных',
            `value` DECIMAL(10,2) NOT NULL COMMENT 'Значение',
            `paid` TINYINT(1) DEFAULT 0 COMMENT 'Флаг подтверждения оплата',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Общая зарплатная таблица';";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table_report_daily);
        $this->db()->query($this->table_report_mailing);
        $this->db()->query($this->table_report_mailing_info);
        $this->db()->query($this->table_report_correspondence_info);
        $this->db()->query($this->table_report_correspondence);
        $this->db()->query($this->table_report_salary);
        $this->db()->query($this->table_report_overlay_salary);
        $this->db()->query($this->table_report_general_salary);
        $this->db()->query($this->table_report_lovestory_mount);
        $this->db()->query($this->table_report_lovestory_mount_plan);
        $this->db()->query($this->table_report_lovestory_mount_plan_agency);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_REPORT_DAILY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_MAILING_INFO_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_MAILING_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_CORRESPONDENCE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_GENERAL_SALARY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_OVERLAY_SALARY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_SALARY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_AGENCY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME, TRUE);
    }

    /**
     * Получить ежедневный отчет
     *
     * @param int $idEmployee ID сотрудника
     * @param string $date дата
     *
     * @return
     */
    public function reportDaily($idEmployee, $date) {
        return $this->db()
            ->select("c.ID as 'CustomerID', es2c.ID as 'es2cID', es2c.EmployeeSiteID, rd.date, rd.emails, rd.chat")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es.EmployeeID = '.$idEmployee.' AND es.IsDeleted = 0 AND es2c.EmployeeSiteID = es.ID', 'inner')
            ->join(self::TABLE_REPORT_DAILY_NAME.' AS rd',
                "rd.EmployeeSiteCustomerID = es2c.ID AND rd.date='".$date."'", 'left')
            ->get()->result_array();
    }

    public function reportDailyFind($dateRecord, $idCross) {
        return $this->db()->get_where(self::TABLE_REPORT_DAILY_NAME, array('date' => $dateRecord, 'EmployeeSiteCustomerID' => $idCross))->row_array();
    }

    public function reportDailyInsert($dateRecord, $idCross, $mails, $chat) {
        $data = [
            'EmployeeSiteCustomerID' => $idCross,
            'date' => $dateRecord
        ];

        if (is_numeric($mails))
            $data['emails'] = $mails;

        if (is_numeric($chat))
            $data['chat'] = $chat;

        $this->db()->insert(self::TABLE_REPORT_DAILY_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportDailyUpdate($idRecord, $mails, $chat) {
        $data = [];

        if (is_numeric($mails))
            $data['emails'] = $mails;

        if (is_numeric($chat))
            $data['chat'] = $chat;

        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_DAILY_NAME, $data);
    }

    /**
     * Получить ежедневный отчет
     *
     * @param int $idEmployee ID сотрудника
     * @param int $year год
     * @param int $month месяц
     *
     * @return
     */
    public function reportLoveStoryMount($idEmployee, $year, $month) {
        return $this->db()
            ->select("es.ID as 'esID', rm.date, rm.emails, rm.chat")
            ->from(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME.' AS rm')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es.EmployeeID = '.$idEmployee.' AND rm.EmployeeSiteID = es.ID AND es.IsDeleted = 0', 'inner')
            ->where("DATE_FORMAT(rm.date, '%Y-%m')='".($year.'-'.$this->normalizeMonth($month))."'", NULL, FALSE)
            ->get()->result_array();
    }

    public function reportLoveStoryMountFind($dateRecord, $idCross) {
        return $this->db()->get_where(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME, array('date' => $dateRecord, 'EmployeeSiteID' => $idCross))->row_array();
    }

    public function reportLoveStoryMountInsert($dateRecord, $idCross, $mails, $chat) {
        $data = [
            'EmployeeSiteID' => $idCross,
            'date' => $dateRecord
        ];

        if (is_numeric($mails))
            $data['emails'] = $mails;

        if (is_numeric($chat))
            $data['chat'] = $chat;

        $this->db()->insert(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportLoveStoryMountUpdate($idRecord, $mails, $chat) {
        $data = [];

        if (is_numeric($mails))
            $data['emails'] = $mails;

        if (is_numeric($chat))
            $data['chat'] = $chat;

        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME, $data);
    }

    /**
     * Получить планы по сайтам для ежемесячного отчета
     *
     * @param int $idEmployee ID сотрудника
     * @param int $year год
     * @param int $month месяц
     *
     * @return
     */
    public function reportLoveStoryMountPlan($idEmployee, $year, $month) {
        return $this->db()
            ->select("es.ID as 'esID', r.emails, r.chat")
            ->from(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_NAME.' AS r')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es.EmployeeID = '.$idEmployee.' AND es.IsDeleted = 0 AND r.EmployeeSiteID = es.ID', 'inner')
            ->where('r.year', $year)
            ->where('r.month', $month)
            ->get()->result_array();
    }

    public function reportLoveStoryMountPlanFind($idEmployeeSite, $year, $month) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_NAME,
                array('year' => $year, 'month' => $month, 'EmployeeSiteID' => $idEmployeeSite))->row_array();
    }

    public function reportLoveStoryMountPlanInsert($year, $month, $idEmployeeSite, $mails, $chat) {
        $data = [
            'EmployeeSiteID' => $idEmployeeSite,
            'year' => $year,
            'month' => $month
        ];

        if (is_numeric($mails))
            $data['emails'] = $mails;

        if (is_numeric($chat))
            $data['chat'] = $chat;

        $this->db()->insert(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportLoveStoryMountPlanUpdate($idRecord, $mails, $chat) {
        $data = [];

        if (is_numeric($mails))
            $data['emails'] = $mails;

        if (is_numeric($chat))
            $data['chat'] = $chat;

        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_NAME, $data);
    }

    /**
     * Получить планы агенства по сайтам для ежемесячного отчета
     *
     * @param int $year год
     * @param int $month месяц
     * @param int $site сайт
     *
     * @return
     */
    public function reportLoveStoryMountPlanAgency($year, $month, $site) {
        return $this->db()
            ->select("EmployeeID, value")
            ->where('year', $year)
            ->where('month', $month)
            ->where('SiteID', $site)
            ->get(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_AGENCY_NAME)->result_array();
    }

    public function reportLoveStoryMountPlanAgencyFind($idSite, $idEmployee, $year, $month) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_AGENCY_NAME,
                array('year' => $year, 'EmployeeID' => $idEmployee, 'month' => $month, 'SiteID' => $idSite))->row_array();
    }

    public function reportLoveStoryMountPlanAgencyInsert($year, $month, $idSite, $idEmployee, $value) {
        $data = [
            'SiteID' => $idSite,
            'EmployeeID' => $idEmployee,
            'year' => $year,
            'month' => $month,
            'value' => $value
        ];

        $this->db()->insert(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_AGENCY_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportLoveStoryMountPlanAgencyUpdate($idRecord, $value) {
        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_AGENCY_NAME, ['value' => $value]);
    }

    private function normalizeMonth($month) {
        if (strlen($month) === 1) {
            $month = '0'.$month;
        }

        return $month;
    }

    public function reportDailyGroupMonth($idEmployee, $year, $month) {
        return $this->db()
            ->select("c.ID as 'CustomerID', es2c.ID as 'es2cID', es2c.EmployeeSiteID, SUM(rd.emails) as 'emails', SUM(rd.chat) as 'chat'")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es.EmployeeID = '.$idEmployee.' AND es.IsDeleted = 0 AND es2c.EmployeeSiteID = es.ID', 'inner')
            ->join(self::TABLE_REPORT_DAILY_NAME.' AS rd',
                "rd.EmployeeSiteCustomerID = es2c.ID AND DATE_FORMAT(rd.date, '%Y-%m')='".($year.'-'.$this->normalizeMonth($month))."'", 'left')
            ->group_by('es2c.ID')
            ->get()->result_array();
    }

    /**
     * Общая таблица по клиентам - за указанное число
     *
     * @param string $date дата
     *
     * @return mixed
     */
    public function reportGeneralOfCustomers($date) {
        return $this->db()
            ->select("c.ID as 'CustomerID', es.SiteID, rd.date, SUM(rd.emails) as emails, SUM(rd.chat) as chat")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0', 'inner')
            ->join(self::TABLE_REPORT_DAILY_NAME.' AS rd',
                "rd.EmployeeSiteCustomerID = es2c.ID AND rd.date='".$date."'", 'left')
            ->group_by('c.ID')
            ->group_by('es.SiteID')
            ->get()->result_array();
    }

    /**
     * Общая таблица по клиентам - за месяц
     *
     * @param int $year год
     * @param int $month месяц
     * @return mixed
     */
    public function reportGeneralOfCustomersGroupMonth($year, $month) {
        return $this->db()
            ->select("c.ID as 'CustomerID', es.SiteID, SUM(rd.emails) as 'emails', SUM(rd.chat) as 'chat'")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0', 'inner')
            ->join(self::TABLE_REPORT_DAILY_NAME.' AS rd',
                "rd.EmployeeSiteCustomerID = es2c.ID AND DATE_FORMAT(rd.date, '%Y-%m')='".($year.'-'.$this->normalizeMonth($month))."'", 'left')
            ->group_by('c.ID')
            ->group_by('es.SiteID')
            ->get()->result_array();
    }

    public function reportMailingFind($dateRecord, $idCross) {
        return $this->db()->get_where(self::TABLE_REPORT_MAILING_NAME, array('date' => $dateRecord, 'EmployeeSiteCustomerID' => $idCross))->row_array();
    }

    public function reportMailingInsert($dateRecord, $idCross, $value) {
        $data = [
            'EmployeeSiteCustomerID' => $idCross,
            'date' => $dateRecord,
            'value' => $value
        ];

        $this->db()->insert(self::TABLE_REPORT_MAILING_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportMailingUpdate($idRecord, $value) {
        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_MAILING_NAME, ['value' => $value]);
    }

    public function reportMailing($idEmployee, $idSite, $year, $month) {
        return $this->db()
            ->select("rm.*")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0 AND es.EmployeeID = '.$idEmployee.' AND es.SiteID = '.$idSite, 'inner')
            ->join(self::TABLE_REPORT_MAILING_NAME.' AS rm',
                "rm.EmployeeSiteCustomerID = es2c.ID AND DATE_FORMAT(rm.date, '%Y-%m')='".($year.'-'.$this->normalizeMonth($month))."'", 'inner')
            ->get()->result_array();
    }

    public function reportMailingInfo($idEmployee, $idSite, $year, $month) {
        return $this->db()
            ->select("rm.*")
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0 AND es.EmployeeID = '.$idEmployee.' AND es.SiteID = '.$idSite, 'inner')
            ->join(self::TABLE_REPORT_MAILING_INFO_NAME.' AS rm',
                "rm.EmployeeSiteCustomerID = es2c.ID AND rm.year=".$year." AND rm.month=".$month, 'inner')
            ->get()->result_array();
    }

    public function reportMailingInfoFind($year, $month, $idCross) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_MAILING_INFO_NAME,
                ['year' => $year, 'month' => $month, 'EmployeeSiteCustomerID' => $idCross]
            )
            ->row_array();
    }

    public function reportMailingInfoInsert($year, $month, $idCross, $id, $age) {
        $data = [
            'EmployeeSiteCustomerID' => $idCross,
            'year' => $year,
            'month' => $month
        ];

        if (is_numeric($id))
            $data['id-info'] = $id;

        if ($age != NULL)
            $data['age-info'] = $age;

        $this->db()->insert(self::TABLE_REPORT_MAILING_INFO_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportMailingInfoUpdate($idRecord, $id, $age) {
        $data = [];

        if (is_numeric($id))
            $data['id-info'] = $id;

        if ($age != NULL)
            $data['age-info'] = $age;

        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_MAILING_INFO_NAME, $data);
    }

    public function reportCorrespondenceInfo($employee, $site, $year, $month) {
        return $this->db()
            ->select("r.*, c.FName, c.SName")
            ->from(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME.' AS r')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c',
                'es2c.ID = r.EmployeeSiteCustomerID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es',
                "es.ID = es2c.EmployeeSiteID AND es.SiteID=$site AND es.EmployeeID=$employee", 'inner')
            ->join(self::TABLE_CUSTOMER_NAME.' AS c',
                'c.ID = es2c.CustomerID', 'left')
            ->where('r.year', $year)
            ->where('r.month', $month)
            ->order_by('r.order-info', 'ASC')
            ->get()->result_array();
    }

    /**
     * Получить запись по ID
     *
     * @param int $idCorrespondenceInfo ID записи в базе
     */
    public function reportCorrespondenceInfoGet($idCorrespondenceInfo) {
        return $this->db()->get_where(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME, array('ID' => $idCorrespondenceInfo))->row_array();
    }

    public function reportCorrespondenceInfoInsert($es2c, $year, $month, $offset) {
        $data = [
            'EmployeeSiteCustomerID' => $es2c,
            'year' => $year,
            'month' => $month
        ];


        // Делаем поиск информации о idEmployee и idSite на основе связки TABLE_EMPLOYEE_SITE_CUSTOMER_NAME
        $record = $this->db()
            ->select('es.*')
            ->from(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es', 'es.ID=es2c.EmployeeSiteID AND es.IsDeleted=0', 'inner')
            ->where('es2c.ID', $es2c)
            ->where('es2c.IsDeleted', 0)->get()->row_array();

        if ($record) {
            // По полученным данным делаем поиск записей
            $records = $this->reportCorrespondenceInfo($record['EmployeeID'], $record['SiteID'], $year, $month);

            // Если записи найдены, то проставляем порядок сортировки
            if ($records) {
                $idNewRecord = false; // ID новой записи
                $order = 0; // Порядок сортировки

                foreach ($records as $record) {
                    // Обновление порядка сортировки для текущей записи
                    $this->db()
                        ->update(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME, ['order-info' => $order++], ['id' => $record['id']]);
                    // Если текущая запись равна $offset для новой строчки
                    if ($record['id'] == $offset) {
                        $data['order-info'] = $order++;
                        $this->db()->insert(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME, $data);
                        $idNewRecord = $this->db()->insert_id();
                    }
                }

                // Если запись была вставлена в список
                if ($idNewRecord) {
                    return $idNewRecord; // Возвращаем ID новой записи
                } else {
                    $data['order-info'] = $order++; // Для новой записи ставим последний ордер
                }
            }
        }

        $this->db()->insert(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportCorrespondenceInfoUpdate($idRecord, $idInfo, $idMenInfo, $menInfo) {
        $data = [];

        if (is_numeric($idInfo))
            $data['id-info'] = $idInfo;

        if (is_numeric($idMenInfo))
            $data['id-men-info'] = $idMenInfo;

        if (!empty($menInfo) || empty($data))
            $data['men-info'] = $menInfo;

        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME, $data);
    }

    public function reportCorrespondence($idEmployee, $idSite, $year, $month) {
        return $this->db()
            ->select("r.*")
            ->from(self::TABLE_REPORT_CORRESPONDENCE_NAME . ' AS r')
            ->join(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME . ' AS info',
                'info.ID = r.CorrespondenceInfoID', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME . ' AS es2c',
                'info.EmployeeSiteCustomerID = es2c.ID AND es2c.IsDeleted=0', 'inner')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                'es.ID = es2c.EmployeeSiteID AND es.IsDeleted = 0 AND es.EmployeeID = '.$idEmployee.' AND es.SiteID = '.$idSite, 'inner')
            -> where("DATE_FORMAT(r.date, '%Y-%m')='".($year.'-'.$this->normalizeMonth($month))."'", NULL, FALSE)
            ->get()->result_array();
    }

    public function reportCorrespondenceFind($dateRecord, $idRecord) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_CORRESPONDENCE_NAME, [
                'date' => $dateRecord, 'CorrespondenceInfoID' => $idRecord])
            ->row_array();
    }

    public function reportCorrespondenceInsert($dateRecord, $idRecord, $value) {
        $data = [
            'CorrespondenceInfoID' => $idRecord,
            'date' => $dateRecord,
            'value' => $value
        ];

        $this->db()->insert(self::TABLE_REPORT_CORRESPONDENCE_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportCorrespondenceUpdate($idRecord, $value) {
        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_CORRESPONDENCE_NAME, ['value' => $value]);
    }

    public function reportCorrespondenceRemove($idRecord) {
        $this->db()->delete(self::TABLE_REPORT_CORRESPONDENCE_NAME, ['CorrespondenceInfoID' => $idRecord]);
        $this->db()->delete(self::TABLE_REPORT_CORRESPONDENCE_INFO_NAME, ['id' => $idRecord]);
    }

    /**
     * Запрос данных "Отчет по зарплате"
     *
     * @param int $employee ID сотрудника
     * @param int $year     год отчетных данных
     * @param int $month    месяц отчетных данных
     *
     * @return mixed
     */
    public function reportSalary($employee, $year, $month) {
        return $this->db()
            ->select('es.ID, es.SiteID, r.emailCount, r.emailAmount, r.chatCount, r.chatAmount,
                        r.deliveryCount, r.deliveryAmount, r.dealerCount, r.dealerAmount, s.IsDealer')
            ->from(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es')
            ->join(self::TABLE_REPORT_SALARY_NAME . ' AS r',
                'es.ID = r.EmployeeSiteID AND r.year='.$year.' AND r.month='.$month, 'left')
            ->join(self::TABLE_SITE_NAME . ' AS s', 'es.SiteID = s.ID', 'left')
            ->where('es.EmployeeID', $employee)
            ->where('es.IsDeleted', 0)
            ->order_by('s.Name', 'ASC')
            ->get()->result_array();
    }

    public function reportSalaryFind($year, $month, $idEmployeeSite) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_SALARY_NAME,
                ['year' => $year, 'month' => $month, 'EmployeeSiteID' => $idEmployeeSite]
            )
            ->row_array();
    }

    public function reportSalaryInsert($year, $month, $idEmployeeSite, $type, $value) {
        $data = [
            'EmployeeSiteID' => $idEmployeeSite,
            'year' => $year,
            'month' => $month,
            $type => $value
        ];

        $this->db()->insert(self::TABLE_REPORT_SALARY_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportSalaryUpdate($idRecord, $type, $value) {
        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_SALARY_NAME, [$type => $value]);
    }

    /**
     * @param $employee
     * @param $year
     * @param $month
     * @return mixed
     */
    public function reportSalaryClose($employee, $year, $month) {
        $salary = $this->db()
            ->select("es.SiteID, r.emailCount, r.emailAmount, r.chatCount,
                        r.chatAmount, r.deliveryCount, r.deliveryAmount, r.dealerCount, r.dealerAmount")
            ->from(self::TABLE_REPORT_SALARY_NAME . ' AS r')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es',
                'es.EmployeeID = '.$employee.' AND es.IsDeleted = 0 AND es.ID = r.EmployeeSiteID', 'inner')
            ->where('r.year', $year)
            ->where('r.month', $month)
            ->get()->result_array();

        // Открываем транзакцию и добавляем записи
        $this->db()->trans_start();
        foreach ($salary as $row) {
            // Собираем информацию об оригинальных значениях
            $salaryOriginal = [];
            foreach ($row as $key => $value) {
                if ($key == 'SiteID') continue;
                $salaryOriginal[$key.'Original'] = $value;
            }

            $data = array_merge($row, $salaryOriginal, [
                'EmployeeID' => $employee,
                'year' => $year,
                'month' => $month,
            ]);

            $record = $this->reportOverlaySalaryFind($employee, $row['SiteID'], $year, $month);

            if (IS_LOVE_STORY) {
                if (empty($record)) {
                    $this->db()->insert(self::TABLE_REPORT_OVERLAY_SALARY_NAME, $data);
                } else {
                    // Перед сохранением проверяем что директор уже не подтвердил сумму
                    $generalSalary = $this->reportGeneralSalaryFind($employee, $row['SiteID'], $year, $month);

                    if (empty($generalSalary)) {
                        $this->db()->where('id', $record['id']);
                        $this->db()->update(self::TABLE_REPORT_OVERLAY_SALARY_NAME, $data);
                    }
                }
            } else {
                $this->db()->insert(self::TABLE_REPORT_OVERLAY_SALARY_NAME, $data);
            }
        }
        $this->db()->trans_complete();

        // Кидаем исключение, если транзакция была нарушена
        if ($this->db()->trans_status() === FALSE)
            throw new RuntimeException('Данные уже отправлены в сводную таблицу');

        return $this->db()->insert_id();
    }

    public function reportOverlaySalary($siteID, $year, $month) {
        return $this->db()
            ->select("r.id, r.emailCount, r.emailAmount, r.chatCount, r.chatAmount, r.deliveryCount, r.deliveryAmount,
                r.dealerCount, r.dealerAmount, r.emailCountOriginal, r.emailAmountOriginal, r.chatCountOriginal,
                r.chatAmountOriginal, r.deliveryCountOriginal, r.deliveryAmountOriginal, r.dealerCountOriginal,
                r.dealerAmountOriginal, r.confirmation, e.ID AS 'EmployeeID', e.FName, e.SName, s.IsDealer, es.ID as 'cross'")
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es', 'es.EmployeeID=e.ID AND es.IsDeleted=0 AND es.SiteID='.$siteID, 'left')
            ->join(self::TABLE_REPORT_OVERLAY_SALARY_NAME . ' AS r',
                'e.ID = r.EmployeeID AND r.SiteID='.$siteID.' AND r.year='.$year.' AND r.month='.$month, 'left')
            ->join(self::TABLE_SITE_NAME . ' AS s', 's.ID='.$siteID, 'left')
            ->where('e.UserRole', USER_ROLE_TRANSLATE)
            ->where('e.IsDeleted', 0)
            ->order_by('e.SName, e.FName', 'ASC')
            ->group_by('e.id')
            ->get()->result_array();
    }

    public function reportOverlaySalaryFind($employee, $idSite, $year, $month) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_OVERLAY_SALARY_NAME,
                ['year' => $year, 'month' => $month, 'EmployeeID' => $employee, 'SiteID' => $idSite]
            )
            ->row_array();
    }

    public function reportOverlaySalaryInsert($idSite, $employee, $year, $month, $type, $value) {
        $data = [
            'SiteID' => $idSite,
            'EmployeeID' => $employee,
            'year' => $year,
            'month' => $month,
            $type => $value,
        ];

        return $this->db()->insert(self::TABLE_REPORT_OVERLAY_SALARY_NAME, $data);
    }

    public function reportOverlaySalaryUpdate($idRecord, $type, $value) {
        // Перед сохранением проверяем что директор уже не подтвердил сумму
        $row = $this->db()->get_where(self::TABLE_REPORT_OVERLAY_SALARY_NAME, ['id' => $idRecord])->row_array();
        $generalSalary = $this->reportGeneralSalaryFind($row['EmployeeID'], $row['SiteID'], $row['year'], $row['month']);

        $data = [$type => $value];

        if (!empty($generalSalary)) {
            // Сброс флага подтверждения в отчете "Сводная зарплатная таблица"
            $data['confirmation'] = 0;
        }

        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_OVERLAY_SALARY_NAME, $data);
    }

    public function reportOverlaySalaryClose($idRecord) {
        $record = $this->db()
            ->get_where(self::TABLE_REPORT_OVERLAY_SALARY_NAME, ['id' => $idRecord])
            ->row_array();

        $generalSalary = $this->reportGeneralSalaryFind($record['EmployeeID'], $record['SiteID'], $record['year'], $record['month']);
        if (!empty($generalSalary)) {
            // Обновление данных в отчете "Общая зарплатная таблица" и сброс флага оплаты
            $this->db()->where('id', $generalSalary['id']);
            $this->db()->update(self::TABLE_REPORT_GENERAL_SALARY_NAME,
                ['paid' => 0, 'value' => $record['emailAmount'] + $record['chatAmount'] + $record['deliveryAmount'] + $record['dealerAmount']]);
        } else {
            $data = [
                'SiteID' => $record['SiteID'],
                'EmployeeID' => $record['EmployeeID'],
                'year' => $record['year'],
                'month' => $record['month'],
                'value' => $record['emailAmount'] + $record['chatAmount'] + $record['deliveryAmount'] + $record['dealerAmount']
            ];

            $this->db()->insert(self::TABLE_REPORT_GENERAL_SALARY_NAME, $data);
        }

        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_OVERLAY_SALARY_NAME, ['confirmation' => 1]);
    }

    public function reportGeneralSalary($year, $month) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_GENERAL_SALARY_NAME, ['year' => $year, 'month' => $month])
            ->result_array();
    }

    public function reportGeneralSalaryFind($idEmployee, $idSite, $year, $month) {
        return $this->db()
            ->get_where(self::TABLE_REPORT_GENERAL_SALARY_NAME,
                ['year' => $year, 'month' => $month, 'EmployeeID' => $idEmployee, 'SiteID' => $idSite]
            )
            ->row_array();
    }

    public function reportGeneralSalaryInsert($idEmployee, $idSite, $year, $month, $value) {
        $data = [
            'EmployeeID' => 0,
            'SiteID' => $idSite,
            'year' => $year,
            'month' => $month,
            'value' => $value
        ];

        $this->db()->insert(self::TABLE_REPORT_GENERAL_SALARY_NAME, $data);

        return $this->db()->insert_id();
    }

    public function reportGeneralSalaryUpdate($idRecord, $value) {
        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_GENERAL_SALARY_NAME, ['value' => $value]);
    }

    public function reportGeneralSalaryUpdatePaid($idRecord, $paid) {
        $this->db()->where('id', $idRecord);
        $this->db()->update(self::TABLE_REPORT_GENERAL_SALARY_NAME, ['paid' => $paid]);
    }

    public function reportApprovedSalary($employee, $year, $month) {
        return $this->db()
            ->select('r.*')
            ->from(self::TABLE_REPORT_GENERAL_SALARY_NAME . ' AS r')
            ->join(self::TABLE_SITE_NAME . ' AS s', 'r.SiteID = s.ID', 'left')
            ->where('r.EmployeeID', $employee)
            ->where('r.year', $year)
            ->where('r.month', $month)
            ->order_by('s.Name', 'ASC')
            ->get()->result_array();
    }

    public function isExistMountReport($employee, $day) {
        return $this->db()
            ->from(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME . ' AS rm')
            ->where("rm.date > DATE_ADD(NOW(), INTERVAL -$day DAY)", null, false)
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es',
                'es.ID = rm.EmployeeSiteID AND es.IsDeleted = 0 AND es.EmployeeID='.$employee, 'inner')
            ->count_all_results() > 0;
    }

    /**
     * Группировка планов в разрезе пользователей
     *
     * @param int $year год
     * @param int $month месяц
     * @param int $site сайт
     */
    public function reportLoveStoryMountGeneralPlan($year, $month, $site) {
        return $this->db()
            ->select("e.ID as 'EmployeeID', SUM(rmp.emails) as 'emails', SUM(rmp.chat) as 'chat', (SUM(rmp.emails)+SUM(rmp.chat)) as 'plan'")
            ->from(self::TABLE_REPORT_LOVESTORY_MOUNT_PLAN_NAME . ' AS rmp')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es', 'es.ID = rmp.EmployeeSiteID AND es.IsDeleted = 0 AND es.SiteID='.$site, 'inner')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = es.EmployeeID', 'inner')
            ->where('rmp.year', $year)
            ->where('rmp.month', $month)
            ->group_by('e.ID')
            ->get()->result_array();
    }

    /**
     * Группировка данных ежемесячного отчета в разрезе пользователей и сайте
     *
     * @param int $year год
     * @param int $month месяц
     * @param int $site сайт
     */
    public function reportLoveStoryMountGeneralGroup($year, $month, $site) {
        return $this->db()
            ->select("e.ID as 'EmployeeID', rm.date, rm.emails, rm.chat")
            ->from(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME . ' AS rm')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es', 'es.ID = rm.EmployeeSiteID AND es.IsDeleted = 0 AND es.SiteID='.$site, 'inner')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = es.EmployeeID', 'inner')
            ->where("DATE_FORMAT(rm.date, '%Y-%m')='".($year.'-'.$this->normalizeMonth($month))."'", NULL, FALSE)
            ->get()->result_array();
    }

    /**
     * Группировка данных ежемесячного отчета за указанный день в разрезе пользователей
     *
     * @param $date
     */
    public function reportLoveStoryMountGeneral($date) {
        return $this->db()
            ->select("e.ID as 'EmployeeID', es.SiteID, SUM(rm.emails) as 'emails', SUM(rm.chat) as 'chat'")
            ->from(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME . ' AS rm')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es', 'es.ID = rm.EmployeeSiteID AND es.IsDeleted = 0', 'inner')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = es.EmployeeID', 'inner')
            ->where('rm.date', $date)
            ->group_by('e.ID, es.ID')
            ->get()->result_array();
    }

    /**
     * Группировка данных ежемесячного отчета в разрезе пользователей
     *
     * @param int $year год
     * @param int $month месяц
     * @param int $site сайт
     */
    public function reportLoveStoryMountGeneralTotal($year, $month, $site) {
        return $this->db()
            ->select("e.ID as 'EmployeeID', (SUM(rm.emails) + SUM(rm.chat)) as 'total'")
            ->from(self::TABLE_REPORT_LOVESTORY_MOUNT_NAME . ' AS rm')
            ->join(self::TABLE_EMPLOYEE_SITE_NAME . ' AS es', 'es.ID = rm.EmployeeSiteID AND es.IsDeleted = 0 AND es.SiteID='.$site, 'inner')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = es.EmployeeID', 'inner')
            ->where("DATE_FORMAT(rm.date, '%Y-%m')='".($year.'-'.$this->normalizeMonth($month))."'", NULL, FALSE)
            ->group_by('e.ID')
            ->get()->result_array();
    }

}