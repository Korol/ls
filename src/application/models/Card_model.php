<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с кредитками
 */
class Card_model extends MY_Model
{

    private $table_card = "
        CREATE TABLE `assol_card` (
          `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `Name` varchar(255) DEFAULT NULL,
          `Number` varchar(255) DEFAULT NULL,
          `Currency` varchar(255) DEFAULT NULL,
          `Created` datetime DEFAULT NULL,
          `Author` int(11) DEFAULT NULL,
          `Active` tinyint(1) NOT NULL DEFAULT '1',
          `Deleted` tinyint(1) NOT NULL DEFAULT '0',
          PRIMARY KEY (`ID`),
          KEY `active` (`Active`),
          KEY `Deleted` (`Deleted`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    /**
     * Инициализация таблицы
     */
    public function initDataBase()
    {
        $this->db()->query($this->table_card);
    }

    public function dropTables()
    {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_CARD_NAME, TRUE);
    }

    /**
     * Список карт
     * @param bool $active только активные карты (для таблицы)
     * @return mixed
     */
    public function getCardsList($active = true)
    {
        if($active)
            $this->db()->where(
                array(
                    'Active' => 1,
                    'Deleted' => 0
                )
            );
        return $this->db()
            ->get(self::TABLE_CARD_NAME)->result_array();
    }

    /**
     * Список карт с ФИО авторов
     * @param bool $active только активные карты (для таблицы)
     * @return mixed
     */
    public function getCardsListWithAuthor($active = true)
    {
        if($active)
            $this->db()->where(
                array(
                    'Active' => 1,
                    'Deleted' => 0
                )
            );
        return $this->db()
            ->select('c.*, e.SName, e.FName')
            ->from(self::TABLE_CARD_NAME . ' AS c')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = c.id')
            ->get()->result_array();
    }

    /**
     * добавляем карту
     * @param $data
     * @return mixed
     */
    public function insertCard($data)
    {
        $this->db()->insert(
            self::TABLE_CARD_NAME,
            $data
        );
        return $this->db()->affected_rows();
    }

    /**
     * обновляем карту
     * @param $data
     * @param $id
     * @return mixed
     */
    public function updateCard($data, $id)
    {
        $this->db()->update(
            self::TABLE_CARD_NAME,
            $data,
            array('ID' => $id)
        );
        return $this->db()->affected_rows();
    }

    /**
     * получаем одну карту
     * @param $id
     * @param bool $active
     * @return mixed
     */
    public function getCard($id, $active = true)
    {
        if($active)
            $this->db()->where(
                array(
                    'Active' => 1,
                    'Deleted' => 0
                )
            );
        return $this->db()
            ->where('ID', $id)
            ->limit(1)
            ->get(self::TABLE_CARD_NAME)->row_array();
    }
}