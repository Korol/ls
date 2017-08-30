<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Site extends MY_Controller {

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->siteGetList($CustomerID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($CustomerID) {
        try {
            $data = $this->input->post('data');

            $insert = false;
            $update = false;

            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            if (isset($data['sites'])) {
                $records = $this->getCustomerModel()->siteGetList($CustomerID);

                foreach($data['sites'] as $idSite) {
                    $key = array_search($idSite, array_column($records, 'SiteID'));
                    if (false === $key) {
                        if (empty($insert)) {
                            $insert = array();
                        }
                        $insert[] = $this->getCustomerModel()->siteInsert($CustomerID, $idSite);
                    }
                }
            }

            if (isset($data['notes'])) {
                foreach($data['notes'] as $note) {
                    $this->getCustomerModel()->siteUpdate($note['id'], array('Note' => $note['note']));
                    $update = true;
                }
            }

            if ($insert || $update) {
                $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Site']);
            }

            $this->json_response(array("status" => 1, "insert" => $insert));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID, $idRecord) {
        try {
            if (!isset($CustomerID, $idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->siteDelete($idRecord);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Site']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function sites()
    {
        $return = '';
        $CustomerID = $this->input->post('CustomerID', true);
        $isEditSites = $this->input->post('isEditSites', true);
//        $CustomerID = $isEditSites = 92;
        if(!empty($CustomerID)){
            // получаем список сайтов клиентки
            $sites = $this->getCustomerModel()->siteGetList($CustomerID);

            // формируем строки таблицы в HTML
            if(!empty($sites)){
                foreach ($sites as $site) {
                    $translators = $this->getCustomerModel()->findTranslatorBySiteCustomer($site['SiteID'], $CustomerID);
                    $return .= '<tr id="str_' . $site['ID'] . '">';
                    $return .= '<td><b>' . $site['Name'] . '</b></td>';
                    $return .= '<td>';
                    if(!empty($isEditSites)){
                        $return .= '<input type="text" class="form-control input-sm note-site" record="' . $site['ID'] . '" value="' . $site['Note'] . '"/>';
                    }
                    else{
                        $return .= $site['Note'];
                    }
                    $return .= '</td>';
                    $return .= '<td>';
                    if(!empty($translators)){
                        $tr = array();
                        foreach ($translators as $translator) {
                            if(!empty($isEditSites)) {
                                $tr[] = '<a href="/employee/' . $translator['ID'] . '/profile" target="_blank">'
                                    . $translator['SName'] . ' ' . $translator['FName']
                                    . '</a>';
                            }
                            else{
                                $tr[] = $translator['SName'] . ' ' . $translator['FName'];
                            }
                        }
                        $return .= implode('<br>', $tr);
                    }
                    $return .= '</td>';
                    $return .= '<td align="center">';
                    if(!empty($isEditSites)) {
                        $return .= '<button class="btn btn-default btn-sm" onclick="if(confirm(\'Вы уверены, что хотите удалить связь клиентки с этим сайтом?\')){removeSiteConnection(' . $site['ID'] . '); recheckSite(' . $site['SiteID'] . ');}"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
                    }
                    $return .= '</td>';
                    $return .= '</tr>';
                }
            }
        }
        echo $return;
    }

}