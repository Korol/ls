<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с кредитками
 */
class Card_model extends MY_Model
{

    private $table_card = "
        CREATE TABLE `finance_card` (
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

        $this->dbforge->drop_table(self::TABLE_FINANCE_CARD, TRUE);
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
            ->where('Deleted', 0)
            ->order_by('Nal ASC, Name ASC, Currency ASC')
            ->get(self::TABLE_FINANCE_CARD)->result_array();
    }

    /**
     * Список карт для редактирования в Админке
     * без карт с флагом Nal=1 (это наличные в трёх валютах)
     * @return mixed
     */
    public function getAdminCardsList()
    {
        $this->db()->where(
            array(
                'Nal' => 0,
                'Deleted' => 0
            )
        );
        return $this->db()
            ->get(self::TABLE_FINANCE_CARD)->result_array();
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
            ->from(self::TABLE_FINANCE_CARD . ' AS c')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = c.id')
            ->where('c.Deleted', 0)
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
            self::TABLE_FINANCE_CARD,
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
            self::TABLE_FINANCE_CARD,
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
            ->where('Deleted', 0)
            ->limit(1)
            ->get(self::TABLE_FINANCE_CARD)->row_array();
    }

    /**
     * получаем одну карту для редактирования в Админке
     * @param $id
     * @return mixed
     */
    public function getAdminCard($id)
    {
        $this->db()->where(
            array(
                'Nal' => 0,
                'Deleted' => 0
            )
        );
        return $this->db()
            ->where('ID', $id)
            ->limit(1)
            ->get(self::TABLE_FINANCE_CARD)->row_array();
    }

    /**
     * удаление карты
     * физически НЕ удаляем – только ставим отметку Deleted = 1
     * и снимаем активность (Active = 0)
     * @param $id
     * @return mixed
     */
    public function deleteCard($id)
    {
        $this->db()->update(
            self::TABLE_FINANCE_CARD,
            array(
                'Active' => 0,
                'Deleted' => 1
            ),
            array('ID' => $id)
        );

        return $this->db()->affected_rows();
    }

    /**
     * список наличных карт
     * @param bool $active
     * @return array
     */
    public function getNalCards($active = true)
    {
        $result = $this->db()
            ->where(
                array(
                    'Active' => 1,
                    'Deleted' => 0,
                    'Nal' => 1
                )
            )
            ->get(self::TABLE_FINANCE_CARD)->result_array();
        return (!empty($result)) ? toolIndexArrayBy($result, 'ID') : array();
    }
}