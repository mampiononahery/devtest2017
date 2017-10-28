<?php

class Rdv_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_rdv_by_id($rdv_id) {
        $this->db->select("*")
                ->from('rdv')
                ->where('rdv_id', $rdv_id);
        $db = $this->db->get();
        return $db->row(0);
    }

    public function get_prd_rdv_by_rdv_id($rdv_id) {
        $this->db->select("*")
                ->from('prd_rdv')
                ->join('production', 'production.prod_id = prd_rdv.id_prd')
                ->join('rdv', 'prd_rdv.id_rdv = rdv.rdv_id')
                ->where('id_rdv', $rdv_id);
        $db = $this->db->get();
        return $db->result();
    }

    public function get_prd_rdv_by_client_id($client_id) {
        $this->db->select("*")
                ->from('prd_rdv')
                ->join('production', 'production.prod_id = prd_rdv.id_prd')
                ->join('rdv', 'prd_rdv.id_rdv = rdv.rdv_id')
                ->where('rdv.id_client', $client_id);
        $db = $this->db->get();
        return $db->result();
    }

    public function get_all_rdv_by_uid($uid) {
        $this->db->select("*")
                ->from('rdv')
                ->join('client', 'rdv.id_client = client.client_id')
                ->where('rdv.user', $uid);
        $db = $this->db->get();
        return $db->result();
    }

    public function get_rdv_closed_date($uid, $date) {
        $this->db->select("*")
                ->from('rdv')
                ->join('client', 'rdv.id_client = client.client_id')
                ->where('rdv.user = "' . $uid . '" AND rdv.dt_start LIKE "%' . $date . '%"');
        $db = $this->db->get();
        return $db->result();
    }

    public function save_rdv($rdv) {
        $this->db->insert('rdv', $rdv);
        return $this->db->insert_id();
    }

    public function save_prd_rdv($prd_rdv) {
        $this->db->insert('prd_rdv', $prd_rdv);
        return $this->db->insert_id();
    }

    public function update_rdv($rdv, $rdv_id) {
        $this->db->update('rdv', $rdv, array('rdv_id' => $rdv_id));
    }

    public function delete_prd_rdv($rdv_id) {
        $this->db->delete('prd_rdv', array('id_rdv' => $rdv_id));
    }

    public function delete_rdv($rdv_id) {
        $this->db->delete('rdv', array('rdv_id' => $rdv_id));
    }

    function test_rdv_query($uid, $table_field, $table_condition, $table_value) {
        $this->db->select("rdv_id")
                ->from('rdv');
        switch ($table_condition) {
            case '~': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' LIKE "%' . $table_value . '%"');
                }break;
            case '=': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' = "' . $table_value . '"');
                }break;
            case '<=': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' <= "' . $table_value . '"');
                }break;
            case '>=': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' >= "' . $table_value . '"');
                }break;
        }
        $db = $this->db->get();
        return $db->result();
    }

    function test_prd_rdv_query($uid, $table_field, $table_condition, $table_value) {
        $this->db->select("*")
                ->from('rdv')
                ->join('prd_rdv', 'rdv.rdv_id = prd_rdv.id_rdv')
                ->join('resource', 'rdv.id_ressource = resource.resource_id')
                ->join('client', 'rdv.id_client = client.client_id');
        switch ($table_condition) {
            case '~': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' LIKE "' . $table_value . '%"');
                }break;
            case '=': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' = "' . $table_value . '"');
                }break;
            case '<=': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' <= "' . $table_value . '"');
                }break;
            case '>=': {
                    $this->db->where('rdv.user = "' . $uid . '" AND ' . $table_field . ' >= "' . $table_value . '"');
                }break;
        }
        $db = $this->db->get();
        return $db->result();
    }

    function get_rdv_by_ids($ids) {
        $this->db->select("*")
                ->from('rdv')
                ->join('resource', 'rdv.id_ressource = resource.resource_id')
                ->join('client', 'rdv.id_client = client.client_id')
                ->where('rdv_id IN(' . $ids . ')');
        $db = $this->db->get();
        return $db->result();
    }

    function get_prd_rdv_by_ids($ids) {
        $this->db->select("*")
                ->from('rdv')
                ->join('prd_rdv', 'rdv.rdv_id = prd_rdv.id_rdv')
                ->join('resource', 'rdv.id_ressource = resource.resource_id')
                ->join('client', 'rdv.id_client = client.client_id')
                ->where('prd_rdv_id IN(' . $ids . ')');
        $db = $this->db->get();
        return $db->result();
    }

}
