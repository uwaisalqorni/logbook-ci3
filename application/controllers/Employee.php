<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Employee extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || $this->session->userdata('role') != 'employee') {
            redirect('auth/login');
        }
        $this->load->model('Logbook_model');
        $this->load->model('ActivityType_model');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $user_id = $this->session->userdata('user_id');
        $total_logbooks = $this->Logbook_model->countLogbooksByUser($user_id);
        
        $data = [
            'title' => 'Employee Dashboard',
            'total_logbooks' => $total_logbooks
        ];
        $this->load->view('employee/dashboard', ['data' => $data]);
    }

    public function logbook() {
        $user_id = $this->session->userdata('user_id');
        $date = $this->input->get('date') ?? date('Y-m-d');
        $message = '';

        // Handle Form Submission
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $date = $this->input->post('date');

            // Date Validation
            if ($date > date('Y-m-d')) {
                $message = 'Tidak dapat mengisi logbook untuk tanggal masa depan.';
            } else {

                // Find or Create Logbook
                $logbook = $this->Logbook_model->getLogbookByUserAndDate($user_id, $date);
                if (!$logbook) {
                    $logbook_id = $this->Logbook_model->createLogbook($user_id, $date);
                } else {
                    $logbook_id = $logbook['id'];
                }

                // Check if logbook is finalized
                if ($logbook && ($logbook['status'] == 'approved' || $logbook['status'] == 'rejected')) {
                    $message = 'Logbook sudah difinalisasi dan tidak dapat diubah.';
                } else {
                    $action = $this->input->post('action');
                    
                    if ($action == 'add_activity') {
                        // Validation
                        if ($this->input->post('start_time') >= $this->input->post('end_time')) {
                            $message = 'Waktu mulai harus lebih awal dari waktu selesai.';
                        } elseif (strlen($this->input->post('description')) < 10) {
                            $message = 'Deskripsi kegiatan minimal 10 karakter.';
                        // } elseif (strlen($this->input->post('output')) < 1) {
                        //     $message = 'Output kegiatan minimal 1 karakter.';
                        } else {
                            $data = [
                                'logbook_id' => $logbook_id,
                                'activity_type_id' => $this->input->post('activity_type_id'),
                                'description' => $this->input->post('description'),
                                'start_time' => $this->input->post('start_time'),
                                'end_time' => $this->input->post('end_time'),
                                'output' => $this->input->post('output'),
                                'kendala' => $this->input->post('kendala')
                            ];
                            if ($this->Logbook_model->addActivity($data)) {
                                $message = 'Kegiatan berhasil ditambahkan';
                            }
                        }
                    } elseif ($action == 'update_activity') {
                        // Validation
                        if ($this->input->post('start_time') >= $this->input->post('end_time')) {
                            $message = 'Waktu mulai harus lebih awal dari waktu selesai.';
                        } elseif (strlen($this->input->post('description')) < 10) {
                            $message = 'Deskripsi kegiatan minimal 10 karakter.';
                        // } elseif (strlen($this->input->post('output')) < 1) {
                        //     $message = 'Output kegiatan minimal 1 karakter.';
                        } else {
                            $data = [
                                'id' => $this->input->post('detail_id'),
                                'activity_type_id' => $this->input->post('activity_type_id'),
                                'description' => $this->input->post('description'),
                                'start_time' => $this->input->post('start_time'),
                                'end_time' => $this->input->post('end_time'),
                                'output' => $this->input->post('output'),
                                'kendala' => $this->input->post('kendala')
                            ];
                            if ($this->Logbook_model->updateActivity($data)) {
                                $this->session->set_flashdata('message', 'Kegiatan berhasil diperbarui');
                                redirect('employee/logbook?date=' . $date);
                            }
                        }
                    } elseif ($action == 'delete_activity') {
                        if ($this->Logbook_model->deleteActivity($this->input->post('detail_id'))) {
                            $message = 'Kegiatan dihapus';
                        }
                    } elseif ($action == 'submit_logbook') {
                        if ($this->Logbook_model->updateStatus($logbook_id, 'submitted')) {
                            $message = 'Logbook berhasil dikirim';
                        }
                    }
                } 
            } 
        }

        // Fetch Data for View
        $logbook = $this->Logbook_model->getLogbookByUserAndDate($user_id, $date);
        $activities = [];
        if ($logbook) {
            $activities = $this->Logbook_model->getActivitiesByLogbookId($logbook['id']);
        }
        $activity_types = $this->ActivityType_model->getAllActivityTypes();

        // Check for Edit Mode
        $edit_data = [];
        if ($this->input->get('edit_id')) {
            $edit_data = $this->Logbook_model->getActivityById($this->input->get('edit_id'));
        }

        // Get previous validation notes if any
        $validation = $this->Logbook_model->getValidationByLogbookId($logbook['id'] ?? 0);
        
        if ($this->session->flashdata('message')) {
            $message = $this->session->flashdata('message');
        }

        $data = [
            'title' => 'Input Logbook',
            'date' => $date,
            'logbook' => $logbook,
            'activities' => $activities,
            'activity_types' => $activity_types,
            'message' => $message,
            'edit_data' => $edit_data,
            'validation' => $validation
        ];

        $this->load->view('employee/logbook_form', ['data' => $data]);
    }

    public function history() {
        $user_id = $this->session->userdata('user_id');
        
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        
        // Pagination
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $logbooks = $this->Logbook_model->getLogbooksByUserIdAndDateRange($user_id, $start_date, $end_date, $limit, $offset);
        $total_logbooks = $this->Logbook_model->countLogbooksByUserIdAndDateRange($user_id, $start_date, $end_date);
        $total_pages = ceil($total_logbooks / $limit);

        $data = [
            'title' => 'Riwayat Logbook',
            'logbooks' => $logbooks,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'page' => $page,
            'total_pages' => $total_pages
        ];

        $this->load->view('employee/logbook_list', ['data' => $data]);
    }

    public function report() {
        $user_id = $this->session->userdata('user_id');
        
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        
        $logbooks = $this->Logbook_model->getLogbooksWithActivities($user_id, $start_date, $end_date);

        $data = [
            'title' => 'Laporan Logbook',
            'logbooks' => $logbooks,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        $this->load->view('employee/report', ['data' => $data]);
    }

    public function export() {
        $user_id = $this->session->userdata('user_id');
        
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        
        $logbooks = $this->Logbook_model->getLogbooksWithActivities($user_id, $start_date, $end_date);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'Laporan Logbook Pegawai');
        $sheet->setCellValue('A2', 'Nama: ' . $this->session->userdata('user_name'));
        $sheet->setCellValue('A3', 'Periode: ' . $start_date . ' s/d ' . $end_date);
        
        $headers = ['No', 'Tanggal', 'Waktu', 'Kegiatan', 'Output', 'Kendala', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }

        $row = 6;
        $no = 1;
        foreach ($logbooks as $logbook) {
            if (empty($logbook['activities'])) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $logbook['date']);
                $sheet->setCellValue('G' . $row, $logbook['status']);
                $row++;
            } else {
                foreach ($logbook['activities'] as $activity) {
                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $logbook['date']);
                    $sheet->setCellValue('C' . $row, date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])));
                    $sheet->setCellValue('D' . $row, $activity['description']);
                    $sheet->setCellValue('E' . $row, $activity['output']);
                    $sheet->setCellValue('F' . $row, $activity['kendala']);
                    $sheet->setCellValue('G' . $row, $logbook['status']);
                    $row++;
                }
            }
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Logbook_' . $this->session->userdata('user_name') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
