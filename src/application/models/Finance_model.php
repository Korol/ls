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
          `type` enum('office','charity','salary','exchange','photo') DEFAULT NULL,
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

    private $table_left = "
        CREATE TABLE `finance_left` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `card_id` int(11) DEFAULT NULL,
          `left_date` date DEFAULT NULL,
          `left_sum` decimal(10,2) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `card_id` (`card_id`),
          KEY `left_date` (`left_date`)
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
        $this->db()->query($this->table_left);
    }

    public function dropTables()
    {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_FINANCE_IN, TRUE);
        $this->dbforge->drop_table(self::TABLE_FINANCE_OUT, TRUE);
        $this->dbforge->drop_table(self::TABLE_FINANCE_EX, TRUE);
        $this->dbforge->drop_table(self::TABLE_FINANCE_LEFT, TRUE);
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
            $this->db()->join(self::TABLE_SITE_NAME . ' AS s', 's.ID = f.site_id', 'left');
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

    /**
     * остаток за прошлый день по наличным картам
     * @param $date дата
     * @param $nal_ids ID наличных карт
     * @return array
     */
    public function getLeft($date, $nal_ids)
    {
        if(empty($nal_ids)){
            return array();
        }
        // income
        $income = $this->db()
            ->select("`card_id`, SUM(`sum`) AS `summ`", null)
            ->where(
                array(
                    'created_date' => $date,
                )
            )
            ->where_in('card_id', $nal_ids)
            ->group_by('`card_id`')
            ->get(self::TABLE_FINANCE_IN)->result_array();
        $data['income'] = (!empty($income))
            ? toolIndexArrayBy($income, 'card_id')
            : array();
        // outcome
        $outcome = $this->db()
            ->select("`card_id`, SUM(`sum`) AS `summ`", null)
            ->where(
                array(
                    'created_date' => $date,
                )
            )
            ->where_in('card_id', $nal_ids)
            ->group_by('`card_id`')
            ->get(self::TABLE_FINANCE_OUT)->result_array();
        $data['outcome'] = (!empty($outcome))
            ? toolIndexArrayBy($outcome, 'card_id')
            : array();
        // exchange UAH
        $exchange = $this->db()
            ->select("SUM(`sum_uah`) AS `summ`", null)
            ->where(
                array(
                    'created_date' => $date,
                )
            )
            ->get(self::TABLE_FINANCE_EX)->row_array();
        $data['exchange'] = (!empty($exchange['summ']))
            ? $exchange['summ']
            : '0.00';
        // exchange out
        $exchange_out = $this->db()
            ->select("`card_id`, SUM(`sum_out`) AS `summ`", null)
            ->where(
                array(
                    'created_date' => $date,
                )
            )
            ->where_in('card_id', $nal_ids)
            ->group_by('`card_id`')
            ->get(self::TABLE_FINANCE_EX)->result_array();
        $data['exchange_out'] = (!empty($exchange_out))
            ? toolIndexArrayBy($exchange_out, 'card_id')
            : array();
        return $data;
    }

    /**
     * получаем остатки по картам за указанный день
     * @param $date
     * @param $card_ids
     * @return array
     */
    public function getLefts($date, $card_ids)
    {
        $return = array();
        if(empty($card_ids)){
            return $return;
        }

        $res = $this->db()
            ->select('card_id, left_sum')
            ->where('left_date', $date)
            ->where_in('card_id', $card_ids)
            ->get(self::TABLE_FINANCE_LEFT)->result_array();

        if(!empty($res)){
            $return = for_select($res, 'card_id', 'left_sum');
        }
        return $return;
    }

    /**
     * сохраняем остатки по картам за указанный день
     * @param $lefts - остатки
     * @param $date - текущий день в формате date('Y-m-d')
     * @return mixed
     */
    public function saveLefts($lefts, $date)
    {
        if(empty($lefts)) return 0;

        $affected = 0;
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        // получаем записи за предыдущий день
        $check_yesterday = $this->db()
            ->where('left_date', $yesterday)
            ->get(self::TABLE_FINANCE_LEFT)->result_array();
        // получаем записи за текущий день
        $check_today = $this->db()
            ->where('left_date', $date)
            ->get(self::TABLE_FINANCE_LEFT)->result_array();
//            ->count_all_results(self::TABLE_FINANCE_LEFT);
        // сохраняем остатки
        if(!empty($check_yesterday) && empty($check_today)){
            // есть записи за предыдущий – но нет записей за текущий день
            foreach ($check_yesterday as $ch){
                if(isset($lefts[$ch['card_id']])){
                    // суммируем вчерашнюю сумму по карте с текущей
                    $sum = $ch['left_sum'] + $lefts[$ch['card_id']];
                    // добавляем записи за сегодня
                    $this->db()->insert(
                        self::TABLE_FINANCE_LEFT,
                        array(
                            'card_id' => $ch['card_id'],
                            'left_date' => $date,
                            'left_sum' => $sum,
                        )
                    );
                    $affected++;
                }
            }
        }
        elseif (!empty($check_yesterday) && !empty($check_today)) {
            // есть записи и за предыдущий и за текущий день (обновление записей за этот день)
            foreach ($check_yesterday as $ct){
                if(isset($lefts[$ct['card_id']])) {
                    // суммируем вчерашнюю сумму по карте с текущей
                    $sum = $ct['left_sum'] + $lefts[$ct['card_id']];
                    // апдейтим суммы за текущий день
                    $this->db()->update(
                        self::TABLE_FINANCE_LEFT,
                        array('left_sum' => $sum),
                        array(
                            'card_id' => $ct['card_id'],
                            'left_date' => $date,
                        )
                    );
                    $affected++;
                }
            }
        }
        elseif (empty($check_yesterday) && !empty($check_today)){
            // записей за предыдущий день нет – а за текущий есть
            // (обновление первого дня учета остатков)
            foreach ($check_today as $cht){
                if(isset($lefts[$cht['card_id']])){
                    // апдейтим суммы за текущий день
                    $this->db()->update(
                        self::TABLE_FINANCE_LEFT,
                        array('left_sum' => $lefts[$cht['card_id']]),
                        array(
                            'card_id' => $cht['card_id'],
                            'left_date' => $date,
                        )
                    );
                    $affected++;
                }
            }
        }
        else{
            // нет записей ни за предыдущий день, ни за текущий день
            foreach ($lefts as $tk => $tv){
                // добавляем записи за текущий день
                $this->db()->insert(
                    self::TABLE_FINANCE_LEFT,
                    array(
                        'card_id' => $tk,
                        'left_date' => $date,
                        'left_sum' => $tv,
                    )
                );
                $affected++;
            }
        }
        return $affected;
    }

    public function truncateTotals()
    {
        $this->db()->truncate(self::TABLE_FINANCE_LEFT);
    }

    /**
     * баланс по всем картам (приход, расход, расход обмен, приход обмен, баланс)
     * @param $card_id
     * @param bool $nal_uah
     * @return mixed
     */
    public function getBalance($card_id, $nal_uah = false)
    {
        $return['in_ex']['total'] = 0;
        $return['in'] = $this->db()
            ->select('SUM(`sum`) AS `total`', false)
            ->where('card_id', $card_id)
            ->get(self::TABLE_FINANCE_IN)->row_array();
        $return['out'] = $this->db()
            ->select('SUM(`sum`) AS `total`', false)
            ->where('card_id', $card_id)
            ->get(self::TABLE_FINANCE_OUT)->row_array();
        $return['out_ex'] = $this->db()
            ->select('SUM(`sum_out`) AS `total`', false)
            ->where('card_id', $card_id)
            ->get(self::TABLE_FINANCE_EX)->row_array();
        if(!empty($nal_uah)){
            $return['in_ex'] = $this->db()
                ->select('SUM(`sum_uah`) AS `total`', false)
                ->get(self::TABLE_FINANCE_EX)->row_array();
        }
        // баланс = ((приход - (расход + расход по обмену)) + приход по обмену)
        $return['total'] = (
            ($return['in']['total'] - ($return['out']['total'] + $return['out_ex']['total']))
            + $return['in_ex']['total']
        );
        return $return;
    }

    /**
     * удаление остатков, начиная с даты $from
     * @param $from
     * @return mixed
     */
    public function removeLefts($from)
    {
        return $this->db()->delete(
            self::TABLE_FINANCE_LEFT,
            array('left_date >=' => $from)
        );
    }

    /**
     * информация об операции по ID и типу
     * @param $id
     * @param $type
     * @return array
     */
    public function getOperationInfo($id, $type)
    {
        if(!$this->checkType($type))
            return false;

        return $this->db()
            ->where('id', $id)
            ->get($this->types[$type])->row_array();
    }

    /**
     * дата последнего остатка – чтоб не допускать разрывов
     * @return string
     */
    public function getLastLeftDate()
    {
        $res = $this->db()
            ->select('left_date')
            ->order_by('left_date DESC')
            ->limit(1)
            ->get(self::TABLE_FINANCE_LEFT)->row_array();
        return (!empty($res['left_date'])) ? $res['left_date'] : '2017-10-20';
    }
}