<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * работа с Финансовой таблицей
 */

class Finance extends MY_Controller
{
    // ключи массивов ниже связаны с ENUM-полями в финансовых таблицах БД!!!
    public $types_in = array(
        'receipts' => 'Поступление',
        'meeting' => 'Встреча',
        'western' => 'Вестерн',
        'apartment' => 'Квартира',
        'transfer' => 'Трансфер',
        'exchange_in' => 'Обмен',
        'reserve' => 'Резерв',
    );
    public $types_out = array(
        'office' => 'Офис',
        'charity' => 'Благотворительность',
        'salary' => 'Зарплата',
        'photo' => 'Фото',
        'exchange_out' => 'Обмен',
    );

    public $cards = array();

    public function __construct()
    {
        parent::__construct();
        $this->assertUserRight();
    }

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'])
            show_error('Данный раздел доступен только для роли "Директор"', 403, 'Доступ запрещен');
    }

    /**
     * загрузка страницы с таблицей
     */
    public function index()
    {
        $data['currencies'] = $this->currencies;
        $data['sites'] = $this->getSiteModel()->getRecords();
        $data['cards'] = $this->getCardModel()->getCardsList();
        $data['employees'] = $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole());
        $data['types_in'] = $this->types_in;
        $data['types_out'] = $this->types_out;
        $this->viewHeader(array(), 'header_wide');
        $this->view('form/finance/index', $data);
        $this->viewFooter();
    }

    /**
     * выдаём данные по AJAX-запросу
     */
    public function data() {
        try {
            $from = $this->input->post('from', true);
            $to = $this->input->post('to', true);
            if (empty($from) || empty($to))
                throw new RuntimeException("Не указана дата для запроса");

            $records = $this->getData($this->convertDate($from), $this->convertDate($to));
            $html = $this->load->view('form/finance/table',
                array('records' => $records),
                true
            );
            echo $html;
//            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
//            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            echo '<b>Error!</b>';
        }
    }

    /**
     * Array
    (
    [operationType] => income
    [modalDate] => 20-10-2017
    [modalInSite] => 39
    [modalInType] => receipts
    [modalInCard] => 1
    [modalInSum] => 8908
    [modalOutSite] => 39
    [modalOutType] => office
    [modalOutEmployee] => 0
    [modalOutCard] => 1
    [modalOutSum] =>
    [modalExSumOut] =>
    [modalExCard] => 1
    [modalExRate] =>
    [modalExSumUah] =>
    [modalComment] => njnkjnkn
    )
     * добавление операции в БД
     */
    public function add()
    {
        $types = array('income', 'outcome', 'exchange');
        $error_tail = ' Проверьте правильность заполнения полей формы!';
        try {
            $form = $this->input->post('form', true);
            if (empty($form))
                throw new RuntimeException("Нет данных для обработки!" . $error_tail);

            parse_str($form, $data);
            if(empty($data['operationType']) || !in_array($data['operationType'], $types))
                throw new RuntimeException("Неверно указан тип операции!" . $error_tail);

            switch ($data['operationType']){
                case 'income':
                    if(!$this->income($data))
                        throw new RuntimeException("Ошибка при зачислении средств!" . $error_tail);
                    break;
                case 'outcome':
                    if(!$this->outcome($data))
                        throw new RuntimeException("Ошибка при списании средств!" . $error_tail);
                    break;
                case 'exchange':
                    if(!$this->exchange($data))
                        throw new RuntimeException("Ошибка при обмене средств!" . $error_tail);
                    break;
                default:
                    throw new RuntimeException("Нет обработчика для данного типа операций!" . $error_tail);
            }

            $this->json_response(array("status" => 1, 'message' => 'Операция успешно добавлена!'));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    /**
     * приход
     * @param $data
     * @return bool
     */
    public function income($data)
    {
        $return = false;
        $card = $this->getCard($data['modalInCard']);
        $in = array(
            'type' => $data['modalInType'],
            'sum' => $this->convertSum($data['modalInSum']),
            'currency' => $card['Currency'],
            'card_id' => $data['modalInCard'],
            'created_date' => $this->convertDate($data['modalDate']),
            'created_ts' => $this->convertDate($data['modalDate']) . ' ' . date('H:i:s'),
            'author_id' => $this->getUserID(),
            'site_id' => $data['modalInSite'],
            'comment' => $data['modalComment'],
        );
        if($this->getFinanceModel()->addOperation('income', $in)){
            $return = true;
        }
        return $return;
    }

    /**
     * расход
     * @param $data
     * @return bool
     */
    public function outcome($data)
    {
        $return = false;
        $card = $this->getCard($data['modalOutCard']);
        $out = array(
            'type' => $data['modalOutType'],
            'sum' => $this->convertSum($data['modalOutSum']),
            'currency' => $card['Currency'],
            'card_id' => $data['modalOutCard'],
            'created_date' => $this->convertDate($data['modalDate']),
            'created_ts' => $this->convertDate($data['modalDate']) . ' ' . date('H:i:s'),
            'author_id' => $this->getUserID(),
            'site_id' => $data['modalOutSite'],
            'employee_id' => $data['modalOutEmployee'],
            'comment' => $data['modalComment'],
        );
        if($this->getFinanceModel()->addOperation('outcome', $out)){
            $return = true;
        }
        return $return;
    }

    /**
     * обмен
     * @param $data
     * @return bool
     */
    public function exchange($data)
    {
        $return = false;
        $card = $this->getCard($data['modalExCard']);
        $ex = array(
            'sum_out' => $this->convertSum($data['modalExSumOut']),
            'card_id' => $data['modalExCard'],
            'currency' => $card['Currency'],
            'rate' => $data['modalExRate'],
            'sum_uah' => $this->convertSum($data['modalExSumUah']),
            'created_date' => $this->convertDate($data['modalDate']),
            'created_ts' => $this->convertDate($data['modalDate']) . ' ' . date('H:i:s'),
            'author_id' => $this->getUserID(),
            'comment' => $data['modalComment'],
        );
        if($this->getFinanceModel()->addOperation('exchange', $ex)){
            $return = true;
            // снимаем средства с выбранной карты за сегодня ?
            // добавляем разменяную сумму к Наличные UAH ?
        }
        return $return;
    }

    /**
     * преобразуем строку из формата клиента в формат БД
     * 20-10-2017 --> 2017-10-20
     * @param $date
     * @return string
     */
    public function convertDate($date)
    {
        $date_ex = explode('-', $date);
        return (count($date_ex) == 3)
            ? $date_ex[2] . '-' . $date_ex[1] . '-' . $date_ex[0]
            : date('Y-m-d');
    }

    /**
     * преобразование суммы
     * запятые меняем на точки
     * к целым числам добавляем .00
     * @param $sum
     * @return string
     */
    public function convertSum($sum)
    {
        if(is_numeric($sum)){
            $sum = str_replace(',', '.', $sum);
            return (strpos($sum, '.') === false) ? (int)$sum . '.00' : number_format($sum, 2, '.', '');
        }
        else{
            return '0.00';
        }
    }

    /**
     * информация о карте по ID
     * @param $id
     * @return mixed
     */
    public function getCard($id)
    {
        return $this->getCardModel()->getCard($id);
    }

    /**
     * All Magic is here!)))
     * !!! Итого по обмену всегда идет плюс к наличным, UAH, минус - с выбранной карты или наличных !!!
     * @param $from
     * @param $to
     * @return array
     */
    public function getData($from, $to)
    {
        $cards = $this->getCards();
        $left = $this->getLeft($from);
        $income = $this->getInOutData($from, $to, 'income');
        $outcome = $this->getInOutData($from, $to, 'outcome');
        $exchange = $this->getExData($from, $to);
        $exchange_uah_sum = $this->calcExUahSum($exchange);
        $result = array();
        if(!empty($cards)){
            $i = 0;
            foreach ($cards as $card) {
                $result[$i]['card_name'] = $card['Name'] . ', ' . $card['Currency'];
                // остаток с прошлого дня по наличным картам
//                if(($card['Nal'] == 1) && !empty($left[$card['ID']])){
                // остаток с прошлого дня по всем картам
                if(!empty($left[$card['ID']])){
                    $result[$i]['left'] = $this->convertSum($left[$card['ID']]);
                }
                else{
                    $result[$i]['left'] = '0.00';
                }
                // результат по карте
                $result[$i]['income'] = $income[$card['ID']];
                // добавляем сумму гривен по обмену к карте «Наличные UAH»
                if(!empty($card['Nal']) && ($card['Currency'] == 'UAH')){
                    $result[$i]['income']['exchange_in'] = $exchange_uah_sum;
                    $income[$card['ID']]['exchange_in'] = $exchange_uah_sum;
                }
                // добавляем сумму, которую обменяли – к расходам карты
                if($exchange[$card['ID']]['summ_out'] != '0.00'){
                    $outcome[$card['ID']]['exchange_out'] = $exchange[$card['ID']]['summ_out'];
                }
                // считаем суммы по масивам Приход и Расход
                $income_sum = $this->convertSum(array_sum($income[$card['ID']]));
                $outcome_sum = $this->convertSum(array_sum($outcome[$card['ID']]));
                $result[$i]['income']['total'] = $income_sum;
                $result[$i]['outcome'] = $outcome[$card['ID']];
                $result[$i]['outcome']['total'] = $outcome_sum;
                $result[$i]['exchange'] = $exchange[$card['ID']];
                $result[$i]['total'] = $this->convertSum(($income_sum - $outcome_sum));
                $i++;
            }
        }
        return $result;
    }

    /**
     * данные по Приходу или Расходу за период
     * @param $from
     * @param $to
     * @param $type
     * @return array
     */
    public function getInOutData($from, $to, $type)
    {
        $all = $this->getFinanceModel()->getInOrOut($from, $to, $type);
        if(empty($all))
            return $this->getEmpty($type);

        $grouped_by_card = get_grouped_array($all, 'card_id');
        $result = array();
        $types = ($type == 'income')
            ? array_keys($this->types_in)
            : array_keys($this->types_out);
        $cards = $this->getCards();
        if(!empty($cards)){
            foreach($cards as $card){
                if(!empty($grouped_by_card[$card['ID']])){
                    $grouped_by_types = toolIndexArrayBy($grouped_by_card[$card['ID']], 'type');
                }
                else{
                    $grouped_by_types = array();
                }
                foreach ($types as $t){
                    $result[$card['ID']][$t] = (!empty($grouped_by_types[$t]))
                        ? $grouped_by_types[$t]['summ']
                        : '0.00';
                }
            }
        }
        return $result;
    }

    /**
     * данные по обменам за период
     * @param $from
     * @param $to
     * @return array
     */
    public function getExData($from, $to)
    {
        $all = $this->getFinanceModel()->getEx($from, $to);
        if(empty($all))
            return $this->getEmptyEx();

        $cards = $this->getCards();
        $grouped_by_card = toolIndexArrayBy($all, 'card_id');
        $result = array();
        if(!empty($cards)){
            foreach ($cards as $card) {
                $result[$card['ID']] = (!empty($grouped_by_card[$card['ID']]))
                    ? $grouped_by_card[$card['ID']]
                    : array(
                        'card_id' => $card['ID'],
                        'summ_out' => '0.00',
                        'summ_uah' => '0.00',
                    );
            }
        }
        return $result;
    }

    /**
     * заполняем Приход или Расход рыбой – если нет записей за период
     * @param $type
     * @return array
     */
    public function getEmpty($type)
    {
        $cards = $this->getCards();
        $types = ($type == 'income')
            ? array_keys($this->types_in)
            : array_keys($this->types_out);
        $result = array();
        if(!empty($cards)){
            foreach($cards as $card){
                foreach ($types as $t){
                    $result[$card['ID']][$t] = '0.00';
                }
            }
        }
        return $result;
    }

    /**
     * заполняем рыбой Обмен
     * @return array
     */
    public function getEmptyEx()
    {
        $cards = $this->getCards();
        $result = array();
        if(!empty($cards)){
            foreach($cards as $card){
                $result[$card['ID']] = array(
                    'summ_out' => '0.00',
                    'summ_uah' => '0.00',
                );
            }
        }
        return $result;
    }

    /**
     * получаем список карт
     * @return array|mixed
     */
    public function getCards()
    {
        return (empty($this->cards))
            ? $this->getCardModel()->getCardsList()
            : $this->cards;
    }

    /**
     * считаем сумму UAH по всем обменам за период
     * @param $ex
     * @return string
     */
    public function calcExUahSum($ex)
    {
        $return = 0;
        if(!empty($ex)){
            foreach ($ex as $e) {
                $return += $e['summ_uah'];
            }
        }
        return $this->convertSum($return);
    }

    public function operation()
    {
        $html = '';
        $type = $this->input->post('type', true);
        $id = $this->input->post('id', true);
        $from = $this->input->post('from', true);
        $to = $this->input->post('to', true);
        $headers = array(
            'income' => 'Приход',
            'outcome' => 'Расход',
            'exchange' => 'Обмен',
        );

        if(!empty($type) && !empty($id)
            && !empty($from) && !empty($to)
            && in_array($type, array('income', 'outcome', 'exchange'))
        ){
            $id = (in_array($id, array('exchange_in', 'exchange_out'))) ? '' : $id; // обмен
            $data['header'] = $headers[$type];
            if($type != 'exchange'){
                $data['header'] .= ' / ' . (($type == 'income') ? $this->types_in[$id] : $this->types_out[$id]);
            }
            $data['header'] .= ($from == $to)
                ? ' за ' . date('d-m-Y', strtotime($from))
                : ' за период с ' . date('d-m-Y', strtotime($from)) . ' до ' . date('d-m-Y', strtotime($to));
            $data['records'] = $this->getFinanceModel()->getOperation($type, $id, $this->convertDate($from), $this->convertDate($to));
            $html = $this->load->view('form/finance/' . $type, $data, true);
        }
        echo $html;
    }

    /**
     * удаление операции
     * @return int
     */
    public function remove()
    {
        $type = $this->input->post('type', true);
        $id = $this->input->post('id', true);
        if(!in_array($type, array('income', 'outcome', 'exchange')))
            echo 0;

        $res = $this->getFinanceModel()->removeOperation($id, $type);
        echo (!empty($res)) ? $id : 0;
    }

    public function getLeft($from)
    {
//        $from = '2017-10-21'; // test
        $result = array();
        // наличные карты
        $nals_cards = $this->getCardModel()->getNalCards();
        // все карты
        $nals_cards = $this->getCards();
        $nals_cards = toolIndexArrayBy($nals_cards, 'ID');
        $nals_cards_ids = (!empty($nals_cards)) ? array_keys($nals_cards) : array();
        // суммы прихода, расхода и обмена по ним за день
        $left = $this->getFinanceModel()->getLeft(date('Y-m-d', strtotime('-1 day', strtotime($from))), $nals_cards_ids);
        if(!empty($nals_cards) && !empty($left)){
            foreach ($nals_cards as $nk => $nals_card) {
                $in = (!empty($left['income'][$nk]['summ'])) ? $left['income'][$nk]['summ'] : '0.00';
                $out = (!empty($left['outcome'][$nk]['summ'])) ? $left['outcome'][$nk]['summ'] : '0.00';
                // добавляем к расходам по карте обмен наличных с карты
                if(!empty($left['exchange_out'][$nk]['summ'])){
                    $out += $left['exchange_out'][$nk]['summ'];
                }
                $total = $in - $out;
                // к UAH наличной карте добавляем весь обмен, в грн
                if((($nals_card['Currency'] == 'UAH') && ($nals_card['Nal'] == 1)) && !empty($left['exchange'])){
                    $total += $left['exchange'];
                }
                $result[$nk] = $total;
            }
        }
//        var_dump(date('Y-m-d', strtotime('-1 day', strtotime($from))), $left, $result); // test
        return $result;
    }
}