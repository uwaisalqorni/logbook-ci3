<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit_model extends CI_Model {

    public function getAllUnits() {
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get('units');
        return $query->result_array();
    }

    public function addUnit($data) {
        $sql = "INSERT INTO units (name) VALUES (?)";
        return $this->db->query($sql, array($data['name']));
    }

    public function updateUnit($data) {
        $sql = "UPDATE units SET name = ? WHERE id = ?";
        return $this->db->query($sql, array($data['name'], $data['id']));
    }

    public function deleteUnit($id) {
        $sql = "DELETE FROM units WHERE id = ?";
        return $this->db->query($sql, array($id));
    }

    public function getUnitById($id) {
        $sql = "SELECT * FROM units WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        return $query->row_array();
    }

    public function countUnits() {
        return $this->db->count_all('units');
    }

    public function getUnitsByKabid($user_id) {
        $sql = "SELECT unit_id FROM kabid_units WHERE user_id = ?";
        $query = $this->db->query($sql, array($user_id));
        return array_column($query->result_array(), 'unit_id');
    }

    public function clearKabidUnits($user_id) {
        $sql = "DELETE FROM kabid_units WHERE user_id = ?";
        return $this->db->query($sql, array($user_id));
    }

    public function assignUnitToKabid($user_id, $unit_id) {
        $sql = "INSERT INTO kabid_units (user_id, unit_id) VALUES (?, ?)";
        return $this->db->query($sql, array($user_id, $unit_id));
    }
}
