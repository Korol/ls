<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с настройками приложения
 */
class Setting_model extends MY_Model {

    private $table = "CREATE TABLE IF NOT EXISTS `assol_settings` (
                          `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
                          `key` VARCHAR(128) NOT NULL COMMENT 'Ключ',
                          `value` TEXT NOT NULL COMMENT 'Значение',
                          PRIMARY KEY (`id`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Настройки';";


    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_SETTING_NAME, TRUE);
    }

    private function settingGet($key) {
        return $this->db()->get_where(self::TABLE_SETTING_NAME, ['key' => $key])->row_array();
    }

    public function get($key) {
        $record = $this->settingGet($key);

        return empty($record) ? '' : $record['value'];
    }

    public function save($key, $value) {
        $record = $this->settingGet($key);

        if (empty($record)) {
            $this->settingInsert($key, $value);
        } else {
            $this->settingUpdate($record['id'], $value);
        }
    }

    /**
     * Сохранение настройки в базу
     *
     * @param string $key Ключ
     * @param string $value Значение
     *
     * @return int ID записи
     */
    private function settingInsert($key, $value) {
        $this->db()->insert(self::TABLE_SETTING_NAME, ['key' => $key, 'value' => $value]);
        return $this->db()->insert_id();
    }

    /**
     * Обновление настройки
     *
     * @param int $id записи
     * @param string $value Значение
     */
    private function settingUpdate($id, $value) {
        $this->db()->where('ID', $id);
        $this->db()->update(self::TABLE_SETTING_NAME, ['value' => $value]);
    }

}