<?php

class Client_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_clients_by_uid($uid,$order_by = array(),$where = array()) {
        $this->db->select("*")
                ->from('client')
                ->where('user = "' . $uid . '"');
		if(sizeof($where) && !empty($where['search_field']) && $where['search_field']!='')
		{
			$this->db->where($where['search_field']." LIKE '%".$where['search_text']."%'");

		}
		if(sizeof($order_by))
		{
			if($order_by[0]!='' && $order_by[1]!='')
			{
				$this->db->order_by($order_by[0],$order_by[1]);
			}
		}
		
        $db = $this->db->get();
        return $db->result();
    }

    public function get_client_closed_birthday($uid, $date) {
        $this->db->select("*")
                ->from('client')
                ->where('user = "' . $uid . '" AND dt_nais LIKE "%' . $date . '"');
        $db = $this->db->get();
        return $db->result();
    }

    public function get_object_closed_birthday($uid, $date) {
        /**
         * find client
         */
        $this->db->select("*")
                ->from('client')
                ->where('user = "' . $uid . '" AND sms_object_versaire = 1');
        $db = $this->db->get();
        $clients = $db->result();
        
        /**
         * find related client objects
         */
        $data_clients = array();
        $data_objects = array();
        $result = array();
        foreach ($clients AS $client) {
            $this->db->select("*")
                    ->from('client_object_entities')
                    ->where('client_id = ' . $client->client_id);
            $db = $this->db->get();
            $objects = $db->result();
            
            $has_objects = false;
            foreach ($objects AS $object) {
                $dynamic_fields = json_decode($object->dynamic_fields, true);
                $name = $dynamic_fields[1];
                $race = $dynamic_fields[3];
                $birthdate = $dynamic_fields[2];
                $date_compare = explode('-', $birthdate);
                $date2 = '-' . $date_compare[1] . '-' . $date_compare[2];
                if ($date2 == $date) {
                    $a = array(
                        'name' => $name,
                        'race' => $race,
                        'birthdate' => $birthdate
                    );
                    array_push($data_objects, $a);
                    $has_objects = true;
                }
            }
            if ($has_objects) {
                $data_clients = array(
                    'client' => json_decode(json_encode($client), true),
                    'objects' => $data_objects,
                );
                array_push($result, $data_clients);
            }
        }

        return $result;
    }

    public function get_client_by_id($client_id) {
        $this->db->select("*")
                ->from('client')
                ->where("client_id = '" . $client_id . "'");
        $db = $this->db->get();
        return $db->row(0);
    }

    public function find_client_by_criteria($criteria) {
        $this->db->select("*")
                ->from('client')
                ->where("nom LIKE \"%" . $criteria . "%\" OR prenom LIKE \"%" . $criteria . "%\" OR adresse LIKE \"%" . $criteria . "%\" OR pays LIKE \"%" . $criteria . "%\"");
        $db = $this->db->get();
        return $db->result();
    }

    public function find_cities_by_criteria($criteria) {
        $this->db->select("*")
                ->from('cities')
                ->where("postal_code LIKE \"" . $criteria . "%\" OR postal_code LIKE \"%-" . $criteria . "%\"");
        $db = $this->db->get();
        return $db->result();
    }

    function update_messaging_status($client_id, $status_id) {
        $data = array(
            'sms_versaire' => $status_id
        );
        $this->db->where('client_id', $client_id);
        return $this->db->update('client', $data);
    }

    function get_client_fields() {
        $this->db->select("*")
                ->from('client_fields');
        $db = $this->db->get();
        return $db->result();
    }

    function get_client_field_by_field_id($field_id) {
        $this->db->select("*")
                ->from('client_fields')
                ->where('field_id', $field_id);
        $db = $this->db->get();
        return $db->row(0);
    }

    function get_client_field_option_value($option_id) {
        $this->db->select("*")
                ->from('client_options')
                ->where('option_id', $option_id);
        $db = $this->db->get();
        return $db->row(0);
    }

    function get_client_fields_by_user($uid) {
        $this->db->select("*")
                ->from('client_fields_filter')
                ->join('client_fields', 'client_fields_filter.field_id = client_fields.field_id')
                ->where('client_fields_filter.uid', $uid);
        $db = $this->db->get();
        return $db->result();
    }

    function get_client_field_options($field_id) {
        $this->db->select("*")
                ->from('client_field_options')
                ->join('client_options', 'client_options.option_id = client_field_options.option_id')
                ->where('client_field_options.field_id', $field_id);
        $db = $this->db->get();
        return $db->result();
    }

    function get_client_object_field_options($field_id) {
        $this->db->select("*")
                ->from('client_object_field_options')
                ->join('client_options', 'client_options.option_id = client_object_field_options.option_id')
                ->where('client_object_field_options.field_id', $field_id);
        $db = $this->db->get();
        return $db->result();
    }

    function get_client_object_fields_by_object_id($object_id) {
        $this->db->select("*")
                ->from('client_object_fields')
                ->where('object_id', $object_id);
        $db = $this->db->get();
        return $db->result();
    }

    public function get_object_by_id($object_id) {
        $this->db->select("*")
                ->from('client_object_entities')
                ->where("entity_id = '" . $object_id . "'");
        $db = $this->db->get();
        return $db->row(0);
    }

    public function save_client_option($options) {
        $this->db->insert('client_options', $options);
        return $this->db->insert_id();
    }

    public function save_field_option($options) {
        $this->db->insert('client_field_options', $options);
        return $this->db->insert_id();
    }

    function test_client_query($uid, $table_field, $table_condition, $table_value) {
        $this->db->select("*")
                ->from('client');
        switch ($table_condition) {
            case '~': {
                    $this->db->where('client.user = "' . $uid . '" AND lower(' . $table_field . ') LIKE lower("' . $table_value . '%")');
                }break;
            case '=': {
                    $this->db->where('client.user = "' . $uid . '" AND ' . $table_field . ' = "' . $table_value . '"');
                }break;
            case '<=': {
                    $this->db->where('client.user = "' . $uid . '" AND ' . $table_field . ' <= ' . $table_value);
                }break;
            case '>=': {
                    $this->db->where('client.user = "' . $uid . '" AND ' . $table_field . ' >= ' . $table_value);
                }break;
        }
        $db = $this->db->get();
        return $db->result();
    }

    function get_client_by_ids($ids) {
        $this->db->select("*")
                ->from('client')
                ->where('client_id IN(' . $ids . ')');
        $db = $this->db->get();
        return $db->result();
    }

}
