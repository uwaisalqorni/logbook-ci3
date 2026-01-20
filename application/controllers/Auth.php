<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index() {
        if ($this->session->userdata('user_id')) {
            $this->redirectUser($this->session->userdata('role'));
        }
        
        $data = [
            'title' => 'Login',
            'username' => '',
            'password' => '',
            'username_err' => '',
            'password_err' => '',
            'csrf_token' => ''
        ];
        $this->load->view('auth/login', ['data' => $data]);
    }

    public function login() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            
            $data = [
                'title' => 'Login',
                'username' => trim($this->input->post('username')),
                'password' => trim($this->input->post('password')),
                'username_err' => '',
                'password_err' => '',
                'csrf_token' => ''
            ];

            // Validate Username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/email
            if ($this->User_model->findUserByUsername($data['username'])) {
                // User found
            } else {
                $data['username_err'] = 'No user found';
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // Validated
                $loggedInUser = $this->User_model->login($data['username'], $data['password']);

                if ($loggedInUser) {
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->load->view('auth/login', ['data' => $data]);
                }
            } else {
                // Load view with errors
                $this->load->view('auth/login', ['data' => $data]);
            }

        } else {
            // Init data
            $data = [
                'title' => 'Login',
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
                'csrf_token' => ''
            ];

            // Load view
            $this->load->view('auth/login', ['data' => $data]);
        }
    }

    public function createUserSession($user) {
        $session_data = [
            'user_id' => $user['id'],
            'user_username' => $user['username'],
            'user_name' => $user['name'],
            'role' => $user['role'],
            'unit_id' => $user['unit_id']
        ];
        $this->session->set_userdata($session_data);
        $this->redirectUser($user['role']);
    }

    public function redirectUser($role) {
        if ($role == 'admin') {
            redirect('admin/dashboard');
        } elseif ($role == 'employee') {
            redirect('employee/dashboard');
        } elseif ($role == 'head') {
            redirect('head/dashboard');
        } elseif ($role == 'kabid') {
            redirect('kabid/dashboard');
        } elseif ($role == 'management') {
            redirect('management/dashboard');
        } else {
            redirect('auth/login');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
