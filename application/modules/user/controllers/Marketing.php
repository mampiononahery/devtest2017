<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . '/controllers/Back.php');

class Marketing extends Back {

    protected $_request_tables;
    protected $_request_conditions;

    function __construct() {
        parent::__construct();

        $this->_request_tables = array(
            array(
                "name" => "client",
                "filter_title" => "Un ou des clients en particulier ?",
                "primary" => "id_client",
                "fields" => array(
                    array(
                        "name" => "nom",
                        "display_as" => "Nom client",
                        "format" => "text",
                        "condition" => array(1)
                    ),
                    array(
                        "name" => "prenom",
                        "display_as" => "Pr&eacute;nom client",
                        "format" => "text",
                        "condition" => array(1, 2)
                    ),
                    array(
                        "name" => "cp",
                        "display_as" => "Code Postal",
                        "format" => "text",
                        "condition" => array(1)
                    ),
                    array(
                        "name" => "ville",
                        "display_as" => "Ville",
                        "format" => "text",
                        "condition" => array(1)
                    )
                )
            ),
            array(
                "name" => "rdv",
                "filter_title" => "Un ou plusieurs rendez-vous ?",
                "primary" => "id_rdv",
                "fields" => array(
                    array(
                        "name" => "nom_client",
                        "display_as" => "Nom client",
                        "format" => "text",
                        "condition" => array(1, 2)
                    ),
                    array(
                        "name" => "dt_start",
                        "display_as" => "Date Début RDV",
                        "format" => "YYYY-MM-DD",
                        "condition" => array(1, 2, 3)
                    ),
                    array(
                        "name" => "dt_end",
                        "display_as" => "Date Fin RDV",
                        "format" => "YYYY-MM-DD",
                        "condition" => array(1, 2, 3)
                    )
                )
            ),
            array(
                "name" => "prd_rdv",
                "filter_title" => "Un ou plusieurs produits rattach&eacute;s &agrave; un rendez-vous ?",
                "primary" => "prd_rdv_id",
                "join" => array("rdv", "client"),
                "fields" => array(
                    array(
                        "name" => "prod_label",
                        "display_as" => "Nom produit",
                        "format" => "text",
                        "condition" => array(1, 2)
                    ),
                    array(
                        "name" => "prod_pu",
                        "display_as" => "Prix unitaire",
                        "format" => "decimal",
                        "condition" => array(1, 2, 3)
                    ),
                    array(
                        "name" => "dt_start",
                        "display_as" => "Date Début RDV",
                        "format" => "YYYY-MM-DD",
                        "condition" => array(1, 2, 3)
                    ),
                    array(
                        "name" => "dt_end",
                        "display_as" => "Date Fin RDV",
                        "format" => "YYYY-MM-DD",
                        "condition" => array(1, 2, 3)
                    ),
                    array(
                        "name" => "nom ",
                        "display_as" => "Nom client",
                        "format" => "text",
                        "condition" => array(1)
                    ),
                    array(
                        "name" => "prenom",
                        "display_as" => "Pr&eacute;nom client",
                        "format" => "text",
                        "condition" => array(1, 2)
                    ),
                    array(
                        "name" => "cp",
                        "display_as" => "Code Postal",
                        "format" => "text",
                        "condition" => array(1)
                    ),
                    array(
                        "name" => "ville",
                        "display_as" => "Ville",
                        "format" => "text",
                        "condition" => array(1)
                    )
                )
            ),
            array(
                "name" => "client_objects",
                "filter_title" => "Chiens",
                "primary" => "object_id",
                "fields" => array(
                    array(
                        "name" => "1",
                        "display_as" => "Nom du chien",
                        "format" => "text",
                        "condition" => array(1, 2)
                    ),
                    array(
                        "name" => "2",
                        "display_as" => "Date de naissance",
                        "format" => "YYYY-MM-DD",
                        "condition" => array(1, 2, 3)
                    ),
                    array(
                        "name" => "3",
                        "display_as" => "Race",
                        "format" => "text",
                        "condition" => array(1, 2)
                    ),
                    array(
                        "name" => "4",
                        "display_as" => "V&eacute;t&eacute;rinaire",
                        "format" => "text",
                        "condition" => array(1, 2)
                    ),
                    array(
                        "name" => "5",
                        "display_as" => "Sport canin",
                        "format" => "text",
                        "condition" => array(1, 2)
                    )
                )
            )
        );

        $this->_request_conditions = array(
            array(
                "display_as" => "Contient",
                "condition" => "~"
            ),
            array(
                "display_as" => "Egale &agrave;",
                "condition" => "="
            ),
            array(
                "display_as" => "Sup&eacute;rieur ou &eacute;gale &agrave;",
                "condition" => ">="
            ),
            array(
                "display_as" => "Inf&eacute;rieur ou &eacute;gale &agrave;",
                "condition" => "<="
            )
        );

        if (!$this->_user->is_sms) {
            //redirect('user/dashboard/not_allowed');
        }
    }

     public function index() {
        $output = new stdClass();
        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        $layout = new Layout();
        $layout->set_title("Gestion campagne SMS");
        $layout->view("sections/marketing", $output, 'user_page');
    }

    public function sms() {
        $output = new stdClass();
        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        $this->load->model('Client_model');
        $client_model = new Client_model();
        $output->clients = $client_model->get_clients_by_uid($this->oc_auth->get_user_id());

        $layout = new Layout();
        $layout->set_title("Interface d'envoi de SMS");
        $layout->view("sections/marketing_sms", $output, 'user_page');
    }

    public function sms_pattern() {

        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('marketing_sms_pattern');
        $crud->set_relation('pattern_type', 'marketing_sms_pattern_type', 'title');
        $crud->set_subject('Mod&egrave;le SMS');
        $crud->columns('pattern_type', 'pattern');

        $crud->display_as(array(
            'pattern_type' => 'Type',
            'pattern' => 'Mod&egrave;le'
        ));

        $crud->field_type('user', 'hidden', $this->oc_auth->get_user_id());

        $crud->unset_read();
        $crud->unset_delete();
        $crud->unset_fields(array('last_update'));

        $output = $crud->render();

        $user_model = new User_model();
        $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
        if (!$logged_user->use_excel) {
            $crud->unset_export();
        }

        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        if ($this->input->post('submit')) {
            $pattern_type = $this->input->post('pattern-type');
            $pattern_pattern = $this->input->post('pattern-pattern');

            $pattern = array(
                'user' => $this->oc_auth->get_user_id(),
                'pattern_type' => $pattern_type,
                'pattern' => $pattern_pattern,
            );

            $this->load->model('Sms_model');
            $sms_model = new Sms_model();
            $action = 'add';

            $check_pattern = $sms_model->get_sms_pattern($this->oc_auth->get_user_id(), $pattern_type);

            if (isset($check_pattern)) {
                $action = 'update';
                $output->current_pattern = $check_pattern;
            }

            if ($action == 'add') {
                $sms_model->save_sms_pattern($pattern);
            } else {
                $sms_model->update_sms_pattern($pattern);
                $check_pattern = $sms_model->get_sms_pattern($this->oc_auth->get_user_id(), 'pattern_type');
                $output->current_pattern = $check_pattern;
            }
        }

        $layout = new Layout();
        $layout->set_title("Mod&egrave;le SMS");
        $layout->view("sections/marketing_sms_pattern", $output, 'user_page');
    }

    public function sms_cron_setting() {
        $output = new stdClass();
        $this->load->model('Sms_model');
        $sms_model = new Sms_model();
        $action = 'add';
        $check_setting = $sms_model->get_sms_notification_setting_by_user($this->oc_auth->get_user_id());
        if (isset($check_setting)) {
            $action = 'update';
            $output->current_setting = $check_setting;
        }
        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        if ($this->input->post('submit')) {
            $setting = array(
                'user' => $this->oc_auth->get_user_id(),
                'rdv_notification_start' => (int) $this->input->post('rdv_notification_start'),
                'birthday_notification_start' => (int) $this->input->post('birthday_notification_start')
            );
            if ($action == 'add') {
                $sms_model->save_sms_notification_setting($setting);
            } else {
                $sms_model->update_sms_notification_setting($setting);
                $check_setting = $sms_model->get_sms_notification_setting_by_user($this->oc_auth->get_user_id());
                $output->current_setting = $check_setting;
            }
        }

        $layout = new Layout();
        $layout->set_title("Param&eacute;trage CRON - Notification SMS");
        $layout->view("sections/marketing_sms_cron_setting", $output, 'user_page');
    }

    public function sms_rdv_setting() {
        $output = new stdClass();
        $this->load->model('Sms_model');
        $sms_model = new Sms_model();
        $action = 'add';

        $check_setting = $sms_model->get_sms_notification_setting_by_user($this->oc_auth->get_user_id());

        if (isset($check_setting)) {
            $action = 'update';
            $output->current_setting = $check_setting;
        }
        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        if ($this->input->post('submit')) {

            $active = $this->input->post('sms-notification');
            if ($active == 'on') {
                $message_notification_setting = array(
                    'user' => $this->oc_auth->get_user_id(),
                    'rdv_notification' => 1
                );
            } else {
                $message_notification_setting = array(
                    'user' => $this->oc_auth->get_user_id(),
                    'rdv_notification' => 0
                );
            }

            if ($action == 'add') {
                $sms_model->save_sms_notification_setting($message_notification_setting);
            } else {
                $sms_model->update_sms_notification_setting($message_notification_setting);
                $check_setting = $sms_model->get_sms_notification_setting_by_user($this->oc_auth->get_user_id());
                $output->current_setting = $check_setting;
            }
        }

        $layout = new Layout();
        $layout->set_title("Notification RDV par SMS");
        $layout->view("sections/marketing_sms_rdv_setting", $output, 'user_page');
    }

    public function request() {
        $output = new stdClass();
        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        $output->request_tables = json_encode($this->_request_tables);
        $output->request_conditions = json_encode($this->_request_conditions);

        $layout = new Layout();
        $layout->set_title("Outils de requ&ecirc;tage");
        $layout->view("sections/marketing_request", $output, 'user_page');
    }

    public function listing() {
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('marketing_request');
        $crud->set_subject('Requ&ecirc;te');
        $crud->columns('request_name', 'table_request');

        $crud->field_type('data_posted', 'text');
        $crud->field_type('user', 'hidden');
        $crud->unset_fields('data_posted');

        $crud->display_as(array(
            'table_request' => 'Table concern&eacute;e',
            'request_name' => 'Nom de la requ&ecirc;te',
            'data_posted' => 'Filtre JSON',
            'last_update' => 'Date cr&eacute;ation',
        ));

        $crud->unset_add();
        $crud->unset_edit();
        $state = $crud->getState();

        $output = $crud->render();

        if ($state == 'read') {
            $output->request_tables = $this->_request_tables;
            $output->request_conditions = $this->_request_conditions;

            $request_id = (int) $this->uri->segment(5);
            $this->load->model('Request_model');
            $request_model = new Request_model();
            if ($request_id > 0) {
                $output->request = $request_model->get_request_by_id($request_id);
            }
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        $layout = new Layout();
        $layout->set_title("Mes requ&ecirc;tes");
        $layout->view("sections/marketing_request_view", $output, 'user_page');
    }

    public function campagne() {
        $this->load->model('Request_model');
        $request_model = new Request_model();

        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('marketing_sms_campaign');
        $crud->set_relation('request_id', 'marketing_request', 'request_name');
        $crud->set_subject('Campagne');
        $crud->where('marketing_sms_campaign.user', $this->oc_auth->get_user_id());
        $crud->columns('request_id', 'name', 'message', 'sending_date');
        $crud->required_fields('request_id', 'name', 'message', 'sending_date');

        $crud->callback_before_insert(array($this, '_prepare_campaign'));
        $crud->callback_before_update(array($this, '_prepare_campaign'));

        $crud->display_as(array(
            'request_id' => 'Nom de la requ&ecirc;te',
            'user' => 'Utilisateur',
            'name' => 'Nom de la campagne',
            'message' => 'Message',
            'sending_date' => 'Date envoi'
        ));

        $crud->field_type('user', 'hidden', $this->oc_auth->get_user_id());
        $crud->unset_fields('last_update');

        $state = $crud->getState();

        $output = $crud->render();
        $output->state = $state;

        $output->requests = $request_model->get_request_by_uid($this->oc_auth->get_user_id());

        $this->load->model('Sms_model');
        $sms_model = new Sms_model();

        $output->pattern = $sms_model->get_sms_pattern($this->oc_auth->get_user_id(), "campaign");

        $layout = new Layout();
        $layout->set_title("Campagne SMS");
        $layout->view("sections/marketing_campagne", $output, 'user_page');
    }

    public function stats() {
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('marketing_sms_logs');
        $crud->set_relation('rdv_id', 'rdv', '{nom_client} {dt_start}');
        $crud->set_relation('request_id', 'marketing_request', 'request_name');
        $crud->columns('sending_type', 'total_sent', 'total_error', 'phone_number', 'message', 'sending_date');
        $crud->set_subject('Statistiques');

        $crud->display_as(array(
            'rdv_id' => 'Rendez-vous',
            'sending_type' => 'Type d\'envoi',
            'total_sent' => 'Qt&eacute;s envoy&eacute;s',
            'total_error' => 'Qt&eacute;s en erreur',
            'phone_number' => 'N. t&eacute;l&eacute;phone',
            'sending_date' => 'Date d\'envoi'
        ));

        $crud->unset_add();
        $crud->unset_edit();
        $crud->unset_delete();

        $output = $crud->render();

        $layout = new Layout();
        $layout->set_title("Statistiques");
        $layout->view("sections/marketing_stats", $output, 'user_page');
    }

    function _prepare_campaign($post_array) {
        $sending_date = $post_array['sending_date'];
        $request_id = $post_array['request_id'];
        $campaign_message = $post_array['message'];
        $campaign_user = $post_array['user'];
        
        $campaign = new stdClass();
        $campaign->request_id = $request_id;
        $campaign->message = $campaign_message;
        $campaign->user = $campaign_user;
        $campaign->sending_date = $sending_date;
        
        $campaign_date = str_replace('/', '-', $sending_date);
        // IF DATE IS EQUAL TO TODAY'S DATE - SEND IT DIRECTLY AND SAVE 
        $date1 = date('Y-m-d', strtotime($campaign_date));
        $date2 = date('Y-m-d');
        if ($date1 == $date2) {
            // GET IDS
            $this->load->model('Request_model');
            $request_model = new Request_model();
            $request = $request_model->get_request_by_id($request_id);
            $posted = json_decode($request->data_posted);

            $table_selection = $posted->table_selection;
            $table_field = $posted->table_field;
            $table_condition = $posted->table_condition;
            $table_value = $posted->table_value;
            $table_operator = $posted->table_operator;

            $table = '';
            $array_size = sizeof($table_selection);
            $result = $this->_launch_query($table_selection, $table_field, $table_condition, $table_value);
            for ($i = 0; $i < $array_size; $i++) {
                $table = $table_selection[$i];
                break;
            }

            $prepared = $this->_prepare_result($table, $result, $table_operator);
            $data = $this->_final_result($table, $prepared);

            $this->_send_campaign($data, $campaign);
        }
    }

}
