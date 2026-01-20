<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kabid extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || $this->session->userdata('role') != 'kabid') {
            redirect('auth/login');
        }
        $this->load->model('Logbook_model');
        $this->load->model('User_model');
        $this->load->model('Unit_model');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $user_id = $this->session->userdata('user_id');
        $unit_id = $this->session->userdata('unit_id');

        // Get units assigned to Kabid
        $assigned_units = $this->Unit_model->getUnitsByKabid($user_id);
        if (empty($assigned_units)) {
            $target_units = $unit_id;
        } else {
            $target_units = $assigned_units;
        }

        $total_units = is_array($target_units) ? count($target_units) : 1;
        $pending_count = $this->Logbook_model->countPendingLogbooksByUnit($target_units, 'head');
        $history_count = $this->Logbook_model->countHistoryLogbooksByUnit($target_units, 'head');

        $data = [
            'title' => 'Kabid Dashboard',
            'total_units' => $total_units,
            'pending_count' => $pending_count,
            'history_count' => $history_count
        ];
        $this->load->view('kabid/dashboard', ['data' => $data]);
    }

    public function report() {
        $user_id = $this->session->userdata('user_id');
        $unit_id = $this->session->userdata('unit_id');

        // Get units assigned to Kabid
        $assigned_units = $this->Unit_model->getUnitsByKabid($user_id);
        if (empty($assigned_units)) {
            $target_units = [$unit_id]; // Force array for consistency
        } else {
            $target_units = $assigned_units;
        }

        // Fetch unit details for dropdown
        $units = [];
        foreach ($target_units as $uid) {
            $units[] = $this->Unit_model->getUnitById($uid);
        }

        $logbooks = [];
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');
        $selected_unit = $this->input->get('unit_id');
        $selected_role = $this->input->get('role');

        if ($this->input->get('filter')) {
            // If specific unit selected, check if it's in assigned list
            if ($selected_unit && in_array($selected_unit, $target_units)) {
                $filter_unit = $selected_unit;
            } else {
                $filter_unit = $target_units;
            }
            
            // Use new method to get detailed report
            $logbooks = $this->Logbook_model->getLogbookReportDetails($start_date, $end_date, $filter_unit, $selected_role);
        }

        $data = [
            'title' => 'Laporan Logbook',
            'units' => $units,
            'logbooks' => $logbooks,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'selected_unit' => $selected_unit,
            'selected_role' => $selected_role
        ];

        $this->load->view('kabid/report', ['data' => $data]);
    }

    public function validation() {
        $user_id = $this->session->userdata('user_id');
        $unit_id = $this->session->userdata('unit_id');

        // Get units assigned to Kabid
        $assigned_units = $this->Unit_model->getUnitsByKabid($user_id);

        // If no specific units assigned, fallback to own unit (backward compatibility)
        if (empty($assigned_units)) {
            $target_units = $unit_id;
        } else {
            $target_units = $assigned_units;
        }

        // Kabid validates Head
        $pending_logbooks = $this->Logbook_model->getPendingLogbooksByUnit($target_units, 'head');
        $history_logbooks = $this->Logbook_model->getHistoryLogbooksByUnit($target_units, 'head');

        $data = [
            'title' => 'Validasi Logbook Head',
            'pending_logbooks' => $pending_logbooks,
            'history_logbooks' => $history_logbooks
        ];

        $this->load->view('kabid/validation_list', ['data' => $data]);
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
                redirect('kabid/validation');
            }
        }

        $logbook = $this->Logbook_model->getLogbookById($id);
        $activities = $this->Logbook_model->getActivitiesByLogbookId($id);
        $validation = $this->Logbook_model->getValidationByLogbookId($id);

        $data = [
            'title' => 'Detail Validasi Head',
            'logbook' => $logbook,
            'activities' => $activities,
            'validation' => $validation,
            'message' => $message
        ];

        $this->load->view('kabid/validation_detail', ['data' => $data]);
    }
}
