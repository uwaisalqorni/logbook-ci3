<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ActivityType_model extends CI_Model {

    public function getAllActivityTypes() {
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get('activity_types');
        return $query->result_array();
    }

    public function addActivityType($data) {
        $sql = "INSERT INTO activity_types (name) VALUES (?)";
        return $this->db->query($sql, array($data['name']));
    }

    public function updateActivityType($data) {
        $sql = "UPDATE activity_types SET name = ? WHERE id = ?";
        return $this->db->query($sql, array($data['name'], $data['id']));
    }

    public function deleteActivityType($id) {
        $sql = "DELETE FROM activity_types WHERE id = ?";
        return $this->db->query($sql, array($id));
    }

    public function getActivityTypeById($id) {
        $sql = "SELECT * FROM activity_types WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        return $query->row_array();
    }
}
