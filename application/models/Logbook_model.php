<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logbook_model extends CI_Model {

    // --- Logbook Header Operations ---

    public function getLogbookByUserAndDate($user_id, $date) {
        $sql = "SELECT * FROM logbooks WHERE user_id = ? AND date = ?";
        $query = $this->db->query($sql, array($user_id, $date));
        return $query->row_array();
    }

    public function createLogbook($user_id, $date) {
        $sql = "INSERT INTO logbooks (user_id, date, status) VALUES (?, ?, 'draft')";
        if ($this->db->query($sql, array($user_id, $date))) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function getLogbookById($id) {
        $sql = "SELECT * FROM logbooks WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        return $query->row_array();
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE logbooks SET status = ? WHERE id = ?";
        $result = $this->db->query($sql, array($status, $id));

        if ($result && ($status == 'revision' || $status == 'approved' || $status == 'rejected')) {
            $sql_details = "UPDATE logbook_details SET status = ? WHERE logbook_id = ? AND status = 'submitted'";
            $this->db->query($sql_details, array($status, $id));
        }
        return $result;
    }

    public function getLogbooksByUserId($user_id) {
        $sql = "SELECT * FROM logbooks WHERE user_id = ? ORDER BY date DESC";
        $query = $this->db->query($sql, array($user_id));
        return $query->result_array();
    }

    public function getLogbooksByUserIdAndDateRange($user_id, $start_date, $end_date, $limit = null, $offset = null) {
        $sql = "SELECT * FROM logbooks WHERE user_id = ? AND date BETWEEN ? AND ? ORDER BY date DESC";
        $binds = array($user_id, $start_date, $end_date);
        
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $binds[] = (int)$limit;
            $binds[] = (int)$offset;
        }
        
        $query = $this->db->query($sql, $binds);
        return $query->result_array();
    }

    public function countLogbooksByUserIdAndDateRange($user_id, $start_date, $end_date) {
        $sql = "SELECT COUNT(*) as total FROM logbooks WHERE user_id = ? AND date BETWEEN ? AND ?";
        $query = $this->db->query($sql, array($user_id, $start_date, $end_date));
        $row = $query->row_array();
        return $row['total'];
    }

    public function getLogbooksWithActivities($user_id, $start_date, $end_date) {
        $sql = "SELECT l.id as logbook_id, l.date, l.status as logbook_status, 
                       ld.id as activity_id, ld.description, ld.start_time, ld.end_time, ld.output, ld.kendala, ld.status as activity_status,
                       at.name as activity_name
                FROM logbooks l
                LEFT JOIN logbook_details ld ON l.id = ld.logbook_id
                LEFT JOIN activity_types at ON ld.activity_type_id = at.id
                WHERE l.user_id = ? AND l.date BETWEEN ? AND ?
                ORDER BY l.date ASC, ld.start_time ASC";
        
        $query = $this->db->query($sql, array($user_id, $start_date, $end_date));
        $results = $query->result_array();
        
        // Group by logbook
        $logbooks = [];
        foreach ($results as $row) {
            $logbook_id = $row['logbook_id'];
            if (!isset($logbooks[$logbook_id])) {
                $logbooks[$logbook_id] = [
                    'id' => $logbook_id,
                    'date' => $row['date'],
                    'status' => $row['logbook_status'],
                    'activities' => []
                ];
            }
            
            if ($row['activity_id']) {
                $logbooks[$logbook_id]['activities'][] = [
                    'id' => $row['activity_id'],
                    'description' => $row['description'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'output' => $row['output'],
                    'kendala' => $row['kendala'],
                    'status' => $row['activity_status'],
                    'activity_name' => $row['activity_name']
                ];
            }
        }
        
        return array_values($logbooks);
    }

    public function countLogbooksByUser($user_id) {
        $sql = "SELECT COUNT(*) as total FROM logbooks WHERE user_id = ?";
        $query = $this->db->query($sql, array($user_id));
        $row = $query->row_array();
        return $row['total'];
    }

    public function getAllLogbooks($start_date, $end_date, $unit_id = null, $role = null, $limit = null, $offset = null) {
        $sql = "SELECT l.*, u.name as user_name, u.role, un.name as unit_name 
                FROM logbooks l 
                JOIN users u ON l.user_id = u.id 
                JOIN units un ON u.unit_id = un.id 
                WHERE l.date BETWEEN ? AND ?";
        
        $binds = array($start_date, $end_date);

        if ($unit_id) {
            if (is_array($unit_id)) {
                if (!empty($unit_id)) {
                    $placeholders = implode(',', array_fill(0, count($unit_id), '?'));
                    $sql .= " AND u.unit_id IN ($placeholders)";
                    $binds = array_merge($binds, $unit_id);
                }
            } else {
                $sql .= " AND u.unit_id = ?";
                $binds[] = $unit_id;
            }
        }

        if ($role) {
            $sql .= " AND u.role = ?";
            $binds[] = $role;
        }
        
        $sql .= " ORDER BY l.date DESC, u.name ASC";

        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $binds[] = (int)$limit;
            $binds[] = (int)$offset;
        }
        
        $query = $this->db->query($sql, $binds);
        return $query->result_array();
    }

    public function getLogbookReportDetails($start_date, $end_date, $unit_id = null, $role = null) {
        $sql = "SELECT l.id as logbook_id, l.date, l.status as logbook_status,
                       u.name as user_name, u.nik, u.role, un.name as unit_name,
                       ld.description, ld.output, ld.kendala, ld.status as activity_status, at.name as activity_name
                FROM logbooks l 
                JOIN users u ON l.user_id = u.id 
                JOIN units un ON u.unit_id = un.id 
                LEFT JOIN logbook_details ld ON l.id = ld.logbook_id
                LEFT JOIN activity_types at ON ld.activity_type_id = at.id
                WHERE l.date BETWEEN ? AND ?";
        
        $binds = array($start_date, $end_date);

        if ($unit_id) {
            if (is_array($unit_id)) {
                if (!empty($unit_id)) {
                    $placeholders = implode(',', array_fill(0, count($unit_id), '?'));
                    $sql .= " AND u.unit_id IN ($placeholders)";
                    $binds = array_merge($binds, $unit_id);
                }
            } else {
                $sql .= " AND u.unit_id = ?";
                $binds[] = $unit_id;
            }
        }

        if ($role) {
            $sql .= " AND u.role = ?";
            $binds[] = $role;
        }
        
        $sql .= " ORDER BY l.date ASC, u.name ASC, ld.start_time ASC";
        
        $query = $this->db->query($sql, $binds);
        return $query->result_array();
    }

    public function countAllLogbooks($start_date, $end_date, $unit_id = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM logbooks l 
                JOIN users u ON l.user_id = u.id 
                WHERE l.date BETWEEN ? AND ?";
        
        $binds = array($start_date, $end_date);

        if ($unit_id) {
            $sql .= " AND u.unit_id = ?";
            $binds[] = $unit_id;
        }
        
        $query = $this->db->query($sql, $binds);
        $row = $query->row_array();
        return $row['total'];
    }

    // --- Logbook Details (Activities) Operations ---

    public function getActivitiesByLogbookId($logbook_id) {
        $sql = "SELECT ld.*, at.name as activity_name FROM logbook_details ld LEFT JOIN activity_types at ON ld.activity_type_id = at.id WHERE ld.logbook_id = ? ORDER BY ld.start_time ASC";
        $query = $this->db->query($sql, array($logbook_id));
        return $query->result_array();
    }

    public function addActivity($data) {
        $sql = "INSERT INTO logbook_details (logbook_id, activity_type_id, description, start_time, end_time, output, kendala, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'draft')";
        $binds = array(
            $data['logbook_id'],
            $data['activity_type_id'],
            $data['description'],
            $data['start_time'],
            $data['end_time'],
            $data['output'],
            $data['kendala']
        );
        return $this->db->query($sql, $binds);
    }

    public function updateActivity($data) {
        $sql = "UPDATE logbook_details SET activity_type_id=?, description=?, start_time=?, end_time=?, output=?, kendala=? WHERE id=?";
        $binds = array(
            $data['activity_type_id'],
            $data['description'],
            $data['start_time'],
            $data['end_time'],
            $data['output'],
            $data['kendala'],
            $data['id']
        );
        return $this->db->query($sql, $binds);
    }

    public function deleteActivity($id) {
        $sql = "DELETE FROM logbook_details WHERE id = ?";
        return $this->db->query($sql, array($id));
    }

    public function getActivityById($id) {
        $sql = "SELECT * FROM logbook_details WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        return $query->row_array();
    }

    public function submitActivity($id) {
        $sql = "UPDATE logbook_details SET status = 'submitted' WHERE id = ?";
        return $this->db->query($sql, array($id));
    }
    
    // --- For Head of Unit ---
    public function getPendingLogbooksByUnit($unit_id, $role = null) {
        $sql = "SELECT l.*, u.name as user_name, u.nik FROM logbooks l JOIN users u ON l.user_id = u.id WHERE l.status = 'submitted'";
        $binds = array();

        if (is_array($unit_id)) {
            if (empty($unit_id)) {
                return []; // No units assigned
            }
            $placeholders = implode(',', array_fill(0, count($unit_id), '?'));
            $sql .= " AND u.unit_id IN ($placeholders)";
            $binds = array_merge($binds, $unit_id);
        } else {
            $sql .= " AND u.unit_id = ?";
            $binds[] = $unit_id;
        }
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $binds[] = $role;
        }
        
        $sql .= " ORDER BY l.date ASC";
        $query = $this->db->query($sql, $binds);
        return $query->result_array();
    }

    public function getHistoryLogbooksByUnit($unit_id, $role = null) {
        $sql = "SELECT l.*, u.name as user_name, u.nik FROM logbooks l JOIN users u ON l.user_id = u.id WHERE l.status IN ('approved', 'rejected')";
        $binds = array();

        if (is_array($unit_id)) {
            if (empty($unit_id)) {
                return []; // No units assigned
            }
            $placeholders = implode(',', array_fill(0, count($unit_id), '?'));
            $sql .= " AND u.unit_id IN ($placeholders)";
            $binds = array_merge($binds, $unit_id);
        } else {
            $sql .= " AND u.unit_id = ?";
            $binds[] = $unit_id;
        }
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $binds[] = $role;
        }
        
        $sql .= " ORDER BY l.date DESC";
        $query = $this->db->query($sql, $binds);
        return $query->result_array();
    }

    public function countPendingLogbooksByUnit($unit_id, $role = null) {
        $sql = "SELECT COUNT(*) as total FROM logbooks l JOIN users u ON l.user_id = u.id WHERE l.status = 'submitted'";
        $binds = array();

        if (is_array($unit_id)) {
            if (empty($unit_id)) {
                return 0;
            }
            $placeholders = implode(',', array_fill(0, count($unit_id), '?'));
            $sql .= " AND u.unit_id IN ($placeholders)";
            $binds = array_merge($binds, $unit_id);
        } else {
            $sql .= " AND u.unit_id = ?";
            $binds[] = $unit_id;
        }
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $binds[] = $role;
        }
        
        $query = $this->db->query($sql, $binds);
        return $query->row()->total;
    }

    public function countHistoryLogbooksByUnit($unit_id, $role = null) {
        $sql = "SELECT COUNT(*) as total FROM logbooks l JOIN users u ON l.user_id = u.id WHERE l.status IN ('approved', 'rejected')";
        $binds = array();

        if (is_array($unit_id)) {
            if (empty($unit_id)) {
                return 0;
            }
            $placeholders = implode(',', array_fill(0, count($unit_id), '?'));
            $sql .= " AND u.unit_id IN ($placeholders)";
            $binds = array_merge($binds, $unit_id);
        } else {
            $sql .= " AND u.unit_id = ?";
            $binds[] = $unit_id;
        }
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $binds[] = $role;
        }
        
        $query = $this->db->query($sql, $binds);
        return $query->row()->total;
    }

    // --- For Management ---
    public function getLogbookStatsByUnit() {
        $sql = "SELECT un.name, COUNT(l.id) as total FROM units un LEFT JOIN users u ON un.id = u.unit_id LEFT JOIN logbooks l ON u.id = l.user_id AND MONTH(l.date) = MONTH(CURRENT_DATE()) AND YEAR(l.date) = YEAR(CURRENT_DATE()) GROUP BY un.id ORDER BY total DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getLogbookCountsByUnit($start_date, $end_date) {
        $sql = "SELECT un.name as unit_name, COUNT(l.id) as total 
                FROM logbooks l 
                JOIN users u ON l.user_id = u.id 
                JOIN units un ON u.unit_id = un.id 
                WHERE l.date BETWEEN ? AND ? 
                GROUP BY un.id 
                ORDER BY total DESC";
        $query = $this->db->query($sql, array($start_date, $end_date));
        return $query->result_array();
    }
    
    public function getRecentLogbooks() {
         $sql = "SELECT l.*, u.name as user_name, un.name as unit_name FROM logbooks l JOIN users u ON l.user_id = u.id JOIN units un ON u.unit_id = un.id ORDER BY l.date DESC LIMIT 5";
         $query = $this->db->query($sql);
         return $query->result_array();
    }

    public function countTodayLogbooks() {
        $sql = "SELECT COUNT(DISTINCT user_id) as total FROM logbooks WHERE date = CURRENT_DATE";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        return $row['total'];
    }

    public function countSubmittedLogbooksToday() {
        $sql = "SELECT COUNT(DISTINCT user_id) as total FROM logbooks WHERE date = CURRENT_DATE AND status IN ('submitted', 'approved', 'rejected', 'revision')";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        return $row['total'];
    }

    public function countSubmittedLogbooksByUnitToday($unit_id) {
        $sql = "SELECT COUNT(DISTINCT l.user_id) as total 
                FROM logbooks l 
                JOIN users u ON l.user_id = u.id 
                WHERE l.date = CURRENT_DATE 
                AND u.unit_id = ? 
                AND l.status IN ('submitted', 'approved', 'rejected', 'revision')";
        $query = $this->db->query($sql, array($unit_id));
        $row = $query->row_array();
        return $row['total'];
    }

    public function addValidation($data) {
        $sql = "INSERT INTO validations (logbook_id, validator_id, status, notes) VALUES (?, ?, ?, ?)";
        $binds = array(
            $data['logbook_id'],
            $data['validator_id'],
            $data['status'],
            $data['notes']
        );
        return $this->db->query($sql, $binds);
    }

    public function getValidationByLogbookId($logbook_id) {
        $sql = "SELECT * FROM validations WHERE logbook_id = ? ORDER BY validated_at DESC LIMIT 1";
        $query = $this->db->query($sql, array($logbook_id));
        return $query->row_array();
    }
    public function getEffectivenessByUnit($unit_id, $start_date, $end_date, $employee_id = null, $period_type = 'daily') {
        $select_date = "l.date";
        $group_by = "u.id, l.date";
        $order_by = "l.date DESC, u.name ASC";

        if ($period_type == 'weekly') {
            $select_date = "CONCAT(YEAR(l.date), ' - Week ', WEEK(l.date)) as period";
            $group_by = "u.id, YEAR(l.date), WEEK(l.date)";
            $order_by = "YEAR(l.date) DESC, WEEK(l.date) DESC, u.name ASC";
        } elseif ($period_type == 'monthly') {
            $select_date = "DATE_FORMAT(l.date, '%Y-%m') as period";
            $group_by = "u.id, YEAR(l.date), MONTH(l.date)";
            $order_by = "YEAR(l.date) DESC, MONTH(l.date) DESC, u.name ASC";
        }

        $sql = "SELECT u.name as user_name, u.nik, $select_date, 
                       SUM(TIMESTAMPDIFF(MINUTE, ld.start_time, ld.end_time)) as total_minutes
                FROM users u
                JOIN logbooks l ON u.id = l.user_id
                JOIN logbook_details ld ON l.id = ld.logbook_id
                WHERE u.unit_id = ? AND l.date BETWEEN ? AND ? AND l.status IN ('submitted', 'approved', 'rejected')";
        
        $binds = array($unit_id, $start_date, $end_date);

        if ($employee_id) {
            $sql .= " AND u.id = ?";
            $binds[] = $employee_id;
        }

        $sql .= " GROUP BY $group_by ORDER BY $order_by";
        
        $query = $this->db->query($sql, $binds);
        return $query->result_array();
    }
}
