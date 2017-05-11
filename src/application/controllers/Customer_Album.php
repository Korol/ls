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

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->albumGetList($CustomerID);

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

}