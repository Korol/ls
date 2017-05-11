<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Video_Site extends MY_Controller {

    public function data($CustomerID) {
        try {
            if (!isset($CustomerID))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->videoSiteGetList($CustomerID);

            $this->json_response(array("status" => 1, 'records' => $records));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function save($CustomerID) {
        try {
            $data = $this->input->post('data');
            $sites = $data['sites'];
            $insert = false;

            if (!isset($CustomerID, $sites))
                throw new RuntimeException("Не указан обязательный параметр");

            $records = $this->getCustomerModel()->videoSiteGetList($CustomerID);

            foreach($sites as $idSite) {
                $key = array_search($idSite, array_column($records, 'SiteID'));
                if (false === $key) {
                    if (empty($insert)) {
                        $insert = array();
                    }
                    $insert[] = $this->getCustomerModel()->videoSiteInsert($CustomerID, $idSite);
                }
            }

            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Video']);

            $this->json_response(array("status" => 1, "sites" => $sites, "insert" => $insert));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

    public function remove($CustomerID, $idRecord) {
        try {
            if (!isset($CustomerID, $idRecord))
                throw new RuntimeException("Не указан обязательный параметр");

            $this->getCustomerModel()->videoSiteDelete($idRecord);
            $this->getCustomerModel()->customerUpdateNote($CustomerID, $this->getUserID(), ['Video']);

            $this->json_response(array("status" => 1));
        } catch (Exception $e) {
            $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
        }
    }

}