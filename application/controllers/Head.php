<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Head extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || $this->session->userdata('role') != 'head') {
            redirect('auth/login');
        }
        $this->load->model('Logbook_model');
        $this->load->model('User_model');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $data = ['title' => 'Head Dashboard'];
        $this->load->view('head/dashboard', ['data' => $data]);
    }

    public function validation() {
        $unit_id = $this->session->userdata('unit_id');

        $pending_logbooks = $this->Logbook_model->getPendingLogbooksByUnit($unit_id);
        $history_logbooks = $this->Logbook_model->getHistoryLogbooksByUnit($unit_id);

        $data = [
            'title' => 'Validasi Logbook',
            'pending_logbooks' => $pending_logbooks,
            'history_logbooks' => $history_logbooks
        ];

        $this->load->view('head/validation_list', ['data' => $data]);
    }

    public function detail($id) {
        $message = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $status = $this->input->post('status');
            $notes = $this->input->post('notes');
            
            if ($this->Logbook_model->updateStatus($id, $status)) {
                // Add validation record
                $validationData = [
                    'logbook_id' => $id,
                    'validator_id' => $this->session->userdata('user_id'),
                    'status' => $status,
                    'notes' => $notes
                ];
                $this->Logbook_model->addValidation($validationData);

                $this->session->set_flashdata('message', 'Logbook berhasil divalidasi: ' . ucfirst($status));
                redirect('head/validation');
            }
        }

        $logbook = $this->Logbook_model->getLogbookById($id);
        $activities = $this->Logbook_model->getActivitiesByLogbookId($id);
        $validation = $this->Logbook_model->getValidationByLogbookId($id);

        $data = [
            'title' => 'Detail Validasi',
            'logbook' => $logbook,
            'activities' => $activities,
            'validation' => $validation,
            'message' => $message
        ];

        $this->load->view('head/validation_detail', ['data' => $data]);
    }
}
