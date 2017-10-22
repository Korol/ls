<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * работа с Финансовой таблицей
 */
class Finance_model extends MY_Model
{
    private $table_in = "
        CREATE TABLE `finance_in` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `type` enum('receipts','meeting','western','apartment','transfer','reserve','exchange_in') DEFAULT NULL,
          `sum` decimal(10,2) NOT NULL DEFAULT '0.00',
          `currency` enum('USD','EUR','UAH') DEFAULT NULL,
          `card_id` int(11) DEFAULT NULL,
          `created_date` date DEFAULT NULL,
          `created_ts` datetime DEFAULT NULL,
          `author_id` int(11) DEFAULT NULL,
          `site_id` int(11) DEFAULT NULL,
          `comment` text,
          PRIMARY KEY (`id`),
          KEY `type` (`type`),
          KEY `currency` (`currency`),
          KEY `card_id` (`card_id`),
          KEY `created_date` (`created_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    private $table_out = "
        CREATE TABLE `finance_out` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `type` enum('office','charity','salary','exchange') DEFAULT NULL,
          `sum` decimal(10,2) NOT NULL DEFAULT '0.00',
          `currency` enum('USD','EUR','UAH') DEFAULT NULL,
          `card_id` int(11) DEFAULT NULL,
          `created_date` date DEFAULT NULL,
          `created_ts` datetime DEFAULT NULL,
          `author_id` int(11) DEFAULT NULL,
          `site_id` int(11) DEFAULT NULL,
          `employee_id` int(11) DEFAULT NULL,
          `comment` text,
          PRIMARY KEY (`id`),
          KEY `type` (`type`),
          KEY `currency` (`currency`),
          KEY `card_id` (`card_id`),
          KEY `created_date` (`created_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    private $table_ex = "
        CREATE TABLE `finance_ex` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `sum_out` decimal(10,2) NOT NULL DEFAULT '0.00',
          `card_id` int(11) DEFAULT NULL,
          `currency` enum('USD','EUR','UAH') DEFAULT NULL,
          `rate` varchar(20) DEFAULT NULL,
          `sum_uah` decimal(10,2) NOT NULL DEFAULT '0.00',
          `created_date` date DEFAULT NULL,
          `created_ts` datetime DEFAULT NULL,
          `author_id` int(11) DEFAULT NULL,
          `comment` text,
          PRIMARY KEY (`id`),
          KEY `card_id` (`card_id`),
          KEY `currency` (`currency`),
          KEY `created_date` (`created_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

    public $types = array(
        'income' => self::TABLE_FINANCE_IN,
        'outcome' => self::TABLE_FINANCE_OUT,
        'exchange' => self::TABLE_FINANCE_EX,
    );

    /**
     * проверка типа операции: Приход, Расход, Обмен
     * @param $type
     * @return bool
     */
    public function checkType($type)
    {
        if(!in_array($type, array_keys($this->types)))
            return false;
        else
            return true;
    }

    /**
     * Инициализация таблицы
     */
    public function initDataBase()
    {
        $this->db()->query($this->table_in);
        $this->db()->query($this->table_out);
        $this->db()->query($this->table_ex);
    }

    public function dropTables()
    {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_FINANCE_IN, TRUE);
        $this->dbforge->drop_table(self::TABLE_FINANCE_OUT, TRUE);
        $this->dbforge->drop_table(self::TABLE_FINANCE_EX, TRUE);
    }

    /**
     * добавление операции в таблицы Приход или Расход
     * @param $type
     * @param $data
     * @return bool
     */
    public function addOperation($type, $data)
    {
        if(!$this->checkType($type))
            return false;

        $this->db()->insert(
            $this->types[$type],
            $data
        );
        return ($this->db()->affected_rows() > 0) ? true : false;
    }

    /**
     * операции Приход или Расход за период
     * @param $from
     * @param $to
     * @param $type
     * @return mixed
     */
    public function getInOrOut($from, $to, $type)
    {
        if(!$this->checkType($type))
            return false;

        $where = ($from == $to)
            ? array('created_date' => $from)
            : array(
                'created_date >=' => $from,
                'created_date <=' => $to,
            );
        $res = $this->db()
            ->select("`card_id`, `type`, SUM(`sum`) AS `summ`", null)
            ->where($where)
            ->group_by('`card_id`, `type`')
            ->get($this->types[$type])->result_array();
        return $res;
    }

    /**
     * операции Обмен за период
     * @param $from
     * @param $to
     * @return mixed
     */
    public function getEx($from, $to)
    {
        $where = ($from == $to)
            ? array('created_date' => $from)
            : array(
                'created_date >=' => $from,
                'created_date <=' => $to,
            );
        $res = $this->db()
            ->select("`card_id`, SUM(`sum_out`) AS `summ_out`, SUM(`sum_uah`) AS `summ_uah`", null)
            ->where($where)
            ->group_by('`card_id`')
            ->get(self::TABLE_FINANCE_EX)->result_array();
        return $res;
    }

    /**
     * операции Приход или Расход определенного типа (`type`) за период
     * @param $type
     * @param $operation_type
     * @param $from
     * @param $to
     * @return bool|array
     */
    public function getOperation($type, $operation_type, $from, $to)
    {
        if(!$this->checkType($type))
            return false;

        $where = ($from == $to)
            ? array('created_date' => $from)
            : array(
                'created_date >=' => $from,
                'created_date <=' => $to,
            );
        $select = "f.*, CONCAT(c.Name, ', ', c.Currency) AS 'card_name'";
        if(in_array($type, array('income', 'outcome'))){
            $select .= ", s.Name AS 'site_name'";
            $this->db()->join(self::TABLE_SITE_NAME . ' AS s', 's.ID = f.site_id');
            $this->db()->where('type', $operation_type);
        }
        if($type == 'outcome'){
            $select .= ", IF(f.employee_id>0, CONCAT(e.SName, ' ', LEFT(e.FName, 1), '. ', LEFT(e.MName, 1), '.'), '') AS 'employee_name'";
            $this->db()->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = f.employee_id', 'left');
        }
        $res = $this->db()
            ->select($select, null)
            ->from($this->types[$type] . ' AS f')
            ->join(self::TABLE_FINANCE_CARD . ' AS c', 'c.ID = f.card_id')
            ->where($where)
            ->order_by('f.created_ts ASC')
            ->get()->result_array(); log_message('error', $this->db()->last_query());
        return $res;
    }

    /**
     * удаление операции
     * @param $id
     * @param $type
     * @return bool
     */
    public function removeOperation($id, $type)
    {
        if(!$this->checkType($type))
            return false;

        $this->db()->delete($this->types[$type], array('id' => $id));
        return true;
    }
}