<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Mens extends MY_Controller {

    /**
     * Сохраняем/обновляем информацию о мужчинах клиентки
     */
    public function save()
    {
        if(!empty($_POST)){
            $post = $this->input->post(null, true);
            $types = array('add', 'update');
            $cid = (!empty($post['CustomerID'])) ? $post['CustomerID'] : 0;
            if(!empty($post['CustomerID']) && in_array($post['type'], $types)){
                // загрузка фото
                if(!empty($_FILES['Photo']['tmp_name'])){
                    $photo = $this->uploadPhoto();
                }
                // новый мужчина
                if($post['type'] == 'add'){
                    $add = array(
                        'CustomerID' => $post['CustomerID'],
                        'SiteID' => (!empty($post['SiteID'])) ? $post['SiteID'] : 0,
                        'Name' => (!empty($post['Name'])) ? $post['Name'] : 'New man',
                        'Photo' => (!empty($photo)) ? $photo : '',
                        'Comment' => (!empty($post['Comment'])) ? $post['Comment'] : '',
                    );
                    // добавляем
                    $this->getCustomerModel()->saveCustomerMen($add);
                    $this->getCustomerModel()->customerUpdateNote($post['CustomerID'], $this->getUserID(), array('AddMen'));
                } // обновляем существующего мужчину
                elseif(($post['type'] == 'update') && !empty($post['ID'])){
                    $men = $this->getCustomerModel()->getCustomerMen($post['ID']);
                    if(!empty($men)){
                        // удаляем старое фото - если есть и старое и новое
                        if(!empty($photo) && !empty($men['Photo'])){
                            $this->getImageModel()->remove($men['Photo']);
                        }
                        $update = array(
                            'SiteID' => (!empty($post['SiteID'])) ? $post['SiteID'] : 0,
                            'Name' => (!empty($post['Name'])) ? $post['Name'] : $men['Name'],
                            'Photo' => (!empty($photo)) ? $photo : $men['Photo'],
                            'Comment' => (!empty($post['Comment'])) ? $post['Comment'] : $men['Comment'],
                        );
                        // обновляем
                        $this->getCustomerModel()->updateCustomerMen($post['ID'], $update);
                        $this->getCustomerModel()->customerUpdateNote($post['CustomerID'], $this->getUserID(), array('EditMen'));
                    }
                }
            }
        }
        if(!empty($cid))
            redirect(base_url('customer/' . $cid . '/profile#Mens'));
        else
            redirect(base_url('customers'));
    }

    /**
     * Типы файлов для загрузки
     * @return array
     */
    protected function getFileTypes() {
        return array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    }

    /**
     * Загрузка фото
     * @return string
     */
    public function uploadPhoto()
    {
        $file = $_FILES['Photo'];
        $ext = $this->assertFileType($file['tmp_name']);
        $file_id = $this->getImageModel()->imageInsert($this->getFileContent($file['tmp_name']), $ext);
        return (!empty($file_id)) ? $file_id . '.' . $ext : '';
    }

    /**
     * Удаление мужчины
     */
    public function remove()
    {
        $res = 0;
        $id = $this->input->post('ID', true);
        $CustomerID = $this->input->post('CustomerID', true);
        if(!empty($id)){
            $men = $this->getCustomerModel()->getCustomerMen($id);
            if(!empty($men)){
                // удаляем фото – если оно есть
                if(!empty($men['Photo'])){
                    $this->getImageModel()->remove($men['Photo']);
                }
                // удаляем запись из БД
                $res = $this->getCustomerModel()->removeCustomerMen($id);
                $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), array('RemoveMen'));
            }
        }
        echo $res;
    }

}