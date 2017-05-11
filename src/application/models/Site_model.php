<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с сайтами
 */
class Site_model extends MY_Model {

    private $table = "CREATE TABLE IF NOT EXISTS `assol_sites` (
                          `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
                          `Name` VARCHAR(256) NOT NULL COMMENT 'Название сайта',
                          `Domen` VARCHAR(256) NOT NULL COMMENT 'Домен сайта',
                          `Note` TEXT NOT NULL COMMENT 'Примечание',
                          `IsDealer` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Флаг Дилерские',
                          PRIMARY KEY (`ID`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сайты';";


    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_SITE_NAME, TRUE);
    }

    /**
     * Получить список сайтов
     *
     * @return mixed
     */
    public function getRecords() {
        return $this->db()
            ->order_by('Name', 'ASC')
            ->get(self::TABLE_SITE_NAME)->result_array();
    }

    /**
     * Получить информацию о сайте
     *
     * @param int $id ID сайта в системе
     *
     * @return mixed
     */
    public function getRecord($id) {
        return $this->db()->get_where(self::TABLE_SITE_NAME, array('ID' => $id))->row_array();
    }

    /**
     * Добавление нового сайта в систему
     *
     * @param string $name Название сайта
     * @param string $domen Домен сайта
     * @param string $note Примечание
     * @param string $isDealer Флаг Дилерские
     */
    public function add($name, $domen, $note, $isDealer) {
        $data = array(
            'Name' => $name,
            'Domen' => $domen,
            'Note' => $note,
            'IsDealer' => $isDealer
        );
        $this->db()->insert(self::TABLE_SITE_NAME, $data);
    }

    /**
     * Удалить сайт из системы
     *
     * @param int $id ID сайта в системе
     */
    public function remove($id) {
        $this->db()->delete(self::TABLE_SITE_NAME, array('ID' => $id));
    }

    /**
     * Редактирование сайта
     *
     * @param int $id ID сайта в системе
     * @param string $name Название сайта
     * @param string $domen Домен сайта
     * @param string $note Примечание
     * @param string $isDealer Флаг Дилерские
     */
    public function update($id, $name, $domen, $note, $isDealer) {
        $data = array(
            'Name' => $name,
            'Domen' => $domen,
            'Note' => $note,
            'IsDealer' => $isDealer
        );

        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SITE_NAME, $data);
    }

}