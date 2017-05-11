<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с новостями
 */
class News_model extends MY_Model {

    private $table_news
        = "CREATE TABLE IF NOT EXISTS `assol_news` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `SiteID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта для публикации',
            `CustomerID` INT(11) NOT NULL COMMENT 'Уникальный номер сайта для публикации',
            `Title` VARCHAR(512) NOT NULL COMMENT 'Заголовок новости',
            `Text` MEDIUMTEXT NOT NULL COMMENT 'Текст новости',
            `Who` INT(11) NOT NULL COMMENT 'Автор статьи',
            `DateCreate` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата создания',
            `DateUpdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата последнего редактирования',
            `ImageID` INT(11) COMMENT 'Миниатюра новости',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Новости';";

    private $table_news_read
        = "CREATE TABLE IF NOT EXISTS `assol_news_read` (
            `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `NewsID` INT(11) NOT NULL COMMENT 'Уникальный номер новости',
            `EmployeeID` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
            PRIMARY KEY (`ID`),
            FOREIGN KEY (`NewsID`) REFERENCES `assol_news` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE,
            FOREIGN KEY (`EmployeeID`) REFERENCES `assol_employee` (`ID`)
                ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Состояние прочтения новостей сотрудником';";

    /** Инициализация таблицы */
    public function initDataBase() {
        $this->db()->query($this->table_news);
        $this->db()->query($this->table_news_read);
    }

    /** Удаление таблиц */
    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_NEWS_READ_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_NEWS_NAME, TRUE);
    }

    /**
     * Получить список новостей
     *
     * @param int|bool $employee ID сотрудника для возможности фильтрации новостей для ролей "Сотрудник" и "Переводчик". Если фильтрация не нужна, то False
     * @param array $data
     *
     * @return mixed
     */
    public function newsGetList($employee, $data) {
        // ID сайта для фильтрации или 0 для всех сайтов
        $SiteID = $data['category'];

        $this->db()
            ->select("news.*, e.FName, e.SName, CONCAT(img.ID, '.', img.ext) as 'FileName'")
            ->from(self::TABLE_NEWS_NAME.' AS news')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'news.ImageID = img.ID', 'left')
            ->join(self::TABLE_EMPLOYEE_NAME.' AS e', 'e.ID = news.Who', 'left');

        // Фильтрация для ролей "Сотрудник" и "Переводчик"
        if ($employee) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_NAME.' AS user',
                    'user.ID = '.$employee, 'inner')
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                    '(user.ID = es.EmployeeID AND es.IsDeleted = 0 AND news.SiteID = es.SiteID)', 'left', false)
                // Фильтрация по клиентам
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS esT',
                    '(user.ID = esT.EmployeeID AND esT.IsDeleted = 0)', 'left', false)
                ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME . ' AS es2c',
                    '(es2c.IsDeleted = 0 AND es2c.EmployeeSiteID=esT.ID AND es2c.CustomerID=news.CustomerID)', 'left', false)
                ->where('(news.SiteID=0 OR es.ID IS NOT NULL)', null, false) // Если сайт не указан или есть связка сайта с сотрудником
                ->where('(news.CustomerID=0 OR es2c.ID IS NOT NULL)', null, false); // Если клинт не указан или есть связка клиента с сотрудником
        }

        // Фильтр по сайту
        if (!empty($SiteID))
            $this->db()->where('news.SiteID', $SiteID);


        // Групировка по сайту
        $this->db()->group_by('news.ID');

        return array(
            'count' => $this->db()->count_all_results('', FALSE),
            'records' => $this->db()
                ->order_by('news.DateCreate', 'DESC')
                ->limit($data['Limit'], $data['Offset'])
                ->get()->result_array()
        );
    }

    /**
     * Получить конкретную новость
     *
     * @param int $id ID новости
     *
     * @return mixed
     */
    public function newsGet($id) {
        return $this->db()->get_where(self::TABLE_NEWS_NAME, array('ID' => $id))->row_array();
    }

    /**
     * Добавление новости
     *
     * @param int       $SiteID     Уникальный номер сайта для публикации
     * @param int       $Customer   Уникальный номер клиента
     * @param string    $Title      Заголовок новости
     * @param string    $Text       Текст новости
     * @param int       $ImageID    ID фото
     * @param int       $Who        Автор статьи
     *
     * @return int ID нового клиента
     */
    public function newsInsert($Title, $Text, $SiteID, $Customer, $ImageID, $Who) {
        $data = array(
            'SiteID' => empty($SiteID) ? 0 : $SiteID,
            'CustomerID' => empty($Customer) ? 0 : $Customer,
            'Title' => $Title,
            'Text' => $Text,
            'Who' => $Who,
            'ImageID' => $ImageID
        );
        $this->db()->set('DateCreate', 'NOW()', FALSE);
        $this->db()->insert(self::TABLE_NEWS_NAME, $data);

        $idNews = $this->db()->insert_id();
        // Сразу выставляем метку прочтения для автора
        $this->db()->insert(self::TABLE_NEWS_READ_NAME, ['EmployeeID' => $Who, 'NewsID' => $idNews]);

        return $idNews;
    }

    /**
     * Редактирование новости
     *
     * @param int $id новости
     * @param int       $SiteID     Уникальный номер сайта для публикации
     * @param int       $Customer   Уникальный номер клиента
     * @param string    $Title      Заголовок новости
     * @param string    $Text       Текст новости
     * @param int       $ImageID    ID фото
     * @param int       $Who        Автор статьи
     */
    public function newsUpdate($id, $Title, $Text, $SiteID, $Customer, $ImageID, $Who) {
        if (is_numeric($ImageID)) {
            $this->newsClearImage($id);
        }

        $data = array(
            'SiteID' => empty($SiteID) ? 0 : $SiteID,
            'CustomerID' => empty($Customer) ? 0 : $Customer,
            'Title' => $Title,
            'Text' => $Text,
            'Who' => $Who
        );

        if (!empty($ImageID)){
            $data['ImageID'] = $ImageID;
        }

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_NEWS_NAME, $data);
    }

    /**
     * Удалить новость из базы
     *
     * @param int $id ID записи в базе
     */
    public function newsDelete($id) {
        $this->newsClearImage($id);
        $this->db()->delete(self::TABLE_NEWS_NAME, array('ID' => $id));
    }

    /**
     * Зачистка миниатюры новости
     * @param int $id ID новости
     */
    private function newsClearImage($id) {
        $news = $this->newsGet($id);
        if (!empty($news['ImageID'])) {

            $image = $this->db()->get_where(self::TABLE_IMAGE_NAME, ['ID' => $news['ImageID']])->row_array();

            if ($image) {
                $file = './files/images/'.$image['ID'].'.'.$image['ext'];
                if (file_exists($file)) unlink($file); // Удаление файла

                $this->db()->delete(self::TABLE_IMAGE_NAME, ['ID' => $image['ID']]); // Удаление записи из таблицы
            }
        }
    }

    /**
     * Получить список сайтов
     *
     * @param int|bool $employee ID сотрудника для возможности фильтрации новостей для ролей "Сотрудник" и "Переводчик". Если фильтрация не нужна, то False
     *
     * @return mixed
     */
    public function getSites($employee) {
        $this->db()
            ->select('s.*')
            ->from(self::TABLE_SITE_NAME.' AS s')
            ->join(self::TABLE_NEWS_NAME.' AS news', 'news.SiteID = s.ID', 'inner');

        // Фильтрация для ролей "Сотрудник" и "Переводчик"
        if ($employee) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_NAME.' AS user',
                    'user.ID = '.$employee, 'inner')
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                    '(user.ID = es.EmployeeID AND es.IsDeleted = 0 AND news.SiteID = es.SiteID) OR (news.SiteID = 0)', 'inner', false);
        }

        return $this->db()
            ->group_by('s.ID')
            ->order_by('s.Name', 'ASC')
            ->get()->result_array();
    }

    /**
     * Получить количество непрочитанных новостей
     *
     * @param int $employee ID сотрудника
     * @param bool $filter флаг для возможности фильтрации новостей для ролей "Сотрудник" и "Переводчик"
     * @return int
     */
    public function getCountUnreadNews($employee, $filter) {
        $this->initUnreadNewsQuery($employee, $filter);

        return $this->db()->count_all_results();
    }

    /**
     * Выставить метку прочтения новостей
     *
     * @param int $employee ID сотрудника
     * @param bool $filter флаг для возможности фильтрации новостей для ролей "Сотрудник" и "Переводчик"
     */
    public function newsRead($employee, $filter) {
        // Получения непрочитанных новостей
        $this->initUnreadNewsQuery($employee, $filter);
        $newsList = $this->db()->get()->result_array();

        // Ставим метку прочтения
        foreach ($newsList as $news)
            $this->db()->insert(self::TABLE_NEWS_READ_NAME, ['EmployeeID' => $employee, 'NewsID' => $news['ID']]);
    }

    /**
     * Подготовка запроса непрочитанных сообщений
     *
     * @param int $employee ID сотрудника
     * @param bool $filter флаг для возможности фильтрации новостей для ролей "Сотрудник" и "Переводчик"
     */
    private function initUnreadNewsQuery($employee, $filter) {
        $this->db()
            ->select('n.*')
            ->from(self::TABLE_NEWS_NAME.' AS n')
            ->join(self::TABLE_NEWS_READ_NAME.' AS nr',
                "nr.EmployeeID = $employee AND n.ID = nr.NewsID", 'left');

        // фильтрации новостей для ролей "Сотрудник" и "Переводчик"
        if ($filter) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS es',
                    "(es.EmployeeID = $employee AND es.IsDeleted = 0 AND n.SiteID = es.SiteID)", 'left', false)
                // Фильтрация по клиентам
                ->join(self::TABLE_EMPLOYEE_SITE_NAME.' AS esT',
                    "(esT.EmployeeID = $employee  AND esT.IsDeleted = 0)", 'left', false)
                ->join(self::TABLE_EMPLOYEE_SITE_CUSTOMER_NAME . ' AS es2c',
                    '(es2c.IsDeleted = 0 AND es2c.EmployeeSiteID=esT.ID AND es2c.CustomerID=n.CustomerID)', 'left', false)
                ->where('(n.SiteID=0 OR es.ID IS NOT NULL)', null, false) // Если сайт не указан или есть связка сайта с сотрудником
                ->where('(n.CustomerID=0 OR es2c.ID IS NOT NULL)', null, false); // Если клинт не указан или есть связка клиента с сотрудником
        }

        // Берем только записи без метки в таблице статусов
        $this->db()->where(['nr.ID' => null]);

        // Групировка новостей по ID
        $this->db()->group_by('n.ID');
    }

}