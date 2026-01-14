<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class Management extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || $this->session->userdata('role') != 'management') {
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
        $stats = $this->Logbook_model->getLogbookStatsByUnit();
        $recent_logbooks = $this->Logbook_model->getRecentLogbooks();

        $data = [
            'title' => 'Management Dashboard',
            'stats' => $stats,
            'recent_logbooks' => $recent_logbooks,
            'total_users' => $this->User_model->countUsers(),
            'total_units' => $this->Unit_model->countUnits(),
            'today_logbooks' => $this->Logbook_model->countTodayLogbooks(),
            'submitted_today' => $this->Logbook_model->countSubmittedLogbooksToday(),
            'total_employees' => $this->User_model->countActiveEmployees()
        ];

        $this->load->view('management/dashboard', ['data' => $data]);
    }

    public function report() {
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        $unit_id = $this->input->get('unit_id') ?? '';

        // Pagination
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $logbooks = $this->Logbook_model->getAllLogbooks($start_date, $end_date, $unit_id, $limit, $offset);
        $total_logbooks = $this->Logbook_model->countAllLogbooks($start_date, $end_date, $unit_id);
        $total_pages = ceil($total_logbooks / $limit);
        
        $units = $this->Unit_model->getAllUnits();

        // Get activities for each logbook to display details
        foreach ($logbooks as &$logbook) {
            $logbook['activities'] = $this->Logbook_model->getActivitiesByLogbookId($logbook['id']);
        }

        $data = [
            'title' => 'Laporan Logbook',
            'logbooks' => $logbooks,
            'units' => $units,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'unit_id' => $unit_id,
            'page' => $page,
            'total_pages' => $total_pages
        ];

        $this->load->view('management/report', ['data' => $data]);
    }

    public function export() {
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');
        $unit_id = $this->input->get('unit_id') ?? '';
        $type = $this->input->get('type') ?? 'excel';

        $logbooks = $this->Logbook_model->getAllLogbooks($start_date, $end_date, $unit_id);
        
        // Prepare data with activities
        foreach ($logbooks as &$logbook) {
            $logbook['activities'] = $this->Logbook_model->getActivitiesByLogbookId($logbook['id']);
        }

        if ($type == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', 'Laporan Logbook Pegawai');
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
            header('Content-Disposition: attachment;filename="Laporan_Logbook.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;

        } elseif ($type == 'pdf') {
            $mpdf = new Mpdf(['orientation' => 'L']);
            
            $html = '<h2>Laporan Logbook Pegawai</h2>';
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
            $mpdf->Output('Laporan_Logbook.pdf', 'D');
            exit;
        }
    }

    public function charts() {
        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-d');

        $stats = $this->Logbook_model->getLogbookCountsByUnit($start_date, $end_date);

        // Prepare data for Chart.js
        $labels = [];
        $data_counts = [];
        $background_colors = [];
        
        // Generate random colors
        foreach ($stats as $stat) {
            $labels[] = $stat['unit_name'];
            $data_counts[] = $stat['total'];
            $background_colors[] = '#' . substr(md5($stat['unit_name']), 0, 6);
        }

        $data = [
            'title' => 'Laporan Grafik',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'labels' => json_encode($labels),
            'data_counts' => json_encode($data_counts),
            'background_colors' => json_encode($background_colors)
        ];

        $this->load->view('management/charts', ['data' => $data]);
    }
}
