<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services_Delivery extends MY_Controller {

    public function data() {
        try {
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $employee = $this->input->post('employee');

            $isAdmin = IS_LOVE_STORY
                ? ($this->isDirector() || $this->isSecretary())
                : ($this->isDirector() || $this->isSecretary());

            $records = $this->getServiceModel()->deliveryGetList($isAdmin ? $employee : $this->getUserID(), $start, $end, $isAdmin);

            // количество фото в Доставке
            // TODO: получать реальное количество фото
            if(!empty($records)){
                foreach ($records as $r_key => $record) {
                    $records[$r_key]['CountImg'] = (int)$this->getServiceModel()->deliveryImageGetCount($record['ID']);
                }
            }

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function add() {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $date = $this->input->post('date');
                $site = $this->input->post('site');
                $userTranslate = $this->input->post('userTranslate');
                $men = $this->input->post('men');
                $girl = $this->input->post('girl');
                $delivery = $this->input->post('delivery');
                $gratitude = $this->input->post('gratitude');

                if (empty($date))
                    throw new RuntimeException("Не указана дата");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (empty($userTranslate))
                    throw new RuntimeException("Не указан переводчик");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($delivery))
                    throw new RuntimeException("Не указана доставка");

                if (empty($gratitude))
                    throw new RuntimeException("Не указана благодарность");

                $id = $this->getServiceModel()->deliveryInsert($this->getUserID(), $date, $girl, $men, $site, $userTranslate, $delivery, $gratitude);

                $this->json_response(array("status" => 1, 'id' => $id));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'translators' => $this->getEmployeeModel()->employeeTranslatorGetList(),
            'sites' => $this->getSiteModel()->getRecords()
        );

        // 2. Загрузка шаблона
        $this->load->view('form/services/add_delivery', $data);
    }

    public function edit($id) {
        // 1. Обработка формы
        if (!empty($_POST)) {
            try {
                $date = $this->input->post('date');
                $site = $this->input->post('site');
                $userTranslate = $this->input->post('userTranslate');
                $men = $this->input->post('men');
                $girl = $this->input->post('girl');
                $delivery = $this->input->post('delivery');
                $gratitude = $this->input->post('gratitude');

                if (empty($date))
                    throw new RuntimeException("Не указана дата");

                if (empty($site))
                    throw new RuntimeException("Не указан сайт");

                if (empty($userTranslate))
                    throw new RuntimeException("Не указан переводчик");

                if (empty($men))
                    throw new RuntimeException("Не указан мужчина");

                if (empty($girl))
                    throw new RuntimeException("Не указана девушка");

                if (empty($delivery))
                    throw new RuntimeException("Не указана доставка");

                if (empty($gratitude))
                    throw new RuntimeException("Не указана благодарность");

                $this->getServiceModel()->deliveryUpdate($id, $date, $girl, $men, $site, $userTranslate, $delivery, $gratitude);

                $this->json_response(array("status" => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        $data = array(
            'translators' => $this->getEmployeeModel()->employeeTranslatorGetList(),
            'sites' => $this->getSiteModel()->getRecords(),
            'record' => $this->getServiceModel()->deliveryGet($id)
        );

        // 2. Загрузка шаблона
        $this->load->view('form/services/edit_delivery', $data);
    }

    public function done() {
        $isAdmin = IS_LOVE_STORY
            ? ($this->isDirector() || $this->isSecretary())
            : ($this->isDirector() || $this->isSecretary());

        if (!$isAdmin)
            show_error('Данный раздел не доступен для текущего пользователя', 403, 'Доступ запрещен');

        try {
            $id = $this->input->post('id');

            if (empty($id))
                throw new Exception('Нет данных для сохранения');

            $this->getServiceModel()->deliveryDone($id);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function photos($DeliveryID)
    {
        $this->load->view('form/services/upload', ['delivery' => $DeliveryID]);
    }

    public function server($DeliveryID) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $records = $this->getServiceModel()->deliveryImageGetList($DeliveryID);

                foreach ($records as $key => $value) {
                    $fileName = $value['ImageID'].'.'.$value['ext'];

                    $records[$key]['deleteType'] = 'DELETE';
                    $records[$key]['deleteUrl'] = base_url("services/delivery/cross/".$value['ID']."/remove");
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
                        if (empty($DeliveryID))
                            throw new RuntimeException("Не указан обязательный параметр");

                        $image = $this->getImage();
                        $cross = $this->getServiceModel()->deliveryImageInsert($DeliveryID, $image['id']);

                        $fileName = $image['id'].'.'.$image['ext'];

                        $records = [
                            [
                                'deleteType' => 'DELETE',
                                'deleteUrl' => base_url("services/delivery/cross/$cross/remove"),
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

    public function remove_cross($idCross) {
        try {

            if (!isset($idCross))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getServiceModel()->deliveryImageDelete($idCross);

            $this->json_response(["status" => 1]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

}
