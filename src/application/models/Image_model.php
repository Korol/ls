<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с изображениями
 */
class Image_model extends MY_Model {

    private $table = "CREATE TABLE IF NOT EXISTS `assol_images` (
                          `ID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
                          `ext` VARCHAR(10) NOT NULL COMMENT 'Расширение файла',
                          PRIMARY KEY (`ID`)
                      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Изображения';";


    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_IMAGE_NAME, TRUE);
    }


    /**
     * Сохранение изображения в базу данных
     *
     * @param string $content Содержимое файла
     * @param string $ext Расширение файла
     *
     * @return int ID записи
     */
    public function imageInsert($content, $ext) {
        // Открываем транзакция
        $this->db()->trans_start();

        // Вставляем информацию о расширение файла
        $this->db()->insert(self::TABLE_IMAGE_NAME, ['ext' => $ext]);
        $id = $this->db()->insert_id();

        // Пытаемся сохранить в файл
        if (file_put_contents("./files/images/$id.$ext", $content) === FALSE) {
            $this->db()->trans_rollback(); // Отменяем транзакцию если ошибка
        } else {
            $this->db()->trans_complete(); // Завершаем транзакцию если успешно
        }

        return $id;
    }

    /**
     * Удалить изображение из системы
     *
     * @param int $id ID изображение в системе
     */
    public function remove($id) {
        $image = $this->db()->get_where(self::TABLE_IMAGE_NAME, ['ID' => $id])->row_array();

        if ($image) {
            $file = './files/images/'.$image['ID'].'.'.$image['ext'];
            if (file_exists($file)) unlink($file); // Удаление файла

            $this->db()->delete(self::TABLE_IMAGE_NAME, ['ID' => $id]); // Удаление записи из таблицы
        }
    }

    public function imagesList($limit = 5, $offset = 0) {
        return $this->db()
            ->from(self::TABLE_IMAGE_NAME)
            ->limit($limit, $offset)
            ->get()->result_array();
    }

}