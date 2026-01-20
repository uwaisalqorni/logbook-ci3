<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class ReportKabid extends CI_Controller {

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

        $this->load->view('report/kabid/index', ['data' => $data]);
    }

    public function export() {
        $user_id = $this->session->userdata('user_id');
        $unit_id = $this->session->userdata('unit_id');
        
        $start_date = $this->input->get('start_date') ?: date('Y-m-01');
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');
        $selected_unit = $this->input->get('unit_id');
        $selected_role = $this->input->get('role');
        $type = $this->input->get('type') ?? 'excel';

        // Get units assigned to Kabid
        $assigned_units = $this->Unit_model->getUnitsByKabid($user_id);
        if (empty($assigned_units)) {
            $target_units = [$unit_id];
        } else {
            $target_units = $assigned_units;
        }

        // Determine filter unit
        if ($selected_unit && in_array($selected_unit, $target_units)) {
            $filter_unit = $selected_unit;
        } else {
            $filter_unit = $target_units;
        }

        // Get data
        $logbooks = $this->Logbook_model->getLogbookReportDetails($start_date, $end_date, $filter_unit, $selected_role);

        if ($type == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Header
            $sheet->setCellValue('A1', 'Laporan Logbook');
            $sheet->setCellValue('A2', 'Periode: ' . $start_date . ' s/d ' . $end_date);
            
            $headers = ['No', 'Tanggal', 'Nama Pegawai', 'NIK', 'Role', 'Unit', 'Kegiatan', 'Output', 'Kendala', 'Status Logbook'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $col++;
            }

            $row = 5;
            $no = 1;
            foreach ($logbooks as $logbook) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $logbook['date']);
                $sheet->setCellValue('C' . $row, $logbook['user_name']);
                $sheet->setCellValue('D' . $row, $logbook['nik']);
                $sheet->setCellValue('E' . $row, ucfirst($logbook['role']));
                $sheet->setCellValue('F' . $row, $logbook['unit_name']);
                
                $activity_desc = '';
                if ($logbook['description']) {
                    $activity_desc = $logbook['activity_name'] . "\n" . $logbook['description'];
                }
                $sheet->setCellValue('G' . $row, $activity_desc);
                $sheet->getStyle('G' . $row)->getAlignment()->setWrapText(true);
                
                $sheet->setCellValue('H' . $row, $logbook['output']);
                $sheet->setCellValue('I' . $row, $logbook['kendala']);
                $sheet->setCellValue('J' . $row, ucfirst($logbook['logbook_status']));
                $row++;
            }

            foreach (range('A', 'J') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Laporan_Logbook_' . date('YmdHis') . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            exit;

        } elseif ($type == 'pdf') {
            $mpdf = new Mpdf(['orientation' => 'L']);
            
            $html = '<h2>Laporan Logbook</h2>';
            $html .= '<p>Periode: ' . $start_date . ' s/d ' . $end_date . '</p>';
            $html .= '<table border="1" style="width:100%; border-collapse: collapse; font-size: 12px;">';
            $html .= '<thead><tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pegawai</th>
                        <th>Role</th>
                        <th>Unit</th>
                        <th>Kegiatan</th>
                        <th>Output</th>
                        <th>Kendala</th>
                        <th>Status</th>
                      </tr></thead><tbody>';
            
            $no = 1;
            foreach ($logbooks as $logbook) {
                $activity_desc = '-';
                if ($logbook['description']) {
                    $activity_desc = '<strong>' . $logbook['activity_name'] . '</strong><br>' . nl2br($logbook['description']);
                }

                $html .= '<tr>
                            <td>' . $no++ . '</td>
                            <td>' . $logbook['date'] . '</td>
                            <td>' . $logbook['user_name'] . '<br><small>' . $logbook['nik'] . '</small></td>
                            <td>' . ucfirst($logbook['role']) . '</td>
                            <td>' . $logbook['unit_name'] . '</td>
                            <td>' . $activity_desc . '</td>
                            <td>' . ($logbook['output'] ?: '-') . '</td>
                            <td>' . ($logbook['kendala'] ?: '-') . '</td>
                            <td>' . ucfirst($logbook['logbook_status']) . '</td>
                          </tr>';
            }
            $html .= '</tbody></table>';
            
            $mpdf->WriteHTML($html);
            $mpdf->Output('Laporan_Logbook_' . date('YmdHis') . '.pdf', 'D');
            exit;
        }
    }
}
