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

    private $table_image_men =
        "CREATE TABLE `assol_image_men` (
          `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `ImageID` int(11) DEFAULT NULL,
          `MenID` int(11) DEFAULT NULL,
          `Comment` varchar(255) DEFAULT NULL,
          PRIMARY KEY (`ID`),
          KEY `ImageID` (`ImageID`),
          KEY `MenID` (`MenID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    private $table_image_site =
        "CREATE TABLE `assol_image_site` (
          `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `ImageID` int(11) DEFAULT NULL,
          `SiteID` int(11) DEFAULT NULL,
          PRIMARY KEY (`ID`),
          KEY `ImageID` (`ImageID`),
          KEY `SiteID` (`SiteID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table);
        $this->db()->query($this->table_image_men);
        $this->db()->query($this->table_image_site);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_IMAGE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_IMAGE_MEN_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_IMAGE_SITE_NAME, TRUE);
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

    /**
     * Получаем связи указанных изображений с сайтами
     * @param array $ids
     * @return array
     */
    public function getImagesToSites($ids)
    {
        $return = array();
        $res = $this->db()
            ->where_in('ImageID', $ids)
            ->get(self::TABLE_IMAGE_SITE_NAME)->result_array();
        if(!empty($res)){
            foreach($res as $row){
                $return[$row['SiteID']][] = $row['ImageID'];
            }
        }
        return $return;
    }

    /**
     * Получаем связи указанных изображений с мужчинами
     * @param array $ids
     * @return array
     */
    public function getImagesToMens($ids)
    {
        $return = array();
        $res = $this->db()
            ->where_in('ImageID', $ids)
            ->get(self::TABLE_IMAGE_MEN_NAME)->result_array();
        if(!empty($res)){
            foreach($res as $row){
                $return[$row['MenID']][$row['ImageID']] = $row['Comment'];
            }
        }
        return $return;
    }

    /**
     * Добавляем связь картинки с сайтом или мужчиной
     * @param array $data
     * @param string $type
     */
    public function addConnect($data, $type)
    {
        $types = array(
            'site' => array(
                'table' => self::TABLE_IMAGE_SITE_NAME,
                'entity' => 'SiteID',
            ),
            'men' => array(
                'table' => self::TABLE_IMAGE_MEN_NAME,
                'entity' => 'MenID',
            )
        );
        if(in_array($type, array_keys($types))){
            // сначала удаляем связь – на всякий случай
            $this->removeConnect($data[$types[$type]['entity']], $data['ImageID'], $type);
            // затем добавляем
            $this->db()->insert($types[$type]['table'], $data);
        }
        return $this->db()->affected_rows();
    }

    /**
     * Удаляем связь картинки с сайтом или мужчиной
     * @param int $entityID
     * @param int $imageID
     * @param string $type
     */
    public function removeConnect($entityID, $imageID, $type)
    {
        $types = array(
            'site' => array(
                'table' => self::TABLE_IMAGE_SITE_NAME,
                'entity' => 'SiteID',
            ),
            'men' => array(
                'table' => self::TABLE_IMAGE_MEN_NAME,
                'entity' => 'MenID',
            )
        );
        if(in_array($type, array_keys($types))){
            $this->db()->delete($types[$type]['table'], array(
                $types[$type]['entity'] => $entityID,
                'ImageID' => $imageID,
            ));
        }
        return $this->db()->affected_rows();
    }
}