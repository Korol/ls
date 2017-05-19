<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с клиентами
 */
class Customer_model extends MY_Model {


    /** Таблица с клиентами */
    private $table_customer =
        "CREATE TABLE IF NOT EXISTS `assol_customer` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `SName` VARCHAR(64) NOT NULL COMMENT 'Фамилия',
            `FName` VARCHAR(64) NOT NULL COMMENT 'Имя',
            `MName` VARCHAR(64) COMMENT 'Отчество',
            `DOB` DATE COMMENT 'Дата рождения',
            `DateRegister` DATE COMMENT 'Дата регистрации',
            `Avatar` INT(11) NOT NULL DEFAULT 0 COMMENT 'ID картинки аватара',
            `City` VARCHAR(64) COMMENT 'Город',
            `Postcode` VARCHAR(32) COMMENT 'Индекс',
            `Country` VARCHAR(64) COMMENT 'Страна',
            `Address` VARCHAR(256) COMMENT 'Адрес проживания',
            `Phone_1` VARCHAR(32) COMMENT 'Телефон 1',
            `Phone_2` VARCHAR(32) COMMENT 'Телефон 2',
            `Email` VARCHAR(320) COMMENT 'E-Mail',
            `Email_site` VARCHAR(320) COMMENT 'E-Mail',
            `Email_private` VARCHAR(320) COMMENT 'E-Mail',
            `VK` VARCHAR(256) COMMENT 'URL страницы Вконтакте',
            `Instagram` VARCHAR(256) COMMENT 'URL страницы в Instagram',
            `Facebook` VARCHAR(256) COMMENT 'URL страницы в Facebook',
            `ProfessionOfDiploma` VARCHAR(256) COMMENT 'Профессия (по диплому)',
            `CurrentWork` VARCHAR(256) COMMENT 'Работа на данный момент',
            `Worship` VARCHAR(128) COMMENT 'Вероисповедание',
            `PassportSeries` VARCHAR(16) COMMENT 'Серия паспорта',
            `PassportNumber` VARCHAR(16) COMMENT 'Номер паспорта',
            `HairColor` VARCHAR(128) COMMENT 'Цвет волос',
            `BodyBuild` VARCHAR(128) COMMENT 'Строение тела',
            `BodyBuildID` INT(11) COMMENT 'Строение тела (значение из справочника)',
            `SizeFoot` VARCHAR(64) COMMENT 'Размер ноги - только LoveStory',
            `Forming` INT(11) COMMENT 'Образование (значение из справочника)',
            `MaritalStatus` INT(11) COMMENT 'Семейное положение (значение из справочника)',
            `EyeColor` INT(11) COMMENT 'Цвет глаз (значение из справочника)',
            `Status` INT(11) COMMENT 'Статус клиента (значение из справочника)',
            `Height` INT(11) COMMENT 'Рост',
            `Weight` INT(11) COMMENT 'Вес',
            `Smoking` INT(11) COMMENT 'Курение',
            `Alcohol` INT(11) COMMENT 'Алкоголь',
            `WishesForManNationality` VARCHAR(128) COMMENT 'Пожелания к мужчине - Национальность',
            `WishesForManAgeMin` INT(11) COMMENT 'Пожелания к мужчине - Минимальный возраст',
            `WishesForManAgeMax` INT(11) COMMENT 'Пожелания к мужчине - Максимальный возраст',
            `WishesForManWeight` INT(11) COMMENT 'Пожелания к мужчине - Вес',
            `WishesForManHeight` INT(11) COMMENT 'Пожелания к мужчине - Рост',
            `WishesForManText` TEXT COMMENT 'Пожелания к мужчине - Текст',
            `IsDeleted` TINYINT(1) DEFAULT 0 COMMENT 'Флаг удаления',
            `ReasonForDeleted` TEXT COMMENT 'Причина удаления',
            `Temper` TEXT COMMENT 'Характер',
            `Interests` TEXT COMMENT 'Интересы',
            `Additionally` TEXT COMMENT 'Дополнительно',
            `Meetings` TEXT COMMENT 'Встречи',
            `Delivery` TEXT COMMENT 'Доставки',
            `Question` MEDIUMTEXT COMMENT 'Вопросы',
            `Note` TEXT COMMENT 'Примечание',
            `ReservationContacts` TEXT COMMENT 'Заказ контактов',
            `DateLastPhotoSession` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата последней фотосессии',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата создания профиля',
            `DateUpdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата последнего редактирования',
            `DateRemove` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата удаления',
            `WhoUpdate` INT(11) COMMENT 'Кто обновил в последний раз',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Клиенты';";

    /** Таблица с историей изменений */
    private $table_customer_history =
        "CREATE TABLE IF NOT EXISTS `assol_customer_history` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `customer` INT(11) COMMENT 'ID клиента',            
            `author` INT(11) COMMENT 'ID сотрудника изменившего поле',          
            `field` TEXT COMMENT 'Название редактируемого поля',
            `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата редактирования поля',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='История изменений карточки клиентов';";

    /** Таблица договоров с клиентом */
    private $table_customer_agreement =
        "CREATE TABLE IF NOT EXISTS `assol_customer_agreement` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `Name` VARCHAR(256) NULL COMMENT 'Название файла',
            `ext` VARCHAR(10) NOT NULL COMMENT 'Расширение файла',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Договора клиента';";

    /** Таблица фотоальбомов клиента */
    private $table_customer_album =
        "CREATE TABLE IF NOT EXISTS `assol_customer_album` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `Name` VARCHAR(128) NULL COMMENT 'Название альбома',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата создания',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Фотоальбомы клиента';";

    /** Таблица связка фотоальбомов с картинками */
    private $table_customer_album2image =
        "CREATE TABLE IF NOT EXISTS `assol_customer_album2image` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `AlbumID` INT(11) NOT NULL COMMENT 'Уникальный номер альбома',
            `ImageID` INT(11) NOT NULL COMMENT 'Уникальный номер изображения',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата создания',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Кросс для фотоальбомов и изображений';";

    /** Таблица с сканами паспорта клиента */
    private $table_customer_passport_scan =
        "CREATE TABLE IF NOT EXISTS `assol_customer_passport_scan` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `Name` VARCHAR(256) NULL COMMENT 'Название файла',
            `ext` VARCHAR(10) NOT NULL COMMENT 'Расширение файла',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сканы паспорта клиента';";

    /** Таблица с языками клиента */
    private $table_customer_language =
        "CREATE TABLE IF NOT EXISTS `assol_customer_language` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `LanguageID` INT(11) NOT NULL COMMENT 'ID языка из справочника',
            `Level` INT(11) NOT NULL COMMENT 'Уровень владения языком',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Языки клиента';";

    /** Таблица с детьмя клиента */
    private $table_customer_children =
        "CREATE TABLE IF NOT EXISTS `assol_customer_children` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `SexID` INT(11) NOT NULL COMMENT 'ID пола ребенка',
            `FIO` VARCHAR(128) COMMENT 'ФИО',
            `Reside` VARCHAR(256) COMMENT 'С кем проживает - Только Love story',
            `DOB` DATE COMMENT 'Дата рождения',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Дети клиента';";

    /** Таблица с вопросами клиента */
    private $table_customer_question =
        "CREATE TABLE IF NOT EXISTS `assol_customer_question` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `Question` TEXT NOT NULL COMMENT 'Вопрос клиента',
            `Answer` TEXT COMMENT 'Ответ на вопрос клиента',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Вопросы клиента';";

    /** Таблица с видео клиента */
    private $table_customer_video =
        "CREATE TABLE IF NOT EXISTS `assol_customer_video` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `Type` TINYINT(1) NOT NULL COMMENT 'Тип видео 0 - видеоподтверждение / 1 - любительское видео',
            `Link` VARCHAR(512) NOT NULL COMMENT 'Ссылка на видео youtube',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Видео клиента';";

    /** Таблица с сайтами-видео клиента*/
    private $table_customer_video_site =
        "CREATE TABLE IF NOT EXISTS `assol_customer_video_site` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Клиентскии сайты-видео';";

    /** Таблица с видео клиента на сайтах */
    private $table_customer_video_site_link =
        "CREATE TABLE IF NOT EXISTS `assol_customer_video_site_link` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта с видео',
            `Type` TINYINT(1) NOT NULL COMMENT 'Тип видео 0 - видеоподтверждение / 1 - любительское видео / 2 - видеописьмо',
            `Link` VARCHAR(512) NOT NULL COMMENT 'Ссылка на видео youtube',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Клиентское видео на сайтах';";

    /** Таблица с сайтами клиента */
    private $table_customer_site =
        "CREATE TABLE IF NOT EXISTS `assol_customer_site` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта',
            `Note` TEXT COMMENT 'Примечание к сайту',
            `IsDeleted` TINYINT(1) DEFAULT 0 COMMENT 'Флаг удаления',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сайты клиента';";

    /** Таблица с историей встреч клиента */
    private $table_customer_story =
        "CREATE TABLE IF NOT EXISTS `assol_customer_story` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `SiteID` INT(11) NOT NULL DEFAULT 0 COMMENT 'Уникальный номер сайта',
            `Date` DATE NOT NULL COMMENT 'Дата',
            `Name` VARCHAR(128) NOT NULL COMMENT 'Имя',
            `Note` TEXT COMMENT 'Дополнительно',
            `Avatar` INT(11) NOT NULL DEFAULT 0 COMMENT 'ID картинки аватара',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='История встреч клиента';";

    private $assol_customer_docs_rights =
        "CREATE TABLE IF NOT EXISTS `assol_customer_docs_rights` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `EmployeeID` INT(11) NOT NULL COMMENT 'ID сотрудника',
            `TargetCustomerID` INT(11) NOT NULL COMMENT 'ID клиентки, к документам которой открываем доступ',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`TargetCustomerID`) REFERENCES `assol_customer` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Права доступа к документам клиенток';";

    /** Таблица с E-Mail клиенток (для ассоль) */
    private $table_customer_email =
        "CREATE TABLE IF NOT EXISTS `assol_customer_email` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиентки',
            `Email` VARCHAR(320) COMMENT 'E-Mail',
            `Note` TEXT COMMENT 'Комментарий',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='E-Mail клиента';";

    /** Таблица с вопросами для клиентов */
    private $table_customer_question_template =
        "CREATE TABLE IF NOT EXISTS `assol_customer_question_template` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `question` TEXT NOT NULL COMMENT 'Вопрос',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Вопросы для клиентов';";
    
    /** Таблица с ответами на вопросы клиентов */
    private $table_customer_question_answer =
        "CREATE TABLE IF NOT EXISTS `assol_customer_question_answer` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `customerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `questionID` INT(11) NOT NULL COMMENT 'Уникальный номер вопроса',
            `answer` TEXT COMMENT 'Ответ на вопрос клиента',
            PRIMARY KEY (`id`),
            FOREIGN KEY (`customerID`) REFERENCES `assol_customer` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`questionID`) REFERENCES `assol_customer_question_template` (`id`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Ответы на вопросы';";

    /** Таблица с изображениями клиента */
    private $table_customer_question_photo =
        "CREATE TABLE IF NOT EXISTS `assol_customer_question_photo` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер клиента',
            `Name` VARCHAR(256) NULL COMMENT 'Название файла',
            `ext` VARCHAR(10) NOT NULL COMMENT 'Расширение файла',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Изображения вопросов клиента';";



    /**
     * Инициализация таблиц
     */
    public function initDataBase() {
        $this->db()->query($this->table_customer);
        $this->db()->query($this->table_customer_history);
        $this->db()->query($this->table_customer_album);
        $this->db()->query($this->table_customer_album2image);
        $this->db()->query($this->table_customer_email);
        $this->db()->query($this->table_customer_site);
        $this->db()->query($this->table_customer_story);
        $this->db()->query($this->table_customer_video);
        $this->db()->query($this->table_customer_video_site);
        $this->db()->query($this->table_customer_video_site_link);
        $this->db()->query($this->table_customer_agreement);
        $this->db()->query($this->table_customer_language);
        $this->db()->query($this->table_customer_children);
        $this->db()->query($this->table_customer_question);
        $this->db()->query($this->table_customer_passport_scan);
        $this->db()->query($this->assol_customer_docs_rights);
        $this->db()->query($this->table_customer_question_template);
        $this->db()->query($this->table_customer_question_answer);
        $this->db()->query($this->table_customer_question_photo);
    }

    /** Удаление таблиц */
    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_CUSTOMER_QUESTION_ANSWER_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_QUESTION_TEMPLATE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_DOCS_RIGHTS_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_ALBUM_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_AGREEMENT_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_LANGUAGE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_CHILDREN_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_QUESTION_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_QUESTION_PHOTO_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_VIDEO_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_SITE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_VIDEO_SITE_LINK_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_VIDEO_SITE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_STORY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_HISTORY_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CUSTOMER_NAME, TRUE);
    }

    /**
     * Получить список клиентов
     *
     * @param int $idEmployee ID сотрудника
     * @param bool $data
     *
     * @return mixed
     */
    public function customerGetList($idEmployee = false, $data = false) {
        $this->db()
            ->select("c.*, e.FName as 'FNameUpdate', e.SName as 'SNameUpdate', CONCAT(img.ID, '.', img.ext) as 'FileName', eml.Email as 'FirstEmail'") // Поле FirstEmail для ассоль
            ->from(self::TABLE_CUSTOMER_NAME . ' AS c')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'c.Avatar = img.ID', 'left')
            ->join(self::TABLE_CUSTOMER_EMAIL_NAME . ' AS eml', 'eml.CustomerID = c.ID', 'left')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'c.WhoUpdate = e.ID', 'left')
            ->group_by('c.ID');

        // Для переводчика фильтруем список клиенток
        if ($idEmployee) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c', 'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS e2s', 'es2c.EmployeeSiteID = e2s.ID AND e2s.IsDeleted = 0 AND e2s.EmployeeID = '.$idEmployee, 'inner');
        }

        if (is_array($data)) {
            $this->db()->where('c.IsDeleted', (isset($data['Status']) ? $data['Status'] : 0));

            if (IS_LOVE_STORY) {
                // Для LoveStory поиск по ID и ФИО в одном поле ID
                if (isset($data['ID']) && $data['ID']) {
                    $this->db()
                        ->group_start()
                            ->like('c.FName', $data['ID'])
                            ->or_like('c.SName', $data['ID'])
                            ->or_where('c.ID', $data['ID'])
                        ->group_end();
                }

                if (isset($data['City']) && $data['City']) {
                    $this->db()->like('c.City', $data['City']);
                }
            } else {
                if (isset($data['ID']) && $data['ID']) {
                    $this->db()->where('c.ID', $data['ID']);
                }

                if (isset($data['FIO']) && $data['FIO']) {
                    $this->db()
                        ->group_start()
                        ->like('c.FName', $data['FIO'])
                        ->or_like('c.SName', $data['FIO'])
                        ->group_end();
                }
            }

            // Фильтрация по максимальному возрасту
            if (isset($data['MaxAge']) && ($data['MaxAge'] > 0))
                $this->db()->where('YEAR(c.DOB) >= (YEAR(NOW()) - '.$data['MaxAge'].')', null, false);

            // Фильтрация по минимальному возрасту
            if (isset($data['MinAge']) && ($data['MinAge'] > 0))
                $this->db()->where('YEAR(c.DOB) <= (YEAR(NOW()) - '.$data['MinAge'].')', null, false);

        }

        return array(
            'count' => $this->db()->count_all_results('', FALSE),
            'records' => $this->db()
                ->order_by(IS_LOVE_STORY ? 'c.DateUpdate' : 'c.DateCreate', 'DESC') // LoveStory сортируем по дате изменения карточки клиентки
                ->limit($data['Limit'], $data['Offset'])
                ->get()->result_array()
        );
    }

    /**
     * Получить информацию о клиенте
     *
     * @param int $id ID клиента в системе
     *
     * @return mixed
     */
    public function customerGet($id) {
        return $this->db()
            ->select("c.*, CONCAT(img.ID, '.', img.ext) as 'FileName'")
            ->from(self::TABLE_CUSTOMER_NAME . ' AS c')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'c.Avatar = img.ID', 'left')
            ->where('c.ID', $id)
            ->get()->row_array();
    }

    /**
     * Добавление нового клиента
     *
     * @param string $sName фамилия
     * @param string $fName имя
     * @param string $mName отчество
     *
     * @return int ID нового клиента
     */
    public function customerInsert($sName, $fName, $mName) {
        $data = array(
            'SName' => $sName,
            'FName' => $fName,
            'MName' => $mName
        );
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_CUSTOMER_NAME, $data);

        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о клиенте
     *
     * @param int $id клиента
     * @param array $data массив полей для сохранения. Например: array('SName' => 'Иванова', 'FName' => 'Аня')
     */
    public function customerUpdate($id, $data) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_NAME, $data);
    }

    /**
     * Удалить клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function customerDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_NAME, array('ID' => $id));
    }

    /**
     * Получить список договоров для клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function agreementGetList($idCustomer) {
        $this->db()->select('ID, Name, ext');
        return $this->db()->get_where(self::TABLE_CUSTOMER_AGREEMENT_NAME, array('CustomerID' => $idCustomer))->result_array();
    }

    /**
     * Получить договор по ID
     *
     * @param int $idAgreement ID записи в базе
     */
    public function agreementGet($idAgreement) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_AGREEMENT_NAME, array('ID' => $idAgreement))->row_array();
    }

    /**
     * Получить информацию о договоре (без содержимого)
     *
     * @param int $idAgreement ID записи в базе
     */
    public function agreementGetMeta($idAgreement) {
        $this->db()->select('ID, Name, CustomerID, ext');
        return $this->db()->get_where(self::TABLE_CUSTOMER_AGREEMENT_NAME, array('ID' => $idAgreement))->row_array();
    }

    /**
     * Сохранение договор в базу данных
     *
     * @param int $idCustomer ID клиента
     * @param string $name Название файла
     * @param string $content Содержимое файла
     * @param string $ext Расширение файла
     *
     * @return int ID записи
     */
    public function agreementInsert($idCustomer, $name, $content, $ext) {
        // Открываем транзакция
        $this->db()->trans_start();

        // Вставляем информацию о файле
        $this->db()->insert(self::TABLE_CUSTOMER_AGREEMENT_NAME, ['CustomerID' => $idCustomer, 'Name' => $name, 'ext' => $ext]);
        $id = $this->db()->insert_id();

        // Пытаемся сохранить в файл
        if (file_put_contents("./files/customer/agreement/$id.$ext", $content) === FALSE) {
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
        $agreement = $this->db()->get_where(self::TABLE_CUSTOMER_AGREEMENT_NAME, ['ID' => $idAgreement])->row_array();

        if ($agreement) {
            $file = './files/customer/agreement/'.$agreement['ID'].'.'.$agreement['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_CUSTOMER_AGREEMENT_NAME, ['ID' => $idAgreement]); // Удаление записи из таблицы
        }
    }

    public function agreementList($limit = 5, $offset = 0) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_AGREEMENT_NAME)
            ->limit($limit, $offset)
            ->get()->result_array();
    }

    /**
     * Получить список фотоальбомов для клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function albumGetList($idCustomer) {
        return $this->db()
            ->order_by("DateCreate", "DESC")
            ->get_where(self::TABLE_CUSTOMER_ALBUM_NAME, array('CustomerID' => $idCustomer))
            ->result_array();
    }

    /**
     * Получить информацию о фотоальбоме
     *
     * @param int $id ID фотоальбома
     *
     * @return mixed
     */
    public function albumGet($id) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_ALBUM_NAME, array('ID' => $id))->row_array();
    }

    /**
     * Получить информацию о фотоальбоме по ID фото
     *
     * @param int $id ID фото
     *
     * @return mixed
     */
    public function albumGetByPhotoId($id) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME.' AS cross')
            ->select('album.*')
            ->where('cross.ID', $id)
            ->join(self::TABLE_CUSTOMER_ALBUM_NAME . ' AS album', 'album.ID=cross.AlbumID', 'inner')
            ->get()->row_array();
    }

    /**
     * Добавление нового фотоальбома
     *
     * @param int $idCustomer ID клиента
     * @param string $name название фотоальбома
     *
     * @return int ID
     */
    public function albumInsert($idCustomer, $name) {
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_CUSTOMER_ALBUM_NAME, ['CustomerID' => $idCustomer, 'Name' => $name]);

        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о фотоальбома
     *
     * @param int $id фотоальбома
     * @param array $data массив полей для сохранения. Например: array('SName' => 'Иванова', 'FName' => 'Аня')
     */
    public function albumUpdate($id, $data) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_ALBUM_NAME, $data);
    }

    /**
     * Удалить фотоальбом из базы
     *
     * @param int $id ID записи в базе
     */
    public function albumDelete($id) {
        // 1. Поиск и удаление всех фоток из альбома
        $album2image = $this->db()->get_where(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME, ['AlbumID' => $id])->result_array();
        foreach ($album2image as $cross) {
            $image = $this->db()->get_where(self::TABLE_IMAGE_NAME, ['ID' => $cross['ImageID']])->row_array();

            if ($image) {
                $file = './files/images/'.$image['ID'].'.'.$image['ext'];
                if (file_exists($file)) unlink($file); // Удаление файла

                $this->db()->delete(self::TABLE_IMAGE_NAME, ['ID' => $cross['ImageID']]); // Удаление записи из таблицы
            }
        }

        // 2. Удаление всех связок
        $this->db()->delete(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME, ['AlbumID' => $id]);

        // 3. Удаление альбома
        $this->db()->delete(self::TABLE_CUSTOMER_ALBUM_NAME, array('ID' => $id));
    }

    /**
     * Получить список фото из фотоальбома
     *
     * @param int $idAlbum ID фотоальбома
     */
    public function albumImageGetList($idAlbum) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME . ' AS a2i')
            ->select('a2i.*, i.ext')
            ->join(self::TABLE_IMAGE_NAME . ' AS i', 'i.ID = a2i.ImageID', 'inner')
            ->where('a2i.AlbumID', $idAlbum)
            ->order_by("a2i.DateCreate", "DESC")
            ->get()
            ->result_array();
    }

    /**
     * Добавление фото в фотоальбом
     *
     * @param int $idAlbum ID фотоальбома
     * @param int $idImage ID фото
     *
     * @return int ID
     */
    public function albumImageInsert($idAlbum, $idImage) {
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME, ['AlbumID' => $idAlbum, 'ImageID' => $idImage]);

        return $this->db()->insert_id();
    }

    /**
     * Удалить фотоальбом из базы
     *
     * @param int $id ID записи в базе
     */
    public function albumImageDelete($id) {
        // 1. Поиск и удаление фото
        $cross = $this->db()->get_where(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME, array('ID' => $id))->row_array();

        $image = $this->db()->get_where(self::TABLE_IMAGE_NAME, ['ID' => $cross['ImageID']])->row_array();

        if ($image) {
            $file = './files/images/'.$image['ID'].'.'.$image['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_IMAGE_NAME, ['ID' => $cross['ImageID']]); // Удаление записи из таблицы
        }

        // 2. Удаление связки фото с альбомом
        $this->db()->delete(self::TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME, ['ID' => $id]);
    }

    /**
     * Получить список email клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function emailGetList($idCustomer) {
        return $this->db()
            ->get_where(self::TABLE_CUSTOMER_EMAIL_NAME, ['CustomerID' => $idCustomer])
            ->result_array();
    }

    /**
     * Добавление email клиенту
     *
     * @param int $idCustomer ID клиента
     * @param string $email E-Mail
     * @param string $note комментарий
     *
     * @return int ID записи
     */
    public function emailInsert($idCustomer, $email, $note) {
        $this->db()->insert(self::TABLE_CUSTOMER_EMAIL_NAME,
            array('CustomerID' => $idCustomer, 'Email' => $email, 'Note' => $note));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о email
     *
     * @param int $id ID записи
     * @param string $email E-Mail
     * @param string $note комментарий
     */
    public function emailUpdate($id, $email, $note) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_EMAIL_NAME, ['Email' => $email, 'Note' => $note]);
    }

    /**
     * Удалить email клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function emailDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_EMAIL_NAME, array('ID' => $id));
    }

    /**
     * Получить список сканов паспорта для клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function passportGetList($idCustomer) {
        $this->db()->select('ID, Name, ext');
        return $this->db()->get_where(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME, array('CustomerID' => $idCustomer))->result_array();
    }

    /**
     * Получить скан паспорта из базы
     *
     * @param int $idPassportScan номер страницы паспорта
     */
    public function passportGet($idPassportScan) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME, array('ID' => $idPassportScan))->row_array();
    }

    /**
     * Добавление скана паспорта клиенту
     *
     * @param int $idCustomer ID клиента
     * @param string $name Название файла
     * @param string $content Содержимое скана паспорта
     * @param string $ext Расширение файла
     *
     * @return int ID записи
     */
    public function passportInsert($idCustomer, $name, $content, $ext) {
        // Открываем транзакция
        $this->db()->trans_start();

        // Вставляем информацию о файле
        $this->db()->insert(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME, ['CustomerID' => $idCustomer, 'Name' => $name, 'ext' => $ext]);
        $id = $this->db()->insert_id();

        // Пытаемся сохранить в файл
        if (file_put_contents("./files/customer/passport/$id.$ext", $content) === FALSE) {
            $this->db()->trans_rollback(); // Отменяем транзакцию если ошибка
        } else {
            $this->db()->trans_complete(); // Завершаем транзакцию если успешно
        }

        return $id;
    }

    /**
     * Удалить скан паспорта из базы
     *
     * @param int $idPassportScan номер страницы паспорта
     */
    public function passportDelete($idPassportScan) {
        $record = $this->db()->get_where(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME, ['ID' => $idPassportScan])->row_array();

        if ($record) {
            $file = './files/customer/passport/'.$record['ID'].'.'.$record['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME, ['ID' => $idPassportScan]); // Удаление записи из таблицы
        }

        $this->db()->delete(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME, array('ID' => $idPassportScan));
    }

    /**
     * Получить список изображений из раздела вопросов для клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function questionPhotoGetList($idCustomer) {
        $this->db()->select('ID, Name, ext');
        return $this->db()->get_where(self::TABLE_CUSTOMER_QUESTION_PHOTO_NAME, array('CustomerID' => $idCustomer))->result_array();
    }

    /**
     * Получить изображение раздела вопросов из базы
     *
     * @param int $idQuestionPhoto номер изображения
     */
    public function questionPhotoGet($idQuestionPhoto) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_QUESTION_PHOTO_NAME, array('ID' => $idQuestionPhoto))->row_array();
    }

    /**
     * Добавление изображения клиенту
     *
     * @param int $idCustomer ID клиента
     * @param string $name Название файла
     * @param string $content Содержимое изображения
     * @param string $ext Расширение файла
     *
     * @return int ID записи
     */
    public function questionPhotoInsert($idCustomer, $name, $content, $ext) {
        // Открываем транзакция
        $this->db()->trans_start();

        // Вставляем информацию о файле
        $this->db()->insert(self::TABLE_CUSTOMER_QUESTION_PHOTO_NAME, ['CustomerID' => $idCustomer, 'Name' => $name, 'ext' => $ext]);
        $id = $this->db()->insert_id();

        // Пытаемся сохранить в файл
        if (file_put_contents("./files/customer/question/photo/$id.$ext", $content) === FALSE) {
            $this->db()->trans_rollback(); // Отменяем транзакцию если ошибка
        } else {
            $this->db()->trans_complete(); // Завершаем транзакцию если успешно
        }

        return $id;
    }

    /**
     * Удалить изображения из базы
     *
     * @param int $idQuestionPhoto ID изображения
     */
    public function questionPhotoDelete($idQuestionPhoto) {
        $record = $this->db()->get_where(self::TABLE_CUSTOMER_QUESTION_PHOTO_NAME, ['ID' => $idQuestionPhoto])->row_array();

        if ($record) {
            $file = './files/customer/question/photo/'.$record['ID'].'.'.$record['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_CUSTOMER_QUESTION_PHOTO_NAME, ['ID' => $idQuestionPhoto]); // Удаление записи из таблицы
        }

        $this->db()->delete(self::TABLE_CUSTOMER_QUESTION_PHOTO_NAME, array('ID' => $idQuestionPhoto));
    }

    /**
     * Получить список языков для клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function languageGetList($idCustomer) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_LANGUAGE_NAME, array('CustomerID' => $idCustomer))->result_array();
    }

    /**
     * Добавление языка клиенту
     *
     * @param int $idCustomer ID клиента
     * @param int $idLanguage ID языка из справочника
     * @param int $level Уровень владения
     *
     * @return int ID записи
     */
    public function languageInsert($idCustomer, $idLanguage, $level) {
        $this->db()->insert(self::TABLE_CUSTOMER_LANGUAGE_NAME, array('CustomerID' => $idCustomer, 'LanguageID' => $idLanguage, 'Level' => $level));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о языке
     *
     * @param int $id ID записи
     * @param int $idLanguage ID языка из справочника
     * @param int $level Уровень владения
     */
    public function languageUpdate($id, $idLanguage, $level) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_LANGUAGE_NAME, array('LanguageID' => $idLanguage, 'Level' => $level));
    }

    /**
     * Удалить язык клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function languageDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_LANGUAGE_NAME, array('ID' => $id));
    }

    /**
     * Получить список детей клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function childrenGetList($idCustomer) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_CHILDREN_NAME, array('CustomerID' => $idCustomer))->result_array();
    }

    /**
     * Добавление ребенка клиенту
     *
     * @param int $idCustomer ID клиента
     * @param int $idChildrenSex ID пола ребенка из справочника
     * @param string $fio ФИО ребенка
     * @param string $dob Дата рождения ребенка
     * @param string $Reside С кем проживает - только LoveStory
     *
     * @return int ID записи
     */
    public function childrenInsert($idCustomer, $idChildrenSex, $fio, $dob, $Reside) {
        $data = ['CustomerID' => $idCustomer, 'SexID' => $idChildrenSex, 'FIO' => $fio, 'DOB' => $dob];
        if (IS_LOVE_STORY) {
            $data['Reside'] = $Reside;
        }

        $this->db()->insert(self::TABLE_CUSTOMER_CHILDREN_NAME, $data);
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о ребенке
     *
     * @param int $id ID записи
     * @param int $idChildrenSex ID пола ребенка из справочника
     * @param string $fio ФИО ребенка
     * @param string $dob Дата рождения ребенка
     * @param string $Reside С кем проживает - только LoveStory
     */
    public function childrenUpdate($id, $idChildrenSex, $fio, $dob, $Reside) {
        $data = ['SexID' => $idChildrenSex, 'FIO' => $fio, 'DOB' => $dob];
        if (IS_LOVE_STORY) {
            $data['Reside'] = $Reside;
        }

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_CHILDREN_NAME, $data);
    }

    /**
     * Удалить ребенка клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function childrenDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_CHILDREN_NAME, array('ID' => $id));
    }

    /**
     * Получить список вопросов клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function questionGetList($idCustomer) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_QUESTION_NAME, array('CustomerID' => $idCustomer))->result_array();
    }

    /**
     * Получить список сайтов клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function siteGetList($idCustomer) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_SITE_NAME . ' AS cs')
            ->select('cs.*')
            ->join(self::TABLE_SITE_NAME . ' AS s', 's.id = cs.SiteID', 'inner')
            ->where('cs.CustomerID', $idCustomer)
            ->where('cs.IsDeleted', 0)
            ->order_by('s.Name', 'ASC')
            ->get()->result_array();
    }

    /**
     * Добавление сайтов клиента
     *
     * @param int $idCustomer ID клиента
     * @param string $idSite сайт
     *
     * @return int ID записи
     */
    public function siteInsert($idCustomer, $idSite) {
        $data = ['CustomerID' => $idCustomer, 'SiteID' => $idSite];

        $record = $this->db()->get_where(self::TABLE_CUSTOMER_SITE_NAME, $data)->row_array();
        if ($record) {
            $id = $record['ID'];
            $this->db()->update(self::TABLE_CUSTOMER_SITE_NAME, ['IsDeleted' => 0], ['ID' => $id]);
        } else {
            $this->db()->insert(self::TABLE_CUSTOMER_SITE_NAME, $data);
            $id = $this->db()->insert_id();
        }

        return $id;
    }

    /**
     * Сохранение информации о сайте
     *
     * @param int $id клиента
     * @param array $data массив полей для сохранения. Например: array('SName' => 'Иванова', 'FName' => 'Аня')
     */
    public function siteUpdate($id, $data) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_SITE_NAME, $data);
    }

    /**
     * Удалить сайт клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function siteDelete($id) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_SITE_NAME, ['IsDeleted' => 1]);
    }

    /**
     * Получить список встреч клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function storyGetList($idCustomer) {
        return $this->db()
            ->select("cs.*, CONCAT(img.ID, '.', img.ext) as 'FileName'")
            ->from(self::TABLE_CUSTOMER_STORY_NAME . ' AS cs')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'cs.Avatar = img.ID', 'left')
            ->where('cs.CustomerID', $idCustomer)
            ->get()->result_array();
    }

    /**
     * Добавление историю встречи клиенту
     *
     * @param int $idCustomer ID клиента
     * @param string $Date Дата встречи
     * @param int $StorySite ID сайта
     * @param string $Name Имя
     * @param string $Note Дополнительное описание
     * @param int $avatar ID фото
     *
     * @return int ID записи
     */
    public function storyInsert($idCustomer, $Date, $StorySite, $Name, $Note, $avatar) {
        $this->db()->insert(self::TABLE_CUSTOMER_STORY_NAME,
            array('CustomerID' => $idCustomer, 'SiteID' => $StorySite, 'Date' => $Date, 'Name' => $Name, 'Note' => $Note, 'Avatar' => is_numeric($avatar) ? $avatar : 0));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о историю встречи
     *
     * @param int $id ID записи
     * @param string $Date Дата встречи
     * @param int $StorySite ID сайта
     * @param string $Name Имя
     * @param string $Note Дополнительное описание
     * @param int $avatar ID фото
     */
    public function storyUpdate($id, $Date, $StorySite, $Name, $Note, $avatar) {
        $data = ['SiteID' => $StorySite, 'Date' => $Date, 'Name' => $Name, 'Note' => $Note];

        if (is_numeric($avatar))
            $data['Avatar'] = $avatar;

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_STORY_NAME, $data);
    }

    /**
     * Удалить историю встречи клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function storyDelete($id) {
        $story = $this->db()->get_where(self::TABLE_CUSTOMER_STORY_NAME, array('ID' => $id))->row_array();
        if ($story['Avatar'] > 0) {
            $image = $this->db()->get_where(self::TABLE_IMAGE_NAME, ['ID' => $story['Avatar']])->row_array();

            if ($image) {
                $file = './files/images/'.$image['ID'].'.'.$image['ext'];
                if (file_exists($file)) unlink($file); // Удаление файла

                $this->db()->delete(self::TABLE_IMAGE_NAME, ['ID' => $image['ID']]); // Удаление записи из таблицы
            }
        }

        $this->db()->delete(self::TABLE_CUSTOMER_STORY_NAME, array('ID' => $id));
    }

    /**
     * Получить вопрос клиента
     *
     * @param int $id вопроса
     */
    public function questionGet($id) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_QUESTION_NAME, array('ID' => $id))->row_array();
    }

    /**
     * Добавление вопроса клиента
     *
     * @param int $idCustomer ID клиента
     * @param string $question вопрос клиента
     *
     * @return int ID записи
     */
    public function questionInsert($idCustomer, $question) {
        $this->db()->insert(self::TABLE_CUSTOMER_QUESTION_NAME, array('CustomerID' => $idCustomer, 'Question' => $question));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение ответа на вопрос клиента
     *
     * @param int $id вопроса
     * @param array $data массив полей для сохранения. Например: array('SName' => 'Иванова', 'FName' => 'Аня')
     */
    public function questionUpdate($id, $data) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_QUESTION_NAME, $data);
    }

    /**
     * Удалить вопрос клиента
     *
     * @param int $id ID записи в базе
     */
    public function questionDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_QUESTION_NAME, array('ID' => $id));
    }

    /**
     * Получить список видео клиента
     *
     * @param int $idCustomer ID клиента
     * @param int $type тип видео
     */
    public function videoGetList($idCustomer, $type) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_VIDEO_NAME)
            ->order_by('ID', 'DESC')
            ->where('CustomerID', $idCustomer)
            ->where('Type', $type)
            ->get()->result_array();
    }

    /**
     * Добавление видео клиента
     *
     * @param int $idCustomer ID клиента
     * @param string $link ссылка на видео
     * @param int $type тип видео
     *
     * @return int ID записи
     */
    public function videoInsert($idCustomer, $link, $type) {
        $this->db()->insert(self::TABLE_CUSTOMER_VIDEO_NAME, array('CustomerID' => $idCustomer, 'Link' => $link, 'Type' => $type));
        return $this->db()->insert_id();
    }

    /**
     * Удалить видео клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function videoDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_VIDEO_NAME, array('ID' => $id));
    }

    /**
     * Получить список видео-сайтов клиента
     *
     * @param int $idCustomer ID клиента
     */
    public function videoSiteGetList($idCustomer) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_VIDEO_SITE_NAME . ' AS cvs')
            ->select('cvs.*')
            ->join(self::TABLE_SITE_NAME . ' AS s', 's.ID = cvs.SiteID', 'inner')
            ->where('cvs.CustomerID', $idCustomer)
            ->order_by('s.Name', 'ASC')
            ->get()->result_array();
    }

    /**
     * Добавление видео-сайтов клиента
     *
     * @param int $idCustomer ID клиента
     * @param string $idSite сайт
     *
     * @return int ID записи
     */
    public function videoSiteInsert($idCustomer, $idSite) {
        $this->db()->insert(self::TABLE_CUSTOMER_VIDEO_SITE_NAME,
            array('CustomerID' => $idCustomer, 'SiteID' => $idSite));
        return $this->db()->insert_id();
    }

    /**
     * Сохранение информации о видео-сайте
     *
     * @param int $id клиента
     * @param array $data массив полей для сохранения. Например: array('SName' => 'Иванова', 'FName' => 'Аня')
     */
    public function videoSiteUpdate($id, $data) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_CUSTOMER_VIDEO_SITE_NAME, $data);
    }

    /**
     * Удалить видео-сайт клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function videoSiteDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_VIDEO_SITE_LINK_NAME, array('SiteID' => $id));
        $this->db()->delete(self::TABLE_CUSTOMER_VIDEO_SITE_NAME, array('ID' => $id));
    }

    /**
     * Получить список видео клиента
     *
     * @param int $idVideoSite видео-сайт
     * @param int $type тип видео
     */
    public function videoSiteLinkGetList($idVideoSite, $type) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_VIDEO_SITE_LINK_NAME)
            ->order_by('ID', 'DESC')
            ->where('SiteID', $idVideoSite)
            ->where('Type', $type)
            ->get()->result_array();
    }

    /**
     * Добавление видео клиента
     *
     * @param int $idVideoSite видео-сайт
     * @param string $link ссылка на видео
     * @param int $type тип видео
     *
     * @return int ID записи
     */
    public function videoSiteLinkInsert($idVideoSite, $link, $type) {
        $this->db()->insert(self::TABLE_CUSTOMER_VIDEO_SITE_LINK_NAME,
            ['Link' => $link, 'Type' => $type, 'SiteID' => $idVideoSite]);
        return $this->db()->insert_id();
    }

    /**
     * Удалить видео клиента из базы
     *
     * @param int $id ID записи в базе
     */
    public function videoSiteLinkDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_VIDEO_SITE_LINK_NAME, ['ID' => $id]);
    }

    /**
     * Получить список дней рождения
     *
     * @param int $idEmployee ID сотрудника за которым закреплены клиентки. Для директора и секретаря выбираются все дни рождения
     * @param string|bool $dtBegin начало периода. Если не указано, то выборка за текущий день
     * @param string|bool $dtEnd окончание периода. Если не указано, то выборка за текущий день
     *
     * @return array
     */
    public function getBirthdays($idEmployee, $dtBegin = false, $dtEnd = false) {
        $this->initBirthdaysQuery($idEmployee, $dtBegin, $dtEnd);
        return $this->db()->get()->result_array();
    }

    /**
     * Получить количество дней рождения
     *
     * @param int $idEmployee ID сотрудника за которым закреплены клиентки. Для директора и секретаря выбираются все дни рождения
     * @param string|bool $dtBegin начало периода. Если не указано, то выборка за текущий день
     * @param string|bool $dtEnd окончание периода. Если не указано, то выборка за текущий день
     *
     * @return int
     */
    public function getBirthdaysCount($idEmployee, $dtBegin = false, $dtEnd = false) {
        $this->initBirthdaysQuery($idEmployee, $dtBegin, $dtEnd);
        return $this->db()->count_all_results();
    }

    private function initBirthdaysQuery($idEmployee, $dtBegin = false, $dtEnd = false) {
        $employee = $this->db()
            ->from(self::TABLE_EMPLOYEE_NAME)
            ->where('ID', $idEmployee)
            ->get()->row_array();

        $this->db()
            ->select('c.ID, c.DOB, c.FName, c.SName')
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->where('c.IsDeleted', 0);

        if (!in_array($employee['UserRole'], array(USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY))) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c', 'es2c.CustomerID = c.ID AND es2c.IsDeleted=0', 'inner')
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS e2s', 'es2c.EmployeeSiteID = e2s.ID AND e2s.IsDeleted = 0 AND e2s.EmployeeID = '.$idEmployee, 'inner')
                ->group_by('c.ID');
        }

        if (!empty($dtBegin) && !empty($dtEnd)) {
            $startYear = (new DateTime($dtBegin))->format('Y');
            $endYear = (new DateTime($dtEnd))->format('Y');

            $this->db()->where(
                "(IF((MONTH('$dtBegin') < MONTH('$dtEnd')) OR ((MONTH('$dtBegin') = MONTH('$dtEnd')) AND ('$dtBegin' <= '$dtEnd')),
                        DATE_FORMAT(`DOB`, '%m-%d') BETWEEN DATE_FORMAT('$dtBegin', '%m-%d') AND DATE_FORMAT('$dtEnd', '%m-%d'),
                        (
                            (DATE_FORMAT(`DOB`, '%m-%d') BETWEEN DATE_FORMAT('$dtBegin', '%m-%d') AND DATE_FORMAT(CONCAT('$startYear', '-12-31'), '%m-%d'))
                                OR
                            (DATE_FORMAT(`DOB`, '%m-%d') BETWEEN DATE_FORMAT(CONCAT('$endYear', '-01-01'), '%m-%d') AND DATE_FORMAT('$dtEnd', '%m-%d'))
                        )
                ) = 1)", NULL, FALSE);
        } else {
            $this->db()->where("DATE_FORMAT(`DOB`,'%m-%d')", "DATE_FORMAT(NOW(),'%m-%d')", FALSE);
        }
    }

    /**
     * Поиск клиентов с нераспределенными по сотрудникам сайтами
     *
     * @param $siteID
     *
     * @return array
     */
    public function findFreeCustomerBySiteID($siteID) {
        // Поиск всех клиентов привязанных к сайту
        $customers = $this->db()
            ->select('c.ID, c.FName, c.SName')
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_CUSTOMER_SITE_NAME.' AS cs',
                'c.ID = cs.CustomerID AND cs.IsDeleted=0 AND cs.SiteID = '.$siteID, 'inner')
            ->order_by('c.SName, c.FName', 'ASC')
            ->get()->result_array();

        $data = [];

        // Поиск всех непривязанных клиентских сайтов к сотрудникам
        foreach ($customers as $customer) {
            $cross = $this->db()
                ->from(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME.' AS es2c')
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                    'es2c.EmployeeSiteID = es.ID AND es.IsDeleted = 0 AND es.SiteID = '.$siteID, 'inner')
                ->where('es2c.CustomerID', $customer['ID'])
                ->where('es2c.IsDeleted', 0)
                ->get()->row_array();

            if (empty($cross))
                $data[] = $customer;
        }

        return $data;
    }

    /**
     * Поиск клиентов привязанных к сайту
     *
     * @param $siteID
     *
     * @return array
     */
    public function findCustomerBySiteID($siteID) {
        // Поиск всех клиентов привязанных к сайту
        $customers = $this->db()
            ->select('c.ID, c.FName, c.SName')
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->join(self::TABLE_CUSTOMER_SITE_NAME.' AS cs',
                'c.ID = cs.CustomerID AND cs.IsDeleted=0 AND cs.SiteID = '.$siteID, 'inner')
            ->order_by('c.SName, c.FName', 'ASC')
            ->get()->result_array();

        return $customers;
    }

    /**
     * Поиск клиентов с нераспределенными по сотрудникам сайтами
     *
     * @param $siteID
     *
     * @return array
     */
    public function findFreeCustomerAllSites($siteID) {
        // Поиск всех клиентов привязанных к сайту
        $customers = $this->db()
            ->select('c.ID, c.FName, c.SName')
            ->from(self::TABLE_CUSTOMER_NAME.' AS c')
            ->where('IsDeleted', 0)
            ->order_by('c.SName, c.FName', 'ASC')
            ->get()->result_array();

        $data = [];

        // Поиск всех непривязанных клиентских сайтов к сотрудникам
        foreach ($customers as $customer) {
            $cross = $this->db()
                ->from(self::TABLE_CUSTOMER_SITE_NAME . ' AS cs')
                ->where('cs.CustomerID', $customer['ID'])
                ->where('cs.SiteID', $siteID)
                ->where('cs.IsDeleted', 0)
                ->get()->row_array();

            if (empty($cross))
                $data[] = $customer;
        }

        return $data;
    }

    /**
     * Получить минимальный и максимальный возраст клиенток
     *
     * @return mixed
     */
    public function getMinMaxAge() {
        // TODO: добавить сюда фильтрацию на основе прав доступа
        return $this->db()
            ->select('YEAR(NOW()) - YEAR(MAX(DOB)) AS min, YEAR(NOW()) - YEAR(MIN(DOB)) AS max', false)
            ->where('DOB !=', null)
            ->get(self::TABLE_CUSTOMER_NAME)->row_array();
    }

    /** Получить список вопросов для клиентской анкеты */
    public function questionTemplateGetList() {
        return $this->db()
            ->get(self::TABLE_CUSTOMER_QUESTION_TEMPLATE_NAME)->result_array();
    }


    /**
     * Добавить новый вопрос в клиентскую анкеты
     *
     * @param string $question текст вопроса
     */
    public function questionTemplateInsert($question) {
        $this->db()->insert(self::TABLE_CUSTOMER_QUESTION_TEMPLATE_NAME, ['question' => $question]);
        return $this->db()->insert_id();
    }

    /**
     * Редактирование текста вопроса клиентской анкеты
     *
     * @param int $id идентификатор записи
     * @param string $question текст вопроса
     */
    public function questionTemplateUpdate($id, $question) {
        $this->db()->update(self::TABLE_CUSTOMER_QUESTION_TEMPLATE_NAME, ['question' => $question], ['id' => $id]);
    }

    /**
     * Удалить вопрос из клиентской анкеты
     *
     * @param int $id идентификатор записи
     */
    public function questionTemplateDelete($id) {
        $this->db()->delete(self::TABLE_CUSTOMER_QUESTION_TEMPLATE_NAME, ['id' => $id]);
    }

    /**
     * Получить список вопросов с ответами
     *
     * @param int $CustomerID идентификатор клиента
     * @return mixed
     */
    public function questionAnswerGetList($CustomerID) {
        return $this->db()
            ->select("qt.id, qt.question, IFNULL(qa.answer, '') AS answer")
            ->from(self::TABLE_CUSTOMER_QUESTION_TEMPLATE_NAME . ' AS qt')
            ->join(self::TABLE_CUSTOMER_QUESTION_ANSWER_NAME . ' AS qa', "qa.questionID=qt.id AND qa.customerID=$CustomerID", 'left')
            ->get()->result_array();
    }

    /**
     * Сохранения ответа на вопрос
     *
     * @param int $CustomerID идентификатор клиента
     * @param int $id идентификатор вопроса
     * @param string $answer тест ответа на вопрос
     */
    public function questionAnswerSave($CustomerID, $id, $answer) {
        $record = $this->db()->get_where(self::TABLE_CUSTOMER_QUESTION_ANSWER_NAME,
                ['customerID' => $CustomerID, 'questionID' => $id])->row_array();

        if (empty($record)) {
            $this->db()->insert(self::TABLE_CUSTOMER_QUESTION_ANSWER_NAME,
                ['customerID' => $CustomerID, 'questionID' => $id, 'answer' => $answer]);
        } else {
            $this->db()->update(self::TABLE_CUSTOMER_QUESTION_ANSWER_NAME,
                ['answer' => $answer], ['customerID' => $CustomerID, 'questionID' => $id]);
        }
    }

    var $fields = [
        'SName' => 'Фамилия',
        'FName' => 'Имя',
        'MName' => 'Отчество',
        'DOB' => 'Дата рождения',
        'DateRegister' => 'Дата регистрации',
        'Avatar' => 'Аватар',
        'City' => 'Город',
        'Postcode' => 'Индекс',
        'Country' => 'Страна',
        'Address' => 'Адрес проживания',
        'Phone_1' => 'Телефон 1',
        'Phone_2' => 'Телефон 2',
        'Email' => 'E-Mail',
        'ProfessionOfDiploma' => 'Профессия (по диплому)',
        'CurrentWork' => 'Работа на данный момент',
        'Worship' => 'Вероисповедание',
        'PassportSeries' => 'Серия паспорта',
        'PassportNumber' => 'Номер паспорта',
        'PassportScan' => 'Скан паспорта',
        'HairColor' => 'Цвет волос',
        'BodyBuild' => 'Строение тела',
        'BodyBuildID' => 'Строение тела',
        'SizeFoot' => 'Размер Ноги',
        'Forming' => 'Образование',
        'MaritalStatus' => 'Семейное положение',
        'EyeColor' => 'Цвет глаз',
        'Status' => 'Статус клиента',
        'Height' => 'Рост',
        'Weight' => 'Вес',
        'Smoking' => 'Курение',
        'Alcohol' => 'Алкоголь',
        'WishesForManNationality' => 'Пожелание к мужчине - Национальность',
        'WishesForManAgeMin' => 'Пожелание к мужчине - Минимальный возраст',
        'WishesForManAgeMax' => 'Пожелание к мужчине - Максимальный возраст',
        'WishesForManWeight' => 'Пожелание к мужчине - Вес',
        'WishesForManHeight' => 'Пожелание к мужчине - Рост',
        'WishesForManText' => 'Пожелание к мужчине - Текст',
        'IsDeleted' => 'Флаг удаления',
        'ReasonForDeleted' => 'Причина удаления',
        'Temper' => 'Характер',
        'Interests' => 'Интересы',
        'Additionally' => 'Дополнительно',
        'Meetings' => 'Встречи',
        'Delivery' => 'Доставки',
        'ReservationContacts' => 'Заказ контактов',
        'DateLastPhotoSession' => 'Дата последней фотосессии',
        'Agreement' => 'Договора',
        'Children' => 'Дети',
        'Language' => 'Иностранные языки',
        'Question' => 'Вопросы',
        'Site' => 'Сайты',
        'Story' => 'История',
        'Album' => 'Фотоальбом',
        'Video' => 'Видео',
        'QuestionPhoto' => 'Вопросы',
        'Email_site' => 'E-Mail для сайта',
        'Email_private' => 'E-Mail клиентки',
        'VK' => 'URL Вконтакте',
        'Instagram' => 'URL Instagram',
        'Facebook' => 'URL Facebook',
    ];

    public function passportList($limit, $offset) {
        return $this->db()
            ->from(self::TABLE_CUSTOMER_PASSPORT_SCAN_NAME)
            ->limit($limit, $offset)
            ->get()->result_array();
    }

    public function rightsGetList($TargetCustomerID) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_DOCS_RIGHTS_NAME, array('TargetCustomerID' => $TargetCustomerID))->result_array();
    }

    public function rightsGet($TargetCustomerID, $EmployeeID) {
        return $this->db()->get_where(self::TABLE_CUSTOMER_DOCS_RIGHTS_NAME, array('EmployeeID' => $EmployeeID, 'TargetCustomerID' => $TargetCustomerID))->row_array();
    }

    /**
     * Удаления прав на документы и паспорт, которые не указаны в списке $TargetEmployees
     *
     * @param int $TargetCustomerID клиент
     * @param array $TargetEmployees новый список пользователей
     */
    public function rightsRemove($TargetCustomerID, $TargetEmployees) {
        // Выбираем записи для удаления
        $records = $this->db()
            ->where('TargetCustomerID', $TargetCustomerID)
            ->where_not_in('EmployeeID', $TargetEmployees)
            ->get(self::TABLE_CUSTOMER_DOCS_RIGHTS_NAME)->result_array();

        // Удаление связок в обе стороны
        foreach ($records as $cross) {
            $this->db()->delete(self::TABLE_CUSTOMER_DOCS_RIGHTS_NAME, ['EmployeeID' => $cross['EmployeeID'], 'TargetCustomerID' => $cross['TargetCustomerID']]);
        }
    }

    public function rightsInsert($TargetCustomerID, $EmployeeID) {
        $record = $this->rightsGet($TargetCustomerID, $EmployeeID);

        if (empty($record)) {
            $this->db()->insert(self::TABLE_CUSTOMER_DOCS_RIGHTS_NAME, array('EmployeeID' => $EmployeeID, 'TargetCustomerID' => $TargetCustomerID));
        }
    }

    /**
     * Получить список изменений карточки клиента
     *
     * @param int $CustomerID id клиента
     */
    public function historyGetList($CustomerID) {
        $records = $this->db()
            ->from(self::TABLE_CUSTOMER_HISTORY_NAME . ' AS h')
            ->select('h.*, e.FName, e.SName')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID=h.author', 'left')
            ->where('h.customer', $CustomerID)
            ->order_by('id', 'DESC')
            ->get()->result_array();

        foreach ($records as $key => $value) {
            // Если описания нет, то заполняем в него название поля
            if (empty($value['description'])) {
                $records[$key]['description'] = $this->fields[$value['field']];
            }
        }

        return $records;
    }

    public function customerUpdateNote($CustomerID, $EmployeeID, $fields, $description = null) {
        // Сохранение информации по полям
        foreach ($fields as $field) {
            $data = ['customer' => $CustomerID, 'author' => $EmployeeID, 'field' => $field];

            if ($description) {
                $data['description'] = $description;
            }

            $this->db()->insert(self::TABLE_CUSTOMER_HISTORY_NAME, $data);
        }

        // Получение истории (3 последних записей)
        $records = $this->db()
            ->from(self::TABLE_CUSTOMER_HISTORY_NAME . ' AS h')
            ->select('MAX(h.id) as idMax, h.*, e.FName, e.SName')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID=h.author', 'left')
            ->where('h.customer', $CustomerID)
            ->group_by('h.field, h.description')
            ->order_by('idMax', 'DESC')
            ->limit(3)
            ->get()->result_array();

        foreach ($records as $key => $value) {
            // Если описания нет, то заполняем в него название поля
            if (empty($value['description'])) {
                $records[$key]['description'] = $this->fields[$value['field']];
            }
        }

        // Обновляем примечание у клиента
        $this->customerUpdate($CustomerID, [
            'WhoUpdate' => $EmployeeID,
            'Note' => implode(', ', array_map(function($record) {
                return $record['description'];
            }, $records))
        ]);
    }

}