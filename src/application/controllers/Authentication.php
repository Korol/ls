<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('employee_model');

        $this->load->helper('url');
        $this->load->library('session');
    }

    public function login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $data = array(
            "employees" => $this->employee_model->employeeGetList(),
            "role_d" => array(
                "10001" => "Директор",
                "10002" => "Секретарь",
                "10003" => "Переводчик",
                "10004" => "Сотрудник"
            )
        );

        // 2. Обработка данных формы
        if (!empty($_POST)) {
            try {
                if (empty($username))
                    throw new Exception('Не указан логин!');

                if (empty($password))
                    throw new Exception('Не указан пароль!');

                $res = $this->employee_model->userAuthorization($username, $password);

                if (!empty($res['errorMessage']))
                    throw new Exception($res['errorMessage']);

                $this->session->set_userdata(
                    array(
                        'logged_system' => TRUE,
                        'username' => $username,
                        'IS_LOVE_STORY' => ($this->input->post('site') == 1),
                        'user' => array(
                            'ID' => $res['record']['ID'],
                            'role' => $res['record']['role'],
                            'Avatar' => $res['record']['Avatar'],
                            'SName' => $res['record']['SName'],
                            'FName' => $res['record']['FName']
                        )
                    )
                );
                redirect(base_url(), 'refresh');
            } catch (Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }

        $this->load->view('form/login', $data);
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect(base_url('login'), 'refresh');
    }

}