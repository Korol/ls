<?php
/*
 * Индексирует массив по ключу
 * $key – уникальное значение в выборке (напр. ID)
 *
 * @param array $arr - исходный массив для индексации
 * @param string $key - название поля, по которому индексируем
 * @return array
 */
function toolIndexArrayBy($arr, $key){

    $result = array();
    foreach($arr as $item){
        if(is_object($item))
            $result[$item->$key] = $item;
        else if(is_array($item))
            $result[$item[$key]] = $item;
    }
    return $result;
}

/**
 * @param array $arr - исходный массив
 * @param string $key - поле, которое будет ключем (уникальное!)
 * @param string $title - поле, которое будет значением
 * @return array
 */
function for_select($arr, $key, $title){

    $result = array();
    foreach($arr as $item){
        if(is_object($item))
            $result[$item->$key] = $item->$title;
        else if(is_array($item))
            $result[$item[$key]] = $item[$title];
    }
    return $result;
}

/*
 * Получение массива значений определенного поля - для WHERE IN
 *
 * @param array $arr - исходный массив для индексации
 * @param string $key - название поля, значение которого выбираем в список
 * @return array
 */
function get_keys_array($arr, $key){
    $result = array();
    foreach($arr as $item){
        if(is_object($item) && isset($item->$key)){
            $result[] = $item->$key;
        }
        elseif(is_array($item) && isset($item[$key])){
            $result[] = $item[$key];
        }
    }
    return $result;
}

/*
 * Группировка элементов массива по указанному полю $group_field
 *
 * @param array $arr - исходный массив для группировки
 * @param string $group_field - название поля, по которому происходит группировка
 * @param string $result_field - если $result_field указан = в группу попадают только значения этого поля
 */
function get_grouped_array($arr, $group_field, $result_field = ''){
    $result = array();
    foreach($arr as $item){
        if(is_object($item) && isset($item->$group_field)){
            if(!empty($result_field))
                $result[$item->$group_field][] = $item->$result_field;// только поле
            else
                $result[$item->$group_field][] = $item;// вся сторока
        }
        elseif(is_array($item) && isset($item[$group_field])){
            if(!empty($result_field))
                $result[$item[$group_field]][] = $item[$result_field];// только поле
            else
                $result[$item[$group_field]][] = $item;// вся строка
        }
    }
    return $result;
}

/*
 * Построение дерева
 *
 * @param array $rs - ссылка на массив категорий
 * @param integer $parent - ID родительского элемента
 * @return array
 */
function build_tree(&$rs,$parent)
{
    $out = array();
    if (!isset($rs[$parent]))
    {
        return $out;
    }
    foreach ($rs[$parent] as $row)
    {
        $chidls = build_tree($rs,$row['id']);
        if ($chidls)
            $row['childs'] = $chidls;

        $out[] = $row;
    }
    return $out;
}

/**
 * Построение древовидного массива из результатов запроса к БД
 * @param array $arr - исходный массив
 * @param string $mk - уникальный ключ
 * @param string $sk - поле, в котором указан ID родителя
 * @param string $child - название массива, в который будут помещены потомки
 * @return array
 */
function table_to_tree_array($arr, $mk = 'id', $sk = 'parent_id', $child = 'child') {
    if(!$arr) {
        return array();
    }

    $l = count($arr);
    for($i = 0; $i < $l; $i++) {
        $mas[ $arr[$i][$mk] ] = &$arr[$i];
    }

    foreach($mas as $k => $v) {
        $mas[ $v[$sk] ][$child][] = &$mas[$k];
    }

    $res = array();
    foreach($arr as $v) {
        if(isset($v[$sk]) && $v[$sk] == 0) {
            $res[] = $v;
        }
    }
    $arr = $res;
    return $arr;
}

/*
 * получаем только цифры из строки
 *
 * @param string $string - исходная строка
 * @return string строка, состоящая только из цифр исходной строки
 */
function get_numbers($string)
{
    $return = '';
    if(strlen($string) > 0){
        $num_arr = array();
        preg_match_all('#\d#', $string, $num_arr);
        if(!empty($num_arr))
            $return = implode('', $num_arr[0]);
    }
    return $return;
}

/*
 * форматируем стоимость товара
 *
 * @param integer $price - стоимость товара
 * @return string $currency - отформатированная строка стоимости товара
 */
function show_price($price, $currency = 'Р')
{
    $price = floatval($price);
    return number_format($price, 0, '.', ' ') . ((!empty($currency)) ? ' ' . $currency : '');
}

/*
 * форматируем дату выпуска товара
 *
 * @param string дата выпуска
 * @return string отформатированная дата выпуска
 */
function show_date($date, $template = 'd.m.Y')
{
    if(!empty($template))
        $date = date($template, strtotime($date));

    return $date;
}

/**
 * аналог ucfirst для multi-byte encodings
 * @param string $string
 * @return string
 */
function my_mb_ucfirst($string) {
    $string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    return $string;
}

/**
 * URL из строки, с использованием транслитерации
 * @param string $string
 * @return mixed|string
 */
function create_url_from_string($string)
{
    //TODO unknown symbol into -
    $string = translit(trim($string));
    $string = preg_replace('/\//','', $string);
    $string = preg_replace('/[\),\(]/','-', $string);
    $string = preg_replace('/ {1,}/','-', $string);
    $string = preg_replace('/&/','-and-', $string);
    //$string = preg_replace('/-\+-/','+', $string);
    //$string = preg_replace('/\+-/','+', $string);
    $string = preg_replace('/-\+-/','-plus-', $string);
    $string = preg_replace('/\+-/','-plus-', $string);
    $string = preg_replace('/[^a-z0-9\-\+]+/','-', $string);
    $string = preg_replace('/-{2,}/','-', $string);
    $string = preg_replace('/-\/-/','/', $string);
    $string = preg_replace('/^-/','', $string);
    $string = preg_replace('/-$/','', $string);
    return $string;
}

/**
 * Транслитерация (используется в функции выше)
 * @param string $string
 * @return string
 */
function translit($string)
{
    $rus = array("/а/", "/б/", "/в/", "/г/", "/ґ/", "/д/", "/е/", "/ё/", "/ж/", "/з/", "/и/", "/й/", "/к/",
        "/л/", "/м/", "/н/", "/о/", "/п/", "/р/", "/с/", "/т/", "/у/", "/ф/", "/х/", "/ц/", "/ч/",
        "/ш/", "/щ/", "/ы/", "/э/", "/ю/", "/я/", "/ь/", "/ъ/", "/і/", "/ї/", "/є/", "/А/", "/Б/",
        "/В/", "/Г/", "/ґ/", "/Д/", "/Е/", "/Ё/", "/Ж/", "/З/", "/И/", "/Й/", "/К/", "/Л/", "/М/",
        "/Н/", "/О/", "/П/", "/Р/", "/С/", "/Т/", "/У/", "/Ф/", "/Х/", "/Ц/", "/Ч/", "/Ш/", "/Щ/",
        "/Ы/", "/Э/", "/Ю/", "/Я/","/Ь/", "/Ъ/", "/І/", "/Ї/","/Є/","/\-/","/\./","/\,/",
        "/\№/", "/a/", "/b/", "/c/", "/d/","/e/", "/f/", "/g/", "/h/", "/i/","/j/", "/k/", "/l/",
        "/m/", "/n/", "/o/", "/p/", "/q/", "/r/", "/s/", "/t/", "/u/", "/v/", "/w/", "/x/", "/y/",
        "/z/", "/A/", "/B/", "/C/", "/D/", "/E/", "/F/", "/G/", "/H/", "/I/", "/J/", "/K/", "/L/",
        "/M/", "/N/", "/O/", "/P/", "/Q/", "/R/", "/S/", "/T/", "/U/", "/V/", "/W/", "/X/", "/Y/",
        "/Z/","/à/", "/è/", "/é/", "/ì/", "/í/", "/î/", "/ò/", "/ó/", "/ù/", "/ú/", "/À/", "/È/",
        "/É/", "/Ì/", "/Í/","/Î/", "/Ò/", "/Ó/", "/Ù/", "/Ú/",
        "/\(/","/\)/",'/\"/','/«/','/»/');
    $lat = array("a", "b", "v", "g", "g", "d", "e", "e", "j", "z", "i", "y", "k",
        "l", "m", "n", "o", "p", "r","s", "t", "u", "f", "h", "c", "ch",
        "sh", "shh", "y", "e", "u", "ja", "", "", "i", "i", "e","A", "B",
        "V", "G", "G", "D", "E", "E", "J", "Z", "I", "Y", "K", "L", "M",
        "N", "O", "P", "R","S", "T", "U", "F", "H", "C", "CH", "SH", "SHH",
        "Y", "E", "U", "JA","", "", "I", "I","E","-","-","-",
        "N", "a", "b", "c", "d","e", "f", "g", "h", "i","j", "k", "l",
        "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y",
        "z", "A", "B", "C", "D", "E","F", "G", "H", "I", "J", "K", "L",
        "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W","X", "Y",
        "Z", "a", "e", "e", "i", "i", "i", "o", "o", "u", "u", "A", "E", "E", "I", "I", "I", "O", "O", "U", "U",
        "-","-",'','','','');

    $string = preg_replace($rus, $lat, $string);
    return  strtolower($string);
}

/**
 * Множественная форма записи
 * @param int $n - количество
 * @param string $form1 - одна единица
 * @param string $form2 - две единицы
 * @param string $form5 - много единиц
 * @return string
 */
function pluralForm($n = 0, $form1 = 'просмотр', $form2 = 'просмотра', $form5 = 'просмотров')
{
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form5;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form5;
}

if(!function_exists('debug')){
    /**
     * Простенький формат дебага
     * @param $variable
     */
    function debug($variable)
    {
        echo '<pre>';
        print_r($variable);
        echo '</pre>';
    }
}

/**
 * Сортировка ассоциативного массива по нескольким полям – мега вещь!
 */
if (!function_exists('array_order_by')) {
    // Usage: $sorted = array_order_by($data, 'points', SORT_DESC, 'time', SORT_ASC, 'friends', SORT_DESC);
    function array_order_by()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp[$field] = array();
                foreach ($data as $key => $row)
                    $tmp[$field][$key] = $row[$field];
                $args[$n] = &$tmp[$field];
            } else {
                $args[$n] = &$args[$n];
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}
?>