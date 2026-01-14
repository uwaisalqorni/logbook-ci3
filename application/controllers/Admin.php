<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || $this->session->userdata('role') != 'admin') {
            redirect('auth/login');
        }
        $this->load->model('User_model');
        $this->load->model('Unit_model');
        $this->load->model('Logbook_model');
        $this->load->model('ActivityType_model');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $data = [
            'title' => 'Admin Dashboard',
            'total_users' => $this->User_model->countUsers(),
            'total_units' => $this->Unit_model->countUnits(),
            'today_logbooks' => $this->Logbook_model->countTodayLogbooks()
        ];
        $this->load->view('admin/dashboard', ['data' => $data]);
    }

    public function units() {
        $message = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $action = $this->input->post('action');
            
            if ($action == 'add') {
                $data = ['name' => trim($this->input->post('name'))];
                if ($this->Unit_model->addUnit($data)) {
                    $message = 'Unit berhasil ditambahkan';
                }
            } elseif ($action == 'edit') {
                $data = [
                    'id' => $this->input->post('id'),
                    'name' => trim($this->input->post('name'))
                ];
                if ($this->Unit_model->updateUnit($data)) {
                    $message = 'Unit berhasil diupdate';
                }
            } elseif ($action == 'delete') {
                if ($this->Unit_model->deleteUnit($this->input->post('id'))) {
                    $message = 'Unit berhasil dihapus';
                }
            }
        }

        $units = $this->Unit_model->getAllUnits();
        $data = [
            'title' => 'Master Unit',
            'units' => $units,
            'message' => $message
        ];
        $this->load->view('admin/units', ['data' => $data]);
    }

    public function activity_types() {
        $message = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $action = $this->input->post('action');
            
            if ($action == 'add') {
                $data = ['name' => trim($this->input->post('name'))];
                if ($this->ActivityType_model->addActivityType($data)) {
                    $message = 'Jenis Kegiatan berhasil ditambahkan';
                }
            } elseif ($action == 'edit') {
                $data = [
                    'id' => $this->input->post('id'),
                    'name' => trim($this->input->post('name'))
                ];
                if ($this->ActivityType_model->updateActivityType($data)) {
                    $message = 'Jenis Kegiatan berhasil diupdate';
                }
            } elseif ($action == 'delete') {
                if ($this->ActivityType_model->deleteActivityType($this->input->post('id'))) {
                    $message = 'Jenis Kegiatan berhasil dihapus';
                }
            }
        }

        $activities = $this->ActivityType_model->getAllActivityTypes();
        $data = [
            'title' => 'Master Jenis Kegiatan',
            'activities' => $activities,
            'message' => $message
        ];
        $this->load->view('admin/activity_types', ['data' => $data]);
    }

    public function users() {
        $message = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $action = $this->input->post('action');
            
            if ($action == 'add') {
                $data = [
                    'nik' => trim($this->input->post('nik')),
                    'name' => trim($this->input->post('name')),
                    'username' => trim($this->input->post('username')),
                    'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'role' => $this->input->post('role'),
                    'unit_id' => $this->input->post('unit_id') ?: NULL,
                    'position' => $this->input->post('position'),
                    'golongan' => $this->input->post('golongan')
                ];
                if ($this->User_model->addUser($data)) {
                    $message = 'Pegawai berhasil ditambahkan';
                }
            } elseif ($action == 'edit') {
                $data = [
                    'id' => $this->input->post('id'),
                    'nik' => trim($this->input->post('nik')),
                    'name' => trim($this->input->post('name')),
                    'username' => trim($this->input->post('username')),
                    'role' => $this->input->post('role'),
                    'unit_id' => $this->input->post('unit_id') ?: NULL,
                    'position' => $this->input->post('position'),
                    'golongan' => $this->input->post('golongan'),
                    'status' => $this->input->post('status'),
                    'password' => !empty($this->input->post('password')) ? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : ''
                ];
                if ($this->User_model->updateUser($data)) {
                    $message = 'Pegawai berhasil diupdate';
                }
            } elseif ($action == 'delete') {
                if ($this->User_model->deleteUser($this->input->post('id'))) {
                    $message = 'Pegawai berhasil dihapus';
                }
            }
        }

        $users = $this->User_model->getAllUsers();
        $units = $this->Unit_model->getAllUnits();
        $data = [
            'title' => 'Master Pegawai',
            'users' => $users,
            'units' => $units,
            'message' => $message
        ];
        $this->load->view('admin/users', ['data' => $data]);
    }
}
