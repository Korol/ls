<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('current_url_build')) {
    /**
     * Построить URL относительно current_url() с удалением индексной страницы из результата
     *
     * @access	public
     * @param   $uri string
     * @return	string
     */
    function current_url_build($uri = '') {
        // 1. Обрезание всех символов '/' в начале и окончание $uri
        if (!empty($uri)) {
            $uri = '/' . trim($uri, '/');
        }

        // 2. Получаем индексную страницу
        $CI =& get_instance();
        $index_page = '/' . trim($CI->config->item('index_page'), '/');

        // 3. Очищаем current_url() от значения $index_page
        return rtrim((str_replace($index_page, '', current_url()) . $uri), '/');
    }
}

if ( ! function_exists('current_url_exclude')) {
    /**
     * Удаление из текущего URL значения $uri
     *
     * @access	public
     * @param   $uri string
     * @return	string
     */
    function current_url_exclude($uri) {
        $currentURL = current_url_build();
        $pos = strripos($currentURL, '/' . trim($uri, '/'));
        return substr($currentURL, 0, $pos);
    }
}

if ( ! function_exists('current_url_segment_exclude')) {
    /**
     * Удаление из текущего URL сегментов
     *
     * @access	public
     * @param   $countSegmentExclude int количество сегментов для удаления
     * @return	string
     */
    function current_url_segment_exclude($countSegmentExclude) {
        $currentURL = current_url_build();
        for ($i=0; $i<$countSegmentExclude; $i++) {
            $currentURL = substr($currentURL, 0, strripos($currentURL, '/'));
        }
        return $currentURL;
    }
}

if ( ! function_exists('uriStartsWith')) {
    /**
     * Проверка наличия контроллера
     *
     * @param $haystack
     * @param $needle
     *
     * @return boolean
     */
    function uriStartsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}