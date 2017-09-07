<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Album extends MY_Controller {

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'] && !$this->role['isSecretary'])
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');
    }

    /**
     * данные для модального окна
     */
    public function datamodal()
    {
        $AlbumID = $this->input->post('AlbumID', true);
        $ImageID = $this->input->post('ImageID', true);
        $CustomerID = $this->input->post('CustomerID', true);
        if(empty($AlbumID) || empty($ImageID) || empty($CustomerID)){
            echo '';
            return;
        }

        // Получаем Сайты и Мужчин, с которыми связана Клиентка
        // mens
        $mens = $this->getCustomerModel()->getCustomerMens($CustomerID); // мужчины, с которыми связан Клиентка
        $mens = toolIndexArrayBy($mens, 'ID'); // вынесли ID в ключи массива
        // sites
        $all_sites = $this->getSiteModel()->getRecords(); // все сайты проекта
        $customer_sites = $this->getCustomerModel()->siteGetList($CustomerID); // сайты, с которыми связан Клиентка
        $sites = array();
        if(!empty($all_sites) && !empty($customer_sites)){
            $sites_all = toolIndexArrayBy($all_sites, 'ID'); // индексируем по ID
            foreach ($customer_sites as $c_site) {
                if(!empty($sites_all[$c_site['SiteID']])){
                    $sites[$c_site['SiteID']] = array('ID' => $c_site['SiteID'], 'Name' => $sites_all[$c_site['SiteID']]['Name']);
                }
            }
        }
        // для Переводчиков
        if($this->role['isTranslate']){
            // фильтруем сайты Клиентки, оставляя только те, с которыми связан и Переводчик, и Клиентка
            $cs_ids = get_keys_array($customer_sites, 'SiteID'); // ID сайтов Клиентки
            $us_ids = get_keys_array($this->getEmployeeModel()->siteGetList($this->getUserID()), 'SiteID'); // ID сайтов Переводчика
            $intersect_ids = array_intersect($cs_ids, $us_ids);
            foreach($sites as $i_key => $i_site){
                if(!in_array($i_key, $intersect_ids)){
                    unset($sites[$i_key]); // удаляем сайт, не связанный с Переводчиком
                }
            }
        }

        // получаем картинки из альбома, получаем связи
        $images = $this->getCustomerModel()->albumImageGetList($AlbumID);
        $img_ids = get_keys_array($images, 'ImageID'); // собираем ID картинок
        // получаем по этим ID связи с сайтами и мужчинами
        if(!empty($img_ids)){
            // с сайтами
            $images_sites = $this->getImageModel()->getImagesToSites($img_ids);
            if(!empty($sites)){
                // проходим по картинкам
                foreach($images as $ik => $iv){
                    // проходим по сайтам
                    foreach($sites as $sid => $sitem){
                        // если в массиве $images_sites для этого сайта есть связь с этой картинкой – Connect = 1
                        if(!empty($images_sites) && in_array($iv['ImageID'], $images_sites[$sid])){
                            $images[$ik]['ToSites'][] = array(
                                'SiteID' => $sid,
                                'SiteName' => $sitem['Name'],
                                'SiteConnect' => 1,
                            );
                        }
                        else {
                            // если в массиве $images_sites для этого сайта нет связи с этой картинкой – Connect = 0
                            $images[$ik]['ToSites'][] = array(
                                'SiteID' => $sid,
                                'SiteName' => $sitem['Name'],
                                'SiteConnect' => 0,
                            );
                        }
                    }
                }
            }
            // с мужчинами
            $images_mens = $this->getImageModel()->getImagesToMens($img_ids);
            if(!empty($mens)){
                // проходим по картинкам
                foreach($images as $iik => $iiv){
                    // проходим по мужчинам
                    foreach($mens as $mid => $mitem){
                        // если в массиве $images_mens для этого мужчины есть связь с этой картинкой – Connect = 1
                        if(!empty($images_mens) && in_array($iiv['ImageID'], array_keys($images_mens[$mid]))){
                            $images[$iik]['ToMens'][$mitem['SiteID']][] = array(
                                'MenID' => $mid,
                                'MenName' => $mitem['Name'],
                                'MenPhoto' => $mitem['Photo'],
                                'MenComment' => $images_mens[$mid][$iiv['ImageID']],
                                'MenConnect' => 1,
                            );
                        }
                        else {
                            // если в массиве $images_mens для этого мужчины нет связи с этой картинкой – Connect = 0
                            $images[$iik]['ToMens'][$mitem['SiteID']][] = array(
                                'MenID' => $mid,
                                'MenName' => $mitem['Name'],
                                'MenPhoto' => $mitem['Photo'],
                                'MenComment' => '',
                                'MenConnect' => 0,
                            );
                        }
                    }
                }
            }
        }
        $modal = $this->load->view(
            'form/customers/album/album_modal',
            array(
                'AlbumID' => $AlbumID,
                'ImageID' => $ImageID,
                'images' => $images,
                'sites' => $sites,
                'CustomerID' => $CustomerID,
            ),
            true
        );
        echo $modal;
    }

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->albumGetList($CustomerID); // альбомы Клиентки

            // получаем картинки
            foreach ($records as $key => $record) {
                $records[$key]['images'] = $this->getCustomerModel()->albumImageGetList($record['ID']);
            }

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function add($CustomerID) {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $name = $this->input->post('name');

                if (!isset($CustomerID) || empty($name))
                    throw new RuntimeException("Не указан обязательный параметр");

                $id = $this->getCustomerModel()->albumInsert($CustomerID, $name);
                $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Album'], 'Создан фотоальбом "'.$name.'"');

                $this->json_response(array("status" => 1, 'id' => $id));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        // 2. Загрузка шаблона
        $this->load->view('form/customers/album/add');
    }

    public function remove($idCustomer, $idAlbum) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка формы
        try {

            if (!isset($idCustomer, $idAlbum))
                throw new RuntimeException("Не указан обязательный параметр");

            $album = $this->getCustomerModel()->albumGet($idAlbum);

            $this->getCustomerModel()->albumDelete($idAlbum);
            $this->getCustomerModel()->customerUpdateNote($idCustomer, $this->getUserID(), ['Album'], 'Удален фотоальбом "'.$album['Name'].'"');

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function remove_cross($idCustomer, $idCross) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка формы
        try {

            if (!isset($idCustomer, $idCross))
                throw new RuntimeException("Не указан обязательный параметр");

            $album = $this->getCustomerModel()->albumGetByPhotoId($idCross);

            $this->getCustomerModel()->albumImageDelete($idCross);
            $this->getCustomerModel()->customerUpdateNote($idCustomer, $this->getUserID(), ['Album'], 'Удалено фото из фотоальбома "'.$album['Name'].'"');

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function upload($CustomerID, $idAlbum) {
        $this->load->view('form/customers/album/upload', ['customer' => $CustomerID, 'album' => $idAlbum]);
    }

    public function server($CustomerID, $idAlbum) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $records = $this->getCustomerModel()->albumImageGetList($idAlbum);

                foreach ($records as $key => $value) {
                    $fileName = $value['ImageID'].'.'.$value['ext'];

                    $records[$key]['deleteType'] = 'DELETE';
                    $records[$key]['deleteUrl'] = base_url("customer/$CustomerID/album/cross/".$value['ID']."/remove");
                    $records[$key]['name'] = $fileName;
                    $records[$key]['size'] = '';
                    $records[$key]['thumbnailUrl'] = base_url('thumb?src=/files/images/'.$fileName.'&w=80');
                    $records[$key]['url'] = base_url('thumb?src=/files/images/'.$fileName);
                }

                $this->json_response(array("status" => 1, 'files' => $records));
                break;
            case 'POST':
                if (!empty($_FILES)) {
                    try {
                        if (empty($CustomerID) || empty($idAlbum))
                            throw new RuntimeException("Не указан обязательный параметр");

                        $album = $this->getCustomerModel()->albumGet($idAlbum);

                        $image = $this->getImage();
                        $cross = $this->getCustomerModel()->albumImageInsert($idAlbum, $image['id']);
                        $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Album'], 'Добавлено фото в фотоальбом "'.$album['Name'].'"');

                        $fileName = $image['id'].'.'.$image['ext'];

                        $records = [
                            [
                                'deleteType' => 'DELETE',
                                'deleteUrl' => base_url("customer/$CustomerID/album/cross/$cross/remove"),
                                'name' => $fileName,
                                'size' => $image['size'],
                                'thumbnailUrl' => base_url('thumb?src=/files/images/'.$fileName.'&w=80'),
                                'type' => $image['type'],
                                'url' => base_url('thumb?src=/files/images/'.$fileName),
                            ]
                        ];

                        $this->json_response(array("status" => 1, 'files' => $records));
                    } catch (Exception $e) {
                        $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
                    }
                }
                break;
        }
    }

    private function getImage() {
        if (!empty($_FILES)) {
            $file = $_FILES['files'];

            if (!isset($file['error'][0]) || is_array($file['error'][0]))
                throw new RuntimeException('Ошибка загрузки файла на сервер');

            switch ($file['error'][0]) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    return null;
//                    throw new RuntimeException('Файл не загружен на сервер');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Превышен размер файла');
                default:
                    throw new RuntimeException('Неизвестная ошибка');
            }

            $ext = $this->assertFileType($file['tmp_name'][0]);

            $types = $this->getFileTypes();

            return [
                'id' => $this->getImageModel()->imageInsert($this->getFileContent($file['tmp_name'][0]), $ext),
                'ext' => $ext,
                'type' => $types[$ext],
                'size' => $file['size'][0]
            ];
        }

        return null;
    }

    protected function getFileTypes() {
        return array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    }

    /**
     * Добавляем/удаляем связь картинки с сайтом или мужчиной
     */
    public function connect()
    {
        $post = $this->input->post(null, true);
        $types = array('site', 'men');
        $res = 0;
        if(in_array($post['Type'], $types)){
            if($post['Type'] == 'site'){
                // связь картинки с сайтом
                if($post['Value'] == 0){
                    // удаляем связь
                    $res = $this->getImageModel()->removeConnect($post['SiteID'], $post['ImageID'], 'site');
                }
                else{
                    // добавляем связь
                    if(!empty($post['SiteID']) && !empty($post['ImageID'])){
                        $add =  array(
                            'SiteID' => $post['SiteID'],
                            'ImageID' => $post['ImageID'],
                        );
                        $res = $this->getImageModel()->addConnect($add, 'site');
                    }
                }
            }
            elseif($post['Type'] == 'men'){
                // связь картинки с мужчиной – связь можно только добавить, удалить нельзя (по ТЗ)
                if($post['Value'] == 1){
                    // добавляем связь
                    if(!empty($post['MenID']) && !empty($post['ImageID'])){
                        $add =  array(
                            'MenID' => $post['MenID'],
                            'ImageID' => $post['ImageID'],
                            'Comment' => (!empty($post['Comment'])) ? $post['Comment'] : '',
                        );
                        $res = $this->getImageModel()->addConnect($add, 'men');
                    }
                }
            }
        }
        echo $res;
        return;
    }

    /**
     * получаем информацию о связях картинки с сайтами и мужчинами на этих сайтах
     */
    public function getimageinfo()
    {
        $return = '';
        $ImageID = $this->input->post('ImageID', true);
        $SiteIDs = $this->input->post('SiteIDs', true); // выбор минимум одного сайта – обязателен!
        $CustomerID = $this->input->post('CustomerID', true);

        // test
//        $ImageID = 22996;
//        $SiteIDs = '25';
//        $CustomerID = 92;

        // если все нужные данные присутствуют – получаем и структурируем информацию
        if(!empty($ImageID) && !empty($CustomerID) && !empty($SiteIDs)){
            // разбиваем ID сайтов из фильтра в массив
            $filterSitesEx = (strpos($SiteIDs, ',') !== false)
                ? explode(',', $SiteIDs) // несколько сайтов в фильтре
                : array((int)$SiteIDs); // один сайт в фильтре

            // Получаем Сайты и Мужчин, с которыми связана Клиентка
            // mens
            $mens = $this->getCustomerModel()->getCustomerMens($CustomerID); // мужчины, с которыми связан Клиентка
            $mens = toolIndexArrayBy($mens, 'ID'); // вынесли ID в ключи массива
            // sites
            $all_sites = $this->getSiteModel()->getRecords(); // все сайты проекта
            $customer_sites = array(); // массив сайтов из фильтра
            foreach ($filterSitesEx as $f_site) {
                $customer_sites[] = array('SiteID' => $f_site); // сайт из фильтра
            }
            $sites = array();
            if(!empty($all_sites) && !empty($customer_sites)){
                $sites_all = toolIndexArrayBy($all_sites, 'ID'); // индексируем по ID
                foreach ($customer_sites as $c_site) {
                    if(!empty($sites_all[$c_site['SiteID']])){
                        $sites[$c_site['SiteID']] = array('ID' => $c_site['SiteID'], 'Name' => $sites_all[$c_site['SiteID']]['Name']);
                    }
                }
            }
            // для Переводчиков
            if($this->role['isTranslate']){
                // фильтруем сайты Клиентки, оставляя только те, с которыми связан и Переводчик, и Клиентка
                $cs_ids = get_keys_array($customer_sites, 'SiteID'); // ID сайтов Клиентки
                $us_ids = get_keys_array($this->getEmployeeModel()->siteGetList($this->getUserID()), 'SiteID'); // ID сайтов Переводчика
                $intersect_ids = array_intersect($cs_ids, $us_ids);
                foreach($sites as $i_key => $i_site){
                    if(!in_array($i_key, $intersect_ids)){
                        unset($sites[$i_key]); // удаляем сайт, не связанный с Переводчиком
                    }
                }
            }

            // получаем связи картинки
            $image = array(
                'ImageID' => $ImageID,
                'ToSites' => array(),
                'ToMens' => array(),
            );
            $img_ids = array($ImageID);
            // с сайтами
            $images_sites = $this->getImageModel()->getImagesToSites($img_ids);
            if(!empty($sites)){
                // проходим по сайтам
                foreach($sites as $sid => $sitem){
                    // если в массиве $images_sites для этого сайта есть связь с этой картинкой – Connect = 1
                    if(!empty($images_sites) && in_array($ImageID, $images_sites[$sid])){
                        $image['ToSites'][] = array(
                            'SiteID' => $sid,
                            'SiteName' => $sitem['Name'],
                            'SiteConnect' => 1,
                        );
                    }
                    else {
                        // если в массиве $images_sites для этого сайта нет связи с этой картинкой – Connect = 0
                        $image['ToSites'][] = array(
                            'SiteID' => $sid,
                            'SiteName' => $sitem['Name'],
                            'SiteConnect' => 0,
                        );
                    }
                }
            }
            // с мужчинами
            $images_mens = $this->getImageModel()->getImagesToMens($img_ids);
            if(!empty($mens)){
                // проходим по мужчинам
                foreach($mens as $mid => $mitem){
                    // если в массиве $images_mens для этого мужчины есть связь с этой картинкой – Connect = 1
                    if(!empty($images_mens) && in_array($ImageID, array_keys($images_mens[$mid]))){
                        $image['ToMens'][$mitem['SiteID']][] = array(
                            'MenID' => $mid,
                            'MenName' => $mitem['Name'],
                            'MenPhoto' => $mitem['Photo'],
                            'MenComment' => $images_mens[$mid][$ImageID],
                            'MenConnect' => 1,
                        );
                    }
                    else {
                        // если в массиве $images_mens для этого мужчины нет связи с этой картинкой – Connect = 0
                        $image['ToMens'][$mitem['SiteID']][] = array(
                            'MenID' => $mid,
                            'MenName' => $mitem['Name'],
                            'MenPhoto' => $mitem['Photo'],
                            'MenComment' => '',
                            'MenConnect' => 0,
                        );
                    }
                }
            }
            $return = $this->getImageFilterModalHtml($image); // получаем HTML список сайтов и мужчин для картинки
        }

        echo $return;
    }

    /**
     * строим список сайтов и мужчин для картинки
     */
    public function getImageFilterModalHtml($image)
    {
        return $this->load->view('form/customers/album/image_filter_info', array('image' => $image), true);
    }

}