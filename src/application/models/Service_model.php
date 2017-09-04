<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с услугами
 */
class Service_model extends MY_Model {

    private $table_service_western =
        "CREATE TABLE IF NOT EXISTS `assol_service_western` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Date` DATE NOT NULL COMMENT 'Дата',
            `Girl` VARCHAR(512) NOT NULL COMMENT 'Девушка',
            `Men` VARCHAR(512) NOT NULL COMMENT 'Мужчина',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `Sum` VARCHAR(128) NOT NULL COMMENT 'Сумма',
            `Code` VARCHAR(64) NOT NULL COMMENT 'Код',
            `IsSend` TINYINT(1) DEFAULT 0 COMMENT 'Флаг отсылки / % Кли-ки',
            `IsPer` TINYINT(1) DEFAULT 0 COMMENT '% Пер-ка',
            `IsDone` TINYINT(1) DEFAULT 0 COMMENT 'Флаг выполнения',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Услуги - вестерны';";

    private $table_service_meeting =
        "CREATE TABLE IF NOT EXISTS `assol_service_meeting` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `DateIn` DATE NOT NULL COMMENT 'Дата приезда',
            `DateOut` DATE NOT NULL COMMENT 'Дата отъезда',
            `Girl` VARCHAR(512) NOT NULL COMMENT 'Девушка',
            `Men` VARCHAR(512) NOT NULL COMMENT 'Мужчина',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `UserTranslateID` INT(11) NULL COMMENT 'Уникальный номер переводчика - для Love story',
            `UserTranslateOrganizer` VARCHAR(512) NOT NULL COMMENT 'Переводчик организатор - для Love story',
            `UserTranslateDuring` VARCHAR(512) NOT NULL COMMENT 'Переводчик во время встречи - для Love story',
            `UserTranslate` VARCHAR(512) NOT NULL COMMENT 'Переводчик - для Assol',
            `City` VARCHAR(512) NOT NULL COMMENT 'Город',
            `Transfer` VARCHAR(512) NOT NULL COMMENT 'Трансфер',
            `Housing` VARCHAR(512) NOT NULL COMMENT 'Жилье',
            `Translate` VARCHAR(512) NOT NULL COMMENT 'Перевод',
            `IsDone` TINYINT(1) DEFAULT 0 COMMENT 'Флаг выполнения',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Услуги - встречи';";

    private $table_service_delivery =
        "CREATE TABLE IF NOT EXISTS `assol_service_delivery` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            `Date` DATE NOT NULL COMMENT 'Дата',
            `Girl` VARCHAR(512) NOT NULL COMMENT 'Девушка',
            `Men` VARCHAR(512) NOT NULL COMMENT 'Мужчина',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `UserTranslateID` INT(11) NULL COMMENT 'Уникальный номер переводчика - для Love story',
            `Delivery` VARCHAR(512) NOT NULL COMMENT 'Доставка',
            `Gratitude` VARCHAR(512) NOT NULL COMMENT 'Благодарность',
            `IsDone` TINYINT(1) DEFAULT 0 COMMENT 'Флаг выполнения',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Услуги - доставки';";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table_service_western);
        $this->db()->query($this->table_service_meeting);
        $this->db()->query($this->table_service_delivery);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_SERVICE_WESTERN_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_SERVICE_MEETING_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_SERVICE_DELIVERY_NAME, TRUE);
    }

    /**
     * Получить количество непрочитанных услуг
     */
    public function getCountUnreadService($isAdmin, $userTranslateID) {
        $count = 0;

        if (IS_LOVE_STORY) {
            if ($isAdmin) {
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_WESTERN_NAME)
                    ->where('IsDone', 0)
                    ->count_all_results();
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_MEETING_NAME)
                    ->where('IsDone', 0)
                    ->count_all_results();
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_DELIVERY_NAME)
                    ->where('IsDone', 0)
                    ->count_all_results();
            } else {
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_MEETING_NAME)
                    ->where('UserTranslateID', $userTranslateID)
                    ->where('IsDone', 0)
                    ->count_all_results();

                $count += $this->db()
                    ->from(self::TABLE_SERVICE_DELIVERY_NAME)
                    ->where('UserTranslateID', $userTranslateID)
                    ->where('IsDone', 0)
                    ->count_all_results();
            }
        } else {
            if ($isAdmin) {
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_WESTERN_NAME)
                    ->where('IsDone', 0)
                    ->count_all_results();
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_MEETING_NAME)
                    ->where('IsDone', 0)
                    ->count_all_results();
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_DELIVERY_NAME)
                    ->where('IsDone', 0)
                    ->count_all_results();
            } else {
                $count += $this->db()
                    ->from(self::TABLE_SERVICE_DELIVERY_NAME)
                    ->where('UserTranslateID', $userTranslateID)
                    ->where('IsDone', 0)
                    ->count_all_results();
            }
        }

        return $count;
    }

    /**
     * Поиск услуги "Вестерн"
     *
     * @param int    $idEmployee ID сотрудника
     * @param string $start      начало периода
     * @param string $end        окончание периода
     * @param string $isDirector флаг директора. Для директора выбираются все не выполненные услуги, если не указаны временные интервалы
     *
     * @return array список услуг
     */
    public function westernGetList($idEmployee, $start, $end, $isDirector) {
        if (!empty($idEmployee))
            $this->db()->where('EmployeeID', $idEmployee);

        $this->db()->group_start();

        if (!empty($start)) {
            if (!empty($end)) {
                $this->db()->where("`Date` BETWEEN '$start' AND '$end'", NULL, FALSE);
            } else {
                $this->db()->where('Date', $start);
            }
        } else {
            if (IS_LOVE_STORY) {
                $this->db()
                    ->group_start()
                        ->where('IsDone', 0)
                        ->or_where("`Date` = DATE_FORMAT(NOW(),'%Y-%m-%d')", NULL, FALSE)
                    ->group_end();
            } else {
                if ($isDirector) {
                    $this->db()->where('IsDone', 0);
                } else {
                    $this->db()->where("`Date` = DATE_FORMAT(NOW(),'%Y-%m-%d')", NULL, FALSE);
                }
            }
        }

        $this->db()->group_end();

        return $this->db()->get(self::TABLE_SERVICE_WESTERN_NAME)->result_array();
    }

    /**
     * Полученить указанный вестерн
     *
     * @param string $id ID записи
     */
    public function westernGet($id) {
        return $this->db()->get_where(self::TABLE_SERVICE_WESTERN_NAME, ['ID' => $id])->row_array();
    }

    /**
     * Добавление нового вестерна
     *
     * @param int       $employeeID ID сотрудника
     * @param string    $date       дата
     * @param string    $girl       девушка
     * @param string    $men        мужчина
     * @param int       $site       ID сайта
     * @param float     $sum        сумма
     * @param string    $code       код
     * @param int       $isSend     флаг "выслали / % Кли-ки"
     * @param int       $isPer      флаг "% Пер-ка"
     *
     * @return int ID записи
     */
    public function westernInsert($employeeID, $date, $girl, $men, $site, $sum, $code, $isSend, $isPer) {
        $data = array(
            'EmployeeID' => $employeeID,
            'Date' => $date,
            'Girl' => $girl,
            'Men' => $men,
            'SiteID' => $site,
            'Sum' => $sum,
            'Code' => $code,
            'IsSend' => $isSend,
            'IsPer' => empty($isPer) ? 0 : $isPer
        );
        $this->db()->insert(self::TABLE_SERVICE_WESTERN_NAME, $data);

        return $this->db()->insert_id();
    }

    /**
     * Обновление вестерна
     *
     * @param int       $id         ID записи
     * @param string    $date       дата
     * @param string    $girl       девушка
     * @param string    $men        мужчина
     * @param int       $site       ID сайта
     * @param float     $sum        сумма
     * @param string    $code       код
     * @param int       $isSend     флаг "выслали / % Кли-ки"
     * @param int       $isPer      флаг "% Пер-ка"
     */
    public function westernUpdate($id, $date, $girl, $men, $site, $sum, $code, $isSend, $isPer) {
        $data = array(
            'Date' => $date,
            'Girl' => $girl,
            'Men' => $men,
            'SiteID' => $site,
            'Sum' => $sum,
            'Code' => $code,
            'IsSend' => $isSend,
            'IsPer' => empty($isPer) ? 0 : $isPer,
        );

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_WESTERN_NAME, $data);
    }

    public function westernDone($id) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_WESTERN_NAME, array('IsDone' => 1));
    }

    public function westernSend($id, $isSend) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_WESTERN_NAME, array('IsSend' => $isSend));
    }

    public function westernPer($id, $isPer) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_WESTERN_NAME, array('IsPer' => $isPer));
    }

    /**
     * Поиск услуги "Встречи"
     *
     * @param int    $idEmployee ID сотрудника
     * @param string $start      начало периода
     * @param string $end        окончание периода
     * @param string $isDirector флаг директора. Для директора выбираются все не выполненные услуги, если не указаны временные интервалы
     *
     * @return array список услуг
     */
    public function meetingGetList($idEmployee, $start, $end, $isDirector) {
        if (!empty($idEmployee)) {
            $this->db()->where('EmployeeID', $idEmployee);
        }

        $this->db()->group_start();

        if (!empty($start)) {
            if (!empty($end)) {
                // Выбираем полное покрытие интервала + вхождение $start и $end в любой интервал
                $this->db()->where("(`DateIn` >= '$start' AND `DateOut` <= '$end') OR ('$start' BETWEEN `DateIn` AND `DateOut`) OR ('$end' BETWEEN `DateIn` AND `DateOut`)", NULL, FALSE);
            } else {
                $this->db()->where("'$start' BETWEEN `DateIn` AND `DateOut`", NULL, FALSE);
            }
        } else {
//            $this->db()
//                ->group_start()
//                    ->where('IsDone', 0)
//                    ->or_where("NOW() BETWEEN `DateIn` AND `DateOut`", NULL, FALSE)
//                ->group_end();

            if (IS_LOVE_STORY) {
                $this->db()
                    ->group_start()
                        ->where('IsDone', 0)
                        ->or_where("CURDATE() BETWEEN `DateIn` AND `DateOut`", NULL, FALSE)
                    ->group_end();
            } else {
                if ($isDirector) {
                    $this->db()->where('IsDone', 0);
                } else {
                    $this->db()->where("CURDATE() BETWEEN `DateIn` AND `DateOut`", NULL, FALSE);
                }
            }
        }

        $this->db()->group_end();

        return $this->db()->get(self::TABLE_SERVICE_MEETING_NAME)->result_array();
    }

    /**
     * Полученить указанную встречу
     *
     * @param string $id ID записи
     */
    public function meetingGet($id) {
        return $this->db()->get_where(self::TABLE_SERVICE_MEETING_NAME, ['ID' => $id])->row_array();
    }

    /**
     * Добавление новой встречи
     *
     * @param int       $employeeID             ID сотрудника
     * @param string    $dateIn                 дата приезда
     * @param string    $dateOut                дата отъезда
     * @param string    $girl                   девушка
     * @param string    $men                    мужчина
     * @param int       $site                   ID сайта
     * @param string    $userTranslate          переводчик
     * @param string    $city                   город
     * @param string    $transfer               трансфер
     * @param string    $housing                жилье
     * @param string    $translate              перевод
     * @param string    $userTranslateOrganizer переводчик - организатор
     * @param string    $userTranslateDuring    переводчик во время встречи
     *
     * @return int ID записи
     */
    public function meetingInsert($employeeID, $dateIn, $dateOut, $girl, $men, $site, $userTranslate, $city,
                                  $transfer, $housing, $translate, $userTranslateOrganizer, $userTranslateDuring) {
        $data = array(
            'EmployeeID' => $employeeID,
            'DateIn' => $dateIn,
            'DateOut' => $dateOut,
            'Girl' => $girl,
            'Men' => $men,
            'SiteID' => $site,
            IS_LOVE_STORY ? 'UserTranslateID' : 'UserTranslate' => $userTranslate,
            'City' => $city,
            'Transfer' => $transfer,
            'Housing' => $housing,
            'Translate' => $translate
        );
        if (IS_LOVE_STORY) {
            $data['UserTranslateOrganizer'] = $userTranslateOrganizer;
            $data['UserTranslateDuring'] = $userTranslateDuring;
        }

        $this->db()->insert(self::TABLE_SERVICE_MEETING_NAME, $data);

        return $this->db()->insert_id();
    }

    /**
     * Обновление встречи
     *
     * @param int       $id                     ID записи
     * @param string    $dateIn                 дата приезда
     * @param string    $dateOut                дата отъезда
     * @param string    $girl                   девушка
     * @param string    $men                    мужчина
     * @param int       $site                   ID сайта
     * @param string    $userTranslate          переводчик
     * @param string    $city                   город
     * @param string    $transfer               трансфер
     * @param string    $housing                жилье
     * @param string    $translate              перевод
     * @param string    $userTranslateOrganizer переводчик - организатор
     * @param string    $userTranslateDuring    переводчик во время встречи
     */
    public function meetingUpdate($id, $dateIn, $dateOut, $girl, $men, $site, $userTranslate, $city, $transfer,
                                  $housing, $translate, $userTranslateOrganizer, $userTranslateDuring) {
        $data = array(
            'DateIn' => $dateIn,
            'DateOut' => $dateOut,
            'Girl' => $girl,
            'Men' => $men,
            'SiteID' => $site,
            IS_LOVE_STORY ? 'UserTranslateID' : 'UserTranslate' => $userTranslate,
            'City' => $city,
            'Transfer' => $transfer,
            'Housing' => $housing,
            'Translate' => $translate
        );

        if (IS_LOVE_STORY) {
            $data['UserTranslateOrganizer'] = $userTranslateOrganizer;
            $data['UserTranslateDuring'] = $userTranslateDuring;
        }

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_MEETING_NAME, $data);
    }

    public function meetingDone($id) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_MEETING_NAME, array('IsDone' => 1));
    }

    /**
     * Поиск услуги "Доставка"
     *
     * @param int    $idEmployee ID сотрудника
     * @param string $start      начало периода
     * @param string $end        окончание периода
     * @param string $isDirector флаг директора. Для директора выбираются все не выполненные услуги, если не указаны временные интервалы
     *
     * @return array список услуг
     */
    public function deliveryGetList($idEmployee, $start, $end, $isDirector) {
        if (!empty($idEmployee)) {
            $this->db()
                ->group_start()
                    ->where('EmployeeID', $idEmployee)
                    ->or_where('UserTranslateID', $idEmployee)
                ->group_end();
        }

        $this->db()->group_start();

        if (!empty($start)) {
            if (!empty($end)) {
                $this->db()->where("`Date` BETWEEN '$start' AND '$end'", NULL, FALSE);
            } else {
                $this->db()->where('Date', $start);
            }
        } else {
            $this->db()
                ->group_start()
                    ->where('IsDone', 0)
                    ->or_where("`Date` = DATE_FORMAT(NOW(),'%Y-%m-%d')", NULL, FALSE)
                ->group_end();
        }

        $this->db()->group_end();

        return $this->db()->get(self::TABLE_SERVICE_DELIVERY_NAME)->result_array();
    }

    /**
     * Полученить указанную доставку
     *
     * @param string $id ID записи
     */
    public function deliveryGet($id) {
        return $this->db()->get_where(self::TABLE_SERVICE_DELIVERY_NAME, ['ID' => $id])->row_array();
    }

    /**
     * Добавление новой доставки
     *
     * @param int       $employeeID     ID сотрудника
     * @param string    $date           дата
     * @param string    $girl           девушка
     * @param string    $men            мужчина
     * @param int       $site           ID сайта
     * @param string    $userTranslate  переводчик
     * @param string    $delivery       доставка
     * @param string    $gratitude      благодарность
     *
     * @return int ID записи
     */
    public function deliveryInsert($employeeID, $date, $girl, $men, $site, $userTranslate, $delivery, $gratitude) {
        $data = array(
            'EmployeeID' => $employeeID,
            'Date' => $date,
            'Girl' => $girl,
            'Men' => $men,
            'SiteID' => $site,
            'UserTranslateID' => $userTranslate,
            'Delivery' => $delivery,
            'Gratitude' => $gratitude
        );
        $this->db()->insert(self::TABLE_SERVICE_DELIVERY_NAME, $data);

        return $this->db()->insert_id();
    }

    /**
     * Обновление доставки
     *
     * @param int       $id             ID записи
     * @param string    $date           дата
     * @param string    $girl           девушка
     * @param string    $men            мужчина
     * @param int       $site           ID сайта
     * @param string    $userTranslate  переводчик
     * @param string    $delivery       доставка
     * @param string    $gratitude      благодарность
     */
    public function deliveryUpdate($id, $date, $girl, $men, $site, $userTranslate, $delivery, $gratitude) {
        $data = array(
            'Date' => $date,
            'Girl' => $girl,
            'Men' => $men,
            'SiteID' => $site,
            'UserTranslateID' => $userTranslate,
            'Delivery' => $delivery,
            'Gratitude' => $gratitude
        );

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_DELIVERY_NAME, $data);
    }

    public function deliveryDone($id) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SERVICE_DELIVERY_NAME, array('IsDone' => 1));
    }

    /**
     * Получить список фото доставки
     *
     * @param int $DeliveryID ID доставки
     */
    public function deliveryImageGetList($DeliveryID) {
        return $this->db()
            ->from(self::TABLE_SERVICE_DELIVERY_2_IMAGE_NAME . ' AS a2i')
            ->select('a2i.*, i.ext')
            ->join(self::TABLE_IMAGE_NAME . ' AS i', 'i.ID = a2i.ImageID', 'inner')
            ->where('a2i.DeliveryID', $DeliveryID)
            ->order_by("a2i.DateCreate", "DESC")
            ->get()
            ->result_array();
    }

    /**
     * Добавление фото в доставку
     *
     * @param int $DeliveryID ID доставки
     * @param int $idImage ID фото
     *
     * @return int ID
     */
    public function deliveryImageInsert($DeliveryID, $idImage) {
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_SERVICE_DELIVERY_2_IMAGE_NAME, ['DeliveryID' => $DeliveryID, 'ImageID' => $idImage]);

        return $this->db()->insert_id();
    }

    /**
     * Удалить фото из доставки
     *
     * @param int $id ID записи в базе
     */
    public function deliveryImageDelete($id) {
        // 1. Поиск и удаление фото
        $cross = $this->db()->get_where(self::TABLE_SERVICE_DELIVERY_2_IMAGE_NAME, array('ID' => $id))->row_array();

        $image = $this->db()->get_where(self::TABLE_IMAGE_NAME, ['ID' => $cross['ImageID']])->row_array();

        if ($image) {
            $file = './files/images/'.$image['ID'].'.'.$image['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_IMAGE_NAME, ['ID' => $cross['ImageID']]); // Удаление записи из таблицы
        }

        // 2. Удаление связки фото с доставкой
        $this->db()->delete(self::TABLE_SERVICE_DELIVERY_2_IMAGE_NAME, ['ID' => $id]);
    }

    /**
     * Получить количество фото доставки
     *
     * @param int $DeliveryID ID доставки
     * @return integer
     */
    public function deliveryImageGetCount($DeliveryID) {
        return $this->db()
            ->from(self::TABLE_SERVICE_DELIVERY_2_IMAGE_NAME . ' AS a2i')
            ->select('a2i.*, i.ext')
            ->join(self::TABLE_IMAGE_NAME . ' AS i', 'i.ID = a2i.ImageID', 'inner')
            ->where('a2i.DeliveryID', $DeliveryID)
            ->order_by("a2i.DateCreate", "DESC")
            ->count_all_results();
    }

    /**
     * поиск доставок по фамилии (может состоять из 2-х частей: Бабич Babich) клиентки + ID сайта
     * @param $SName
     * @param $SName2
     * @param $SiteID
     */
    public function findDeliveryBySName($SName, $SName2, $SiteID)
    {
        $where = array(
            'sd.IsDone' => 1
        );
        if($SiteID > 0){
            $where['sd.SiteID'] = $SiteID;
        }
        $this->db()->select("sd.*, e.SName AS 'ESName', e.FName AS 'EFName', e.MName AS 'EMName', 
        e2.SName AS 'E2SName', e2.FName AS 'E2FName', e2.MName AS 'E2MName', s.Name AS 'SiteName'")
            ->from(self::TABLE_SERVICE_DELIVERY_NAME . ' AS sd')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = sd.EmployeeID')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e2', 'e2.ID = sd.UserTranslateID')
            ->join(self::TABLE_SITE_NAME . ' AS s', 's.ID = sd.SiteID');
        $this->db()->group_start()
            ->like('sd.Girl', $SName);
        if(!empty($SName2)){
            $this->db()->or_like('sd.Girl', $SName2);
        }
        $this->db()->group_end();
        $res = $this->db()->where($where)
            ->order_by('sd.Date', 'desc')
            ->get()
            ->result_array(); // log_message('error', $this->db()->last_query());
        return $res;
    }

}