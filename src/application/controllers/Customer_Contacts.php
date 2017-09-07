<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_Contacts extends MY_Controller
{
//    public function index()
//    {
//        $this->viewHeader();
//        $this->view('form/customers/contacts', array('isEdit' => true, 'CustomerID' => 92));
//        $this->viewFooter();
//    }

    public function data()
    {
        $CustomerID = $this->input->post('CustomerID', true);
        $SiteIDs = $this->input->post('SiteIDs', true);
        $contacts = array();

        if(!empty($CustomerID)){
            // для первичного вывода и неактивного фильтра по сайтам
            // выводим для всех, кроме Директора и Секретаря, только по тем сайтам, с которыми они связаны
            if(empty($SiteIDs) && empty(($this->isDirector() || $this->isSecretary()))){
                $employee_sites = $this->getEmployeeModel()->siteGetList($this->getUserID()); // сайты, с которыми связан сотрудник
                if(!empty($employee_sites)){
                    $st = array();
                    foreach ($employee_sites as $employee_site) {
                        $st[] = $employee_site['SiteID'];
                    }
                    $SiteIDs = implode(',', $st); // строка с ID сайтов, с которыми связан сотрудник, like: 1,2,3,4,5
                }
                else{
                    $SiteIDs = '654321'; // рыба, чтоб не срабатывала empty()
                }
            }

            // учитываем фильтрацию по сайтам
            $sites = (!empty($SiteIDs)) ? explode(',', $SiteIDs) : '';
            $contacts = $this->getCustomerModel()->getCustomerContactsList($CustomerID, $sites);
        }

        $this->json_response(array('status' => 1, 'records' => $contacts, 'CustomerID' => $CustomerID));
    }

    public function save()
    {
        $status = 0;
        $post = $this->input->post(null, true);
        if(!empty($post['CustomerID']) && !empty($post['type']) && !empty($post['Name'])){
            if($post['type'] == 'add'){
                // добавляем новый контакт
                $add = array(
                    'CustomerID' => $post['CustomerID'],
                    'Name' => $post['Name'],
                    'Date' => (!empty($post['Date']) ? date('Y-m-d', strtotime($post['Date'])) : date('Y-m-d')),
                    'SiteID' => $post['SiteID'],
                    'IDonSite' => $post['IDonSite'],
                    'Comment' => $post['Comment'],
                    'Added' => date('Y-m-d H:i:s'),
                );
                $status = (int)$this->getCustomerModel()->addCustomerContact($add);
            }
            elseif(($post['type'] == 'edit') && !empty($post['ContactID'])){
                // редактируем существующий контакт
                $contact = $this->getCustomerModel()->getCustomerContact($post['ContactID']);
                $edit = array(
                    'Name' => (!empty($post['Name']) ? $post['Name'] : $contact['Name']),
                    'Date' => (!empty($post['Date']) ? date('Y-m-d', strtotime($post['Date'])) : date('Y-m-d')),
                    'SiteID' => $post['SiteID'],
                    'IDonSite' => $post['IDonSite'],
                    'Comment' => $post['Comment'],
                );
                $status = (int)$this->getCustomerModel()->updateCustomerContact($post['ContactID'], $edit);
            }
            $this->getCustomerModel()->customerUpdateNote($post['CustomerID'], $this->getUserID(), array('ReservationContacts'));
        }
        $this->json_response(array('status' => $status));
    }

    public function remove()
    {
        $status = 0;
        $ContactID = $this->input->post('ContactID', true);
        if(!empty($ContactID)){
            $this->getCustomerModel()->removeContact($ContactID);
            $status = 1;
        }
        $this->json_response(array('status' => $status));
    }

    public function get()
    {
        $status = 0;
        $ContactID = $this->input->post('ContactID', true);
        if(!empty($ContactID)){
            $contact = $this->getCustomerModel()->getCustomerContact($ContactID);
            if(!empty($contact)){
                $status = 1;
                $contact['Date'] = date('d.m.Y', strtotime($contact['Date']));
            }
        }
        $this->json_response(array('status' => $status, 'contact' => ((!empty($contact)) ? $contact : array())));
    }
}