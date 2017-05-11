<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends MY_Controller {

    /**
     * Функция проверки прав доступа
     */
    function assertUserRight() {
        if (!$this->role['isDirector'] && !$this->role['isSecretary'])
            show_error('Данный раздел доступен только для ролей "Директор" и "Секретарь"', 403, 'Доступ запрещен');
    }

    public function index() {
        $data = array(
            'sites' => $this->getSitesFilter()
        );

        $this->viewHeader($data);
        $this->view('form/news/index');
        $this->viewFooter([
            'isWysiwyg' => true,
            'js_array' => [
                'public/js/assol.news.js'
            ]
        ]);
    }

    public function add() {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $Title = $this->input->post('Title');
                $Text = $this->input->post('Text');
                $Site = $this->input->post('Site');
                $Customer = $this->input->post('Customer');

                if (empty($Title))
                    throw new Exception('Не указан заголовок');

                if (empty($Text))
                    throw new Exception('Не указан текст новости');

                $id = $this->getNewsModel()->newsInsert($Title, $Text, $Site, $Customer, $this->getImage(), $this->getUserID());

                $res = array('status' => 1, 'id' => $id);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        $customers = $this->getCustomerModel()->customerGetList(false, ['Status' => 0]);

        uasort($customers['records'], function($a, $b) {
            return strcmp($a['SName'].' '.$a['FName'], $b['SName'].' '.$b['FName']);
        });

        $data = array(
            'sites' => $this->getSites(),
            'customers' => $customers['records']
        );

        // 3. Загрузка шаблона
        $this->load->view('form/news/add', $data);
    }

    private function getImage() {
        if (!empty($_FILES)) {
            $file = $_FILES['thumb'];

            if (!isset($file['error']) || is_array($file['error']))
                throw new RuntimeException('Ошибка загрузки файла на сервер');

            switch ($file['error']) {
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

            $ext = $this->assertFileType($file['tmp_name']);

            return $this->getImageModel()->imageInsert($this->getFileContent($file['tmp_name']), $ext);
        }

        return null;
    }

    public function edit($id) {
        // 1. Проверка прав доступа
        $this->assertUserRight();

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $Title = $this->input->post('Title');
                $Text = $this->input->post('Text');
                $Site = $this->input->post('Site');
                $Customer = $this->input->post('Customer');

                if (empty($Title))
                    throw new Exception('Не указан заголовок');

                if (empty($Text))
                    throw new Exception('Не указан текст новости');

                $this->getNewsModel()->newsUpdate($id, $Title, $Text, $Site, $Customer, $this->getImage(), $this->getUserID());

                $res = array('status' => 1);
            } catch (Exception $e) {
                $res = array('status' => 0, 'message' => $e->getMessage());
            }

            $this->json_response($res);
        }

        $customers = $this->getCustomerModel()->customerGetList(false, ['Status' => 0]);

        uasort($customers['records'], function($a, $b) {
            return strcmp($a['SName'].' '.$a['FName'], $b['SName'].' '.$b['FName']);
        });

        $data = array(
            'record' => $news = $this->getNewsModel()->newsGet($id),
            'sites' => $this->getSites(),
            'customers' => $customers['records']
        );

        // 3. Загрузка шаблона
        $this->load->view('form/news/edit', $data);
    }

    private function getSites() {
        return array_merge(
            array(
                array(
                    'ID' => 0,
                    'Name' => 'Все новости',
                    'Domen' => 'Все новости',
                    'Note' => ''
                )
            ),
            $this->getSiteModel()->getRecords()
        );
    }

    private function getSitesFilter() {
        return array_merge(
            array(
                array(
                    'ID' => 0,
                    'Name' => 'Все новости',
                    'Domen' => 'Все новости',
                    'Note' => ''
                )
            ),
            $this->getNewsModel()->getSites(($this->isTranslate() || $this->isEmployee()) ? $this->getUserID() : false)
        );
    }

    public function data() {
        try {
            $data = $this->input->post('data');

            // Определяем необходимость фильтрации по сайтам
            $employee = ($this->isTranslate() || $this->isEmployee()) ? $this->getUserID() : false;

            $news = $this->getNewsModel()->newsGetList($employee, $data);

            $records = array();
            $out = array();

            foreach($news['records'] as $new) {
                $date = date_format(date_create($new['DateCreate']), 'd.m.Y');
                $records["$date"][] = $new;
            }

            foreach($records as $key => $value) {
                $isCurrent = date('d.m.Y') == $key;

                $out[] = array(
                    'date' => $key,
                    'isCurrentDay' => $isCurrent,
                    'news' => $value
                );
            }

            $this->json_response(array("status" => 1, 'records' => $out, 'count' => $news['count']));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($ID) {
        try {
            // 1. Проверка прав доступа
            $this->assertUserRight();

            if (!isset($ID))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getNewsModel()->newsDelete($ID);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function read() {
        try {
            $this->getNewsModel()->newsRead($this->getUserID(), ($this->isTranslate() || $this->isEmployee()));

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    protected function getFileTypes() {
        return array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    }
}
