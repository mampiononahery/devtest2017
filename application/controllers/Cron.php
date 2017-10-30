<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . '/controllers/Front.php');

class Cron extends Front {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->load->model('Client_model');
        $this->load->library('oc_auth');
        $this->load->model('Sms_model');
        $this->load->model('Rdv_model');
        $this->load->library('api_sms');
        $this->load->helper('date');

        $client_model = new Client_model();
        $sms_model = new Sms_model();
        $rdv_model = new Rdv_model();
        $api_sms = new Api_sms();

        $sms_settings = $sms_model->get_active_sms_notification_setting();

        foreach ($sms_settings AS $sms_setting) {
            $rdv_cron_start = isset($sms_setting->rdv_notification_start) && (int) $sms_setting->rdv_notification_start > 0 ? (int) $sms_setting->rdv_notification_start : 1;
            $birthday_cron_start = isset($sms_setting->birthday_notification_start) && (int) $sms_setting->birthday_notification_start > 0 ? (int) $sms_setting->birthday_notification_start : 7;

            /**
             * SEND BIRTHDAY SMS
             */
            $date_close = date_close(date('Y-m-d'), $birthday_cron_start);
            $clients = $client_model->get_client_closed_birthday($sms_setting->user, $date_close);

            if (!empty($clients)) {
                foreach ($clients AS $entity) {
                    $formatted_number = str_replace('+', '00', $entity->tel_mobile);
                    $pattern = $sms_model->get_sms_pattern($sms_setting->user, 3);
                    $str1 = str_replace('%NOM%', $entity->nom, $pattern->pattern);
                    $str2 = str_replace('%PRENOM%', $entity->prenom, $str1);

                    $api_sms->set_destination_number($formatted_number);
                    $api_sms->set_sms(strip_tags($str2));

                    $data['state'] = $api_sms->send_sms();

                    if ($data['state']) {
                        $message_log = array(
                            'user' => $sms_setting->user,
                            'phone_number' => $formatted_number,
                            'message' => $str2,
                            'sending_type' => 'Anniversaire'
                        );
                        $sms_model->save_log($message_log);
                    }
                }
            }

            /**
             * SEND DOGS BIRTHDAY SMS
             */
            $date_close3 = date_close(date('Y-m-d'), $birthday_cron_start);
            $data = $client_model->get_object_closed_birthday($sms_setting->user, $date_close3);

            foreach ($data AS $entity) {
                $client = $entity['client'];
                $formatted_number = str_replace('+', '00', $client['tel_mobile']);
                $pattern = $sms_model->get_sms_pattern($sms_setting->user, 4);
                $str1 = str_replace('%NOM%', $client['nom'], $pattern->pattern);
                $str2 = str_replace('%PRENOM%', $client['prenom'], $str1);

                if (!empty($entity['objects'])) {
                    $str2 .= 'Chien(s):';
                    foreach ($entity['objects'] AS $object) {
                        $str2 .= $object['name'] . ' ,';
                    }
                }

                $str3 = trim($str2, ' ,');

                $api_sms->set_destination_number($formatted_number);
                $api_sms->set_sms(strip_tags($str3));

                $data['state'] = $api_sms->send_sms();

                if ($data['state']) {
                    $message_log = array(
                        'user' => $sms_setting->user,
                        'phone_number' => $formatted_number,
                        'message' => $str2,
                        'sending_type' => 'Anniversaire'
                    );
                    $sms_model->save_log($message_log);
                }
            }

            /**
             * SEND RDV SMS
             */
            $date_close2 = date_close(date('Y-m-d'), $rdv_cron_start);
            $rdv = $rdv_model->get_rdv_closed_date($sms_setting->user, $date_close2);

            if (!empty($rdv)) {
                foreach ($rdv AS $entity) {
                    $formatted_number = str_replace('+', '00', $entity->tel_mobile);
                    $pattern = $sms_model->get_sms_pattern($sms_setting->user, 1);

                    $str1 = str_replace('%NOM%', $entity->nom, $pattern->pattern);
                    $str2 = str_replace('%PRENOM%', $entity->prenom, $str1);
                    $str3 = str_replace('%DATE%', $entity->dt_start, $str2);

                    $api_sms->set_destination_number($formatted_number);
                    $api_sms->set_sms(strip_tags($str3));

                    $data['state'] = $api_sms->send_sms();

                    if ($data['state']) {
                        $message_log = array(
                            'user' => $sms_setting->user,
                            'phone_number' => $formatted_number,
                            'message' => $str2,
                            'sending_type' => 'Rappel de rdv'
                        );
                        $sms_model->save_log($message_log);
                    }
                }
            }
        }
    }

    public function alerte() {
        $this->load->library('oc_auth');

        $this->load->model('Alerte_model');
        $this->load->model('User_model');
        $this->load->library('email');
        $alerte_model = new Alerte_model();
        $user_model = new User_model();
        $alertes = $alerte_model->get_cron_alertes();

        $data = array();
        foreach ($alertes AS $item) {
            $data[$item->user][] = $item;
        }

        foreach ($data AS $uid => $array) {
            $i = 1;
            $user = $user_model->get_user_by_uid($uid);
            $this->email->from('contact@maged.lu', 'Gestionnaire d\'alerte PowerCRM');
            $this->email->to('' . $uid . ' <' . $user->email_adress . '>');
            $this->email->subject('Alerte email PowerCRM'); // pour l\'utilisateur ' . $uid);
            $message = '<ul>';
            foreach ($array AS $entity) {
                $message .= '<li><p>Alerte #' . $i . ': <br>Client: ' . strtoupper($entity->nom) . ' ' . ucfirst($entity->prenom) . '<br>Note: "' . strip_tags(trim($entity->note)) . '"<br>Date d\'alerte: ' . $entity->dt_real . '</p></li>';
                $i++;
            }
            $message .= '</ul>';
            echo $message;
            $this->email->message($message);
            $this->email->send();
        }
    }

    public function campagne() {
        /** GET ALL CAMPAIGN */
        $this->load->model('Request_model');
        $request_model = new Request_model();
        $campaigns = $request_model->get_active_sms_campaign();

        foreach ($campaigns AS $campaign) {
            $current_request = $request_model->get_request_by_id($campaign->request_id);
            $query = json_decode($current_request->data_posted);

            if (!empty($query)) {
                $table_selection = $query->table_selection;
                $table_field = $query->table_field;
                $table_condition = $query->table_condition;
                $table_value = $query->table_value;
                $table_operator = $query->table_operator;

                $table = '';
                $array_size = sizeof($table_selection);
                $result = $this->_launch_query($table_selection, $table_field, $table_condition, $table_value);
                for ($i = 0; $i < $array_size; $i++) {
                    $table = $table_selection[$i];
                    break;
                }

                $prepared = $this->_prepare_result($table, $result, $table_operator);
                $data = $this->_final_result($table, $prepared);

                $this->_send_campaign($data);
            }
        }
    }

    function _send_campaign($data) {
        $this->load->library('api_sms');

        $client_array = array();
        foreach ($data AS $entity) {
            if (isset($entity->client_id) && in_array($entity->client_id, $client_array)) {
                // do nothing
            } else {
                array_push($client_array, $entity->client_id);
                if (isset($entity->tel_mobile) && !empty($entity->tel_mobile)) {
                    $formatted_number = str_replace('+', '00', $entity->tel_mobile);
                    $api_sms = new Api_sms();
                    $api_sms->set_destination_number($formatted_number);

                    $str1 = str_replace('%NOM%', $entity->nom, $campaign->message);
                    $str2 = str_replace('%PRENOM%', $entity->prenom, $str1);

                    $api_sms->set_sms(strip_tags($str2));
                    $api_sms->set_date_envoi($campaign->sending_date);
                    $api_sms->send_sms();

                    $this->load->model('Sms_model');
                    $message_log = array(
                        'user' => $campaign->user,
                        "request_id" => $campaign->request_id,
                        'phone_number' => $formatted_number,
                        'message' => $str2,
                        'sending_date' => $campaign->sending_date,
                        'sending_type' => 'Campagne'
                    );
                    $sms_log_model = new Sms_model();
                    $sms_log_model->save_log($message_log);
                }
            }
        }
    }

    function _launch_query($table_selection, $table_field, $table_condition, $table_value) {
        $this->load->model('Client_model');
        $this->load->model('Rdv_model');

        $client_model = new Client_model();
        $rdv_model = new Rdv_model();

        $result = array();
        $table = '';
        $uid = $this->oc_auth->get_user_id();

        $array_size = sizeof($table_selection);
        if ($array_size <= 0) {
            return false;
        }

        for ($i = 0; $i < $array_size; $i++) {
            $table = $table_selection[$i];
            switch ($table_selection[$i]) {
                case 'client': {
                        $result[$i] = $client_model->test_client_query($uid, $table_field[$i], $table_condition[$i], $table_value[$i]);
                    }break;
                case 'rdv': {
                        $result[$i] = $rdv_model->test_rdv_query($uid, $table_field[$i], $table_condition[$i], $table_value[$i]);
                    }break;
                case 'prd_rdv': {
                        $result[$i] = $rdv_model->test_prd_rdv_query($uid, $table_field[$i], $table_condition[$i], $table_value[$i]);
                    }break;
            }
        }
        return $result;
    }

    function _prepare_result($table, $result, $operator) {
        $first_row = array();
        $size = sizeof($result);

        for ($i = 0; $i < $size; $i++) {
            $current = array();
            foreach ($result[$i] AS $item) { // row
                $key = $table . '_id';
                array_push($current, $item->$key);
            }
            if ($i > 0) {
                $op = $operator[$i];
                if ($op == 'or') {
                    $first_row = array_merge($current, $first_row);
                } else {
                    $first_row = array_intersect($current, $first_row);
                }
            } else {
                $first_row = $current;
            }
        }

        return array_unique($first_row);
    }

    function _final_result($table, $result) {
        $this->load->model('Client_model');
        $this->load->model('Rdv_model');

        $client_model = new Client_model();
        $rdv_model = new Rdv_model();

        $list = '';
        foreach ($result AS $item) {
            $list .= ',' . $item;
        }
        $ids = trim($list, ',');

        $return = array();

        if (strlen($ids) > 0) {
            switch ($table) {
                case 'client': {
                        $return = $client_model->get_client_by_ids($ids);
                    }break;
                case 'rdv': {
                        $return = $rdv_model->get_rdv_by_ids($ids);
                    }break;
                case 'prd_rdv': {
                        $return = $rdv_model->get_prd_rdv_by_ids($ids);
                    }break;
            }
        }

        return $return;
    }

}
