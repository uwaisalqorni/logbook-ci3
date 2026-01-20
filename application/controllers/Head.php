<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class Head extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || $this->session->userdata('role') != 'head') {
            redirect('auth/login');
        }
        $this->load->model('Logbook_model');
        $this->load->model('User_model');
        $this->load->model('ActivityType_model');
        $this->load->model('Unit_model');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $unit_id = $this->session->userdata('unit_id');

        // Stats
        $total_employees = $this->User_model->countActiveEmployeesByUnit($unit_id);
        $submitted_today = $this->Logbook_model->countSubmittedLogbooksByUnitToday($unit_id);
        $not_submitted_today = $total_employees - $submitted_today;
        
        $pending_validation = $this->Logbook_model->countPendingLogbooksByUnit($unit_id, 'employee');

        $data = [
            'title' => 'Head Dashboard',
            'not_submitted_today' => $not_submitted_today,
            'pending_validation' => $pending_validation
        ];
        $this->load->view('head/dashboard', ['data' => $data]);
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
                                redirect('head/logbook?date=' . $date);
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
            'title' => 'Input Logbook Head',
            'date' => $date,
            'logbook' => $logbook,
            'activities' => $activities,
            'activity_types' => $activity_types,
            'message' => $message,
            'edit_data' => $edit_data,
            'validation' => $validation
        ];

        $this->load->view('head/logbook_form', ['data' => $data]);
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
            'title' => 'Riwayat Logbook Head',
            'logbooks' => $logbooks,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'page' => $page,
            'total_pages' => $total_pages
        ];

        $this->load->view('head/logbook_list', ['data' => $data]);
    }

    public function validation() {
        $unit_id = $this->session->userdata('unit_id');

        $pending_logbooks = $this->Logbook_model->getPendingLogbooksByUnit($unit_id, 'employee');
        $history_logbooks = $this->Logbook_model->getHistoryLogbooksByUnit($unit_id, 'employee');

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

    public function report() {
        $unit_id = $this->session->userdata('unit_id');
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');

        // Pagination
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Filter by unit_id from session, and role 'employee'
        $logbooks = $this->Logbook_model->getAllLogbooks($start_date, $end_date, $unit_id, 'employee', $limit, $offset);
        $total_logbooks = $this->Logbook_model->countAllLogbooks($start_date, $end_date, $unit_id);
        $total_pages = ceil($total_logbooks / $limit);
        
        // Get activities for each logbook to display details
        foreach ($logbooks as &$logbook) {
            $logbook['activities'] = $this->Logbook_model->getActivitiesByLogbookId($logbook['id']);
        }

        $data = [
            'title' => 'Laporan Logbook Unit',
            'logbooks' => $logbooks,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'page' => $page,
            'total_pages' => $total_pages
        ];

        $this->load->view('report/head/index', ['data' => $data]);
    }

    public function export() {
        $unit_id = $this->session->userdata('unit_id');
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        $type = $this->input->get('type') ?? 'excel';

        // Filter by unit_id from session, and role 'employee'
        $logbooks = $this->Logbook_model->getAllLogbooks($start_date, $end_date, $unit_id, 'employee');
        
        // Prepare data with activities
        foreach ($logbooks as &$logbook) {
            $logbook['activities'] = $this->Logbook_model->getActivitiesByLogbookId($logbook['id']);
        }

        if ($type == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', 'Laporan Logbook Unit');
            $sheet->setCellValue('A2', 'Periode: ' . $start_date . ' s/d ' . $end_date);
            
            $headers = ['No', 'Tanggal', 'Nama Pegawai', 'Unit', 'Waktu', 'Kegiatan', 'Output', 'Kendala', 'Status'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $col++;
            }

            $row = 5;
            $no = 1;
            foreach ($logbooks as $logbook) {
                if (empty($logbook['activities'])) {
                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $logbook['date']);
                    $sheet->setCellValue('C' . $row, $logbook['user_name']);
                    $sheet->setCellValue('D' . $row, $logbook['unit_name']);
                    $sheet->setCellValue('I' . $row, $logbook['status']);
                    $row++;
                } else {
                    foreach ($logbook['activities'] as $activity) {
                        $sheet->setCellValue('A' . $row, $no++);
                        $sheet->setCellValue('B' . $row, $logbook['date']);
                        $sheet->setCellValue('C' . $row, $logbook['user_name']);
                        $sheet->setCellValue('D' . $row, $logbook['unit_name']);
                        $sheet->setCellValue('E' . $row, date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])));
                        $sheet->setCellValue('F' . $row, $activity['description']);
                        $sheet->setCellValue('G' . $row, $activity['output']);
                        $sheet->setCellValue('H' . $row, $activity['kendala']);
                        $sheet->setCellValue('I' . $row, $logbook['status']);
                        $row++;
                    }
                }
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan_Logbook_Unit.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;

        } elseif ($type == 'pdf') {
            $mpdf = new Mpdf(['orientation' => 'L']);
            
            $html = '<h2>Laporan Logbook Unit</h2>';
            $html .= '<p>Periode: ' . $start_date . ' s/d ' . $end_date . '</p>';
            $html .= '<table border="1" style="width:100%; border-collapse: collapse; font-size: 12px;">';
            $html .= '<thead><tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pegawai</th>
                        <th>Unit</th>
                        <th>Waktu</th>
                        <th>Kegiatan</th>
                        <th>Output</th>
                        <th>Kendala</th>
                        <th>Status</th>
                      </tr></thead><tbody>';
            
            $no = 1;
            foreach ($logbooks as $logbook) {
                if (empty($logbook['activities'])) {
                    $html .= '<tr>
                                <td>' . $no++ . '</td>
                                <td>' . $logbook['date'] . '</td>
                                <td>' . $logbook['user_name'] . '</td>
                                <td>' . $logbook['unit_name'] . '</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>' . $logbook['status'] . '</td>
                              </tr>';
                } else {
                    foreach ($logbook['activities'] as $activity) {
                        $html .= '<tr>
                                    <td>' . $no++ . '</td>
                                    <td>' . $logbook['date'] . '</td>
                                    <td>' . $logbook['user_name'] . '</td>
                                    <td>' . $logbook['unit_name'] . '</td>
                                    <td>' . date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])) . '</td>
                                    <td>' . $activity['description'] . '</td>
                                    <td>' . $activity['output'] . '</td>
                                    <td>' . $activity['kendala'] . '</td>
                                    <td>' . $logbook['status'] . '</td>
                                  </tr>';

                    }
                }
            }
            $html .= '</tbody></table>';
            
            $mpdf->WriteHTML($html);
            $mpdf->Output('Laporan_Logbook_Unit.pdf', 'D');
            exit;
        }
    }
    public function effectiveness() {
        $unit_id = $this->session->userdata('unit_id');
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        $employee_id = $this->input->get('employee_id');
        $period_type = $this->input->get('period_type') ?? 'daily';

        $effectiveness_data = $this->Logbook_model->getEffectivenessByUnit($unit_id, $start_date, $end_date, $employee_id, $period_type);
        $employees = $this->User_model->getEmployeesByUnit($unit_id);

        $data = [
            'title' => 'Laporan Efektifitas',
            'effectiveness_data' => $effectiveness_data,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'employees' => $employees,
            'selected_employee' => $employee_id,
            'period_type' => $period_type
        ];

        $this->load->view('report/head/effectiveness', ['data' => $data]);
    }

    public function export_effectiveness() {
        $unit_id = $this->session->userdata('unit_id');
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        $employee_id = $this->input->get('employee_id');
        $period_type = $this->input->get('period_type') ?? 'daily';
        $type = $this->input->get('type') ?? 'excel';

        $effectiveness_data = $this->Logbook_model->getEffectivenessByUnit($unit_id, $start_date, $end_date, $employee_id, $period_type);

        $period_label = 'Tanggal';
        if ($period_type == 'weekly') $period_label = 'Minggu';
        if ($period_type == 'monthly') $period_label = 'Bulan';

        if ($type == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', 'Laporan Efektifitas Jam Kerja');
            $sheet->setCellValue('A2', 'Periode: ' . $start_date . ' s/d ' . $end_date);
            
            $headers = ['No', $period_label, 'Nama Pegawai', 'NIK', 'Total Jam Kerja (Menit)', 'Total Jam Kerja (Jam)'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $col++;
            }

            $row = 5;
            $no = 1;
            foreach ($effectiveness_data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                
                $date_val = $item['date'] ?? $item['period'];
                if ($period_type == 'daily') {
                    $date_val = date('d/m/Y', strtotime($date_val));
                }
                
                $sheet->setCellValue('B' . $row, $date_val);
                $sheet->setCellValue('C' . $row, $item['user_name']);
                $sheet->setCellValue('D' . $row, $item['nik']);
                $sheet->setCellValue('E' . $row, $item['total_minutes']);
                $sheet->setCellValue('F' . $row, round($item['total_minutes'] / 60, 2));
                $row++;
            }

            foreach (range('A', 'F') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan_Efektifitas.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;

        } elseif ($type == 'pdf') {
            $mpdf = new Mpdf(['orientation' => 'P']);
            
            $html = '<h2>Laporan Efektifitas Jam Kerja</h2>';
            $html .= '<p>Periode: ' . $start_date . ' s/d ' . $end_date . '</p>';
            $html .= '<table border="1" style="width:100%; border-collapse: collapse; font-size: 12px;">';
            $html .= '<thead><tr>
                        <th>No</th>
                        <th>' . $period_label . '</th>
                        <th>Nama Pegawai</th>
                        <th>NIK</th>
                        <th>Total Menit</th>
                        <th>Total Jam</th>
                      </tr></thead><tbody>';
            
            $no = 1;
            foreach ($effectiveness_data as $item) {
                $date_val = $item['date'] ?? $item['period'];
                if ($period_type == 'daily') {
                    $date_val = date('d/m/Y', strtotime($date_val));
                }

                $html .= '<tr>
                            <td>' . $no++ . '</td>
                            <td>' . $date_val . '</td>
                            <td>' . $item['user_name'] . '</td>
                            <td>' . $item['nik'] . '</td>
                            <td>' . $item['total_minutes'] . '</td>
                            <td>' . round($item['total_minutes'] / 60, 2) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
            
            $mpdf->WriteHTML($html);
            $mpdf->Output('Laporan_Efektifitas.pdf', 'D');
            exit;
        }
    }
}
