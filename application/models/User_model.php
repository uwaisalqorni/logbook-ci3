<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $query = $this->db->query($sql, array($username));
        $row = $query->row_array();

        if ($row) {
            $hashed_password = $row['password'];
            if (password_verify($password, $hashed_password)) {
                return $row;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function findUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $query = $this->db->query($sql, array($username));

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllUsers() {
        $sql = "SELECT u.*, un.name as unit_name FROM users u LEFT JOIN units un ON u.unit_id = un.id ORDER BY u.name ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function addUser($data) {
        $sql = "INSERT INTO users (nik, name, username, password, role, unit_id, position, golongan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $binds = array(
            $data['nik'],
            $data['name'],
            $data['username'],
            $data['password'],
            $data['role'],
            $data['unit_id'],
            $data['position'],
            $data['golongan']
        );
        return $this->db->query($sql, $binds);
    }

    public function updateUser($data) {
        $sql = "UPDATE users SET nik=?, name=?, username=?, role=?, unit_id=?, position=?, golongan=?, status=?";
        $binds = array(
            $data['nik'],
            $data['name'],
            $data['username'],
            $data['role'],
            $data['unit_id'],
            $data['position'],
            $data['golongan'],
            $data['status']
        );

        if (!empty($data['password'])) {
            $sql .= ", password=?";
            $binds[] = $data['password'];
        }
        $sql .= " WHERE id=?";
        $binds[] = $data['id'];

        return $this->db->query($sql, $binds);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->query($sql, array($id));
    }

    public function countUsers() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        return $row['total'];
    }

    public function countActiveEmployees() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'employee' AND status = 'active'";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        return $row['total'];
    }
}
