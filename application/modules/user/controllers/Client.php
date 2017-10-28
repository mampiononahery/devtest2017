<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . '/controllers/Back.php');

class Client extends Back {

    public function index() {

        $crud = new grocery_CRUD();

        //$crud->set_theme('datatables');
		  $crud->set_theme('flexigrid');

        $crud->set_table('client');
        $crud->set_subject('client');
        $crud->where('user', $this->oc_auth->get_user_id());
        $crud->add_action('Alerte   ', '', 'user/client/alerte', 'fa fa-bell-o');
        $crud->columns('genre', 'nom', 'prenom', 'pays', 'adresse', 'tel_mobile', 'cp', 'ville', 'email');
        $crud->field_type('user', 'hidden', $this->oc_auth->get_user_id());
        $crud->set_rules('tel_mobile', 'Num. portable', 'phone_number');
        $crud->required_fields('nom', 'prenom', 'tel_mobile');

        $user_model = new User_model();
        $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
        if (!$logged_user->use_excel) {
            $crud->unset_export();
        }

        $state = $crud->getState();
        $fields = array('user', 'genre', 'nom', 'prenom', 'email', 'dt_nais', 'pays', 'adresse', 'cp', 'ville', 'tel_fixe', 'tel_mobile', 'sms_versaire', 'sms_object_versaire','dynamic_fields');

        $crud->callback_field('dynamic_fields', array($this, 'callback_load_dynamic_fields'));
        $crud->callback_read_field('dynamic_fields', array($this, 'callback_load_dynamic_fields'));
        $crud->callback_before_insert(array($this, 'callback_format_dynamic_fields'));
        $crud->callback_before_update(array($this, 'callback_format_dynamic_fields'));
        $crud->callback_column('nom', array($this, 'callback_format_firstname_fields'));
        $crud->callback_column('prenom', array($this, 'callback_format_lastname_fields'));
        $crud->fields($fields);

        $field_labels = array(
            'user' => 'Nom',
            'genre' => 'Titre',
            'prenom' => 'Pr&eacute;nom',
            'cp' => 'Code postal',
            'dt_nais' => 'Date de naissance',
            'tel_fixe' => 'T&eacute;l&eacute;phone fixe',
            'tel_mobile' => 'Num. portable',
            'email' => 'Adresse email',
            'sms_versaire' => 'Envoi de SMS marketing &agrave; la date anniversaire du clien ',
            'sms_object_versaire' => 'Envoi de SMS marketing &agrave; la date anniversaire du chien ',
            'dynamic_fields' => 'Informations suppl&eacute;mentaires'
        );

        $crud->display_as($field_labels);

        $output = $crud->render();

        $client_id = (int) $this->uri->segment(5);
        if ($client_id > 0) {
            $this->load->model('Client_model');
            $client_model = new Client_model();
            $output->client = $client_model->get_client_by_id($client_id);
            $this->load->helper('date');
            if (is_date_close($output->client->dt_nais)) {
                $output->birthday_is_closed = true;
            }
        }

        if ($state == 'read') {
            $output->action_read = true;
            $this->load->model('Rdv_model');
            $rdv_model = new Rdv_model();
            $prd_rdv = $rdv_model->get_prd_rdv_by_client_id($client_id);
            $output->order_stories = $prd_rdv;

        }

        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }

        if (isset($this->_alertes) && !empty($this->_alertes)) {
            $output->alertes = $this->_alertes;
        }
        if (isset($this->_user) && !empty($this->_user)) {
            $output->user = $this->_user;
        }
        /** FIN - GESTION CHIENS */
        $layout = new Layout();
        $layout->set_title("Clients");
		
		
        $layout->view("sections/client", $output, 'user_page');
    }

    public function alerte() {
        $client_id = (int) $this->uri->segment(4);

        if ($client_id > 0) {
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('alerte');
            $crud->set_subject('Alerte');
            $crud->required_fields('client_id', 'user', 'note', 'dt_real');
            $crud->where("alerte.user = \"" . $this->oc_auth->get_user_id() . "\" AND alerte.client_id = " . $client_id);
            $crud->columns('dt_real', 'note', 'acquitted');
            $crud->add_action('Voir Alerte', '', 'user/alerte/index/read', 'read-icon');
            $crud->add_action('Acquitter', '', 'user/alerte/close_alerte', 'fa fa-check-square-o');

            $crud->unset_read();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_fields('email_send');

            $state = $crud->getState();

            $crud->display_as(array(
                'client_id' => 'Client',
                'dt_real' => 'Date',
                'acquitted' => 'Alerte acquitt&eacute;e',
                'email_send' => 'Email envoy&eacute;'
            ));

            $crud->field_type('client_id', 'hidden', $client_id);
            $crud->field_type('user', 'hidden', $this->oc_auth->get_user_id());

            $user_model = new User_model();
            $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
            if (!$logged_user->use_excel) {
                $crud->unset_export();
            }

            $crud->unset_edit();
            $output = $crud->render();

            $output->state = $state;

            $this->load->model('Client_model');
            $client_model = new Client_model();

            $output->client = $client_model->get_client_by_id($client_id);

            if (isset($this->_alertes) && !empty($this->_alertes)) {
                $output->alertes = $this->_alertes;
            }
            if (isset($this->_user) && !empty($this->_user)) {
                $output->user = $this->_user;
            }

            $this->load->helper('date');
            if (is_date_close($output->client->dt_nais)) {
                $output->birthday_is_closed = true;
            }

            $layout = new Layout();
            $layout->set_title("Alertes clients");
            $layout->view("sections/client_alerte", $output, 'user_page');
        } else {
            redirect('user/dashboard');
        }
    }

    public function latest_alerte() {
        $client_id = (int) $this->uri->segment(4);

        if ($client_id > 0) {
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('alerte');
            $crud->set_subject('Alerte');
            $crud->required_fields('client_id', 'user', 'note', 'dt_real');
            $crud->where("alerte.user = \"" . $this->oc_auth->get_user_id() . "\" AND acquitted = 0 AND dt_real <= now() AND alerte.client_id = " . $client_id);
            $crud->columns('dt_real', 'note', 'acquitted');
            $crud->add_action('Voir Alerte', '', 'user/alerte/index/read', 'read-icon');
            $crud->add_action('Acquitter', '', 'user/alerte/close_alerte', 'fa fa-check-square-o');

            $crud->unset_read();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_fields('email_send');

            $state = $crud->getState();

            $crud->display_as(array(
                'client_id' => 'Client',
                'dt_real' => 'Date',
                'acquitted' => 'Alerte acquitt&eacute;e',
                'email_send' => 'Email envoy&eacute;'
            ));

            $crud->field_type('client_id', 'hidden', $client_id);
            $crud->field_type('user', 'hidden', $this->oc_auth->get_user_id());

            $user_model = new User_model();
            $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
            if (!$logged_user->use_excel) {
                $crud->unset_export();
            }

            $crud->unset_edit();
            $output = $crud->render();

            $output->state = $state;
            $output->latest_alert = 1;

            $this->load->model('Client_model');
            $client_model = new Client_model();

            $output->client = $client_model->get_client_by_id($client_id);

            if (isset($this->_alertes) && !empty($this->_alertes)) {
                $output->alertes = $this->_alertes;
            }
            if (isset($this->_user) && !empty($this->_user)) {
                $output->user = $this->_user;
            }

            $this->load->helper('date');
            if (is_date_close($output->client->dt_nais)) {
                $output->birthday_is_closed = true;
            }

            $layout = new Layout();
            $layout->set_title("Alertes clients");
            $layout->view("sections/client_alerte", $output, 'user_page');
        } else {
            redirect('user/dashboard');
        }
    }

    public function data() {
        $client_id = (int) $this->uri->segment(4);

        if ($client_id > 0) {
            $user_model = new User_model();
            $user_doc_allowed_id = $user_model->get_user_doc_by_uid($this->oc_auth->get_user_id());
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('doc_client');
            $crud->set_subject('Document client');
            if (!empty($user_doc_allowed_id)) {
                $crud->set_relation('type_doc', 'type_doc', 'nom_type', 'typ_doc_id IN (' . $user_doc_allowed_id . ')');
            }
            $crud->required_fields('id_client', 'type_doc', 'document', 'dt_crea');
            $crud->where("id_client = " . $client_id);
            $crud->columns('type_doc', 'commentaire', 'document', 'dt_crea');

            $crud->set_field_upload('document', 'assets/uploads/data/' . $this->oc_auth->get_user_id() . '/files/');

            $crud->display_as(array(
                'type_doc' => 'Type de document',
                'id_client' => 'Client',
                'dt_crea' => 'Date cr&eacute;ation',
                'email_send' => 'Email envoy&eacute;'
            ));

            $crud->field_type('id_client', 'hidden', $client_id);

            $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
            if (!$logged_user->use_excel) {
                $crud->unset_export();
            }

            $output = $crud->render();

            $this->load->model('Client_model');
            $client_model = new Client_model();

            $output->client = $client_model->get_client_by_id($client_id);

            $this->load->helper('date');
            if (is_date_close($output->client->dt_nais)) {
                $output->birthday_is_closed = true;
            }

            $layout = new Layout();
            $layout->set_title("Documents clients");
            $layout->view("sections/client_data", $output, 'user_page');
        } else {
            redirect('user/dashboard');
        }
    }

    public function contract() {
        $client_id = (int) $this->uri->segment(4);

        if ($client_id > 0) {
            $user_model = new User_model();
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('doc_contrat_clt');
            $crud->set_subject('Contrat client');
            $crud->required_fields('id_client', 'type_contrat', 'dt_fin_contrat', 'date_echeance', 'date_effet', 'cotisation', 'compagnie', 'lien_compagnie', 'document');
            $crud->where("id_client = " . $client_id);
            $crud->columns('type_contrat', 'dt_fin_contrat', 'date_echeance', 'date_effet', 'cotisation', 'compagnie', 'lien_compagnie', 'document');

            $crud->set_field_upload('document', 'assets/uploads/data/' . $this->oc_auth->get_user_id() . '/contracts/');

            $crud->display_as(array(
                'type_contrat' => 'Type de contrat',
                'date_echeance' => 'Date d\'&eacute;ch&eacute;ance',
                'dt_fin_contrat' => 'Date fin du contrat',
                'date_effet' => 'Date d\'effet du contrat',
                'email_send' => 'Email envoy&eacute;'
            ));

            $crud->field_type('id_client', 'hidden', $client_id);

            $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
            if (!$logged_user->use_excel) {
                $crud->unset_export();
            }

            $output = $crud->render();

            $this->load->model('Client_model');
            $client_model = new Client_model();

            $output->client = $client_model->get_client_by_id($client_id);

            if (isset($this->_alertes) && !empty($this->_alertes)) {
                $output->alertes = $this->_alertes;
            }
            if (isset($this->_user) && !empty($this->_user)) {
                $output->user = $this->_user;
            }
            $this->load->helper('date');
            if (is_date_close($output->client->dt_nais)) {
                $output->birthday_is_closed = true;
            }

            $layout = new Layout();
            $layout->set_title("Contrats clients");
            $layout->view("sections/client_contract", $output, 'user_page');
        } else {
            redirect('user/dashboard');
        }
    }

    public function rdv() {
        $client_id = (int) $this->uri->segment(4);

        if ($client_id > 0) {
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('rdv');
            $crud->set_subject('Rendez-vous clients');
            $crud->set_relation('id_ressource', 'resource', 'name');
            $crud->required_fields('note_prd', 'nom_client', 'id_ressource', 'dt_start', 'dt_end', 'rdv_etat', 'sms', 'clt_non_pointe');
            $crud->where("id_client = " . $client_id);
            $crud->columns('note_prd', 'id_ressource', 'dt_start', 'dt_end', 'rdv_etat', 'sms', 'clt_non_pointe');

            $crud->display_as(array(
                'note_prd' => 'Note sur le produit',
                'id_ressource' => 'Assign&eacute; &agrave;',
                'dt_start' => 'D&eacute;but du RDV',
                'dt_end' => 'Fin du RDV',
                'rdv_etat' => 'Etat du RDV',
                'sms' => 'Notifi&eacute; par SMS',
                'clt_non_pointe' => 'Point&eacute;'
            ));

            $crud->field_type('id_client', 'hidden', $client_id);
            $crud->field_type('user', 'hidden', $this->oc_auth->get_user_id());

            $user_model = new User_model();
            $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
            if (!$logged_user->use_excel) {
                $crud->unset_export();
            }

            $crud->unset_edit();
            $output = $crud->render();

            $this->load->model('Client_model');
            $client_model = new Client_model();

            $output->client = $client_model->get_client_by_id($client_id);

            $this->load->helper('date');
            if (is_date_close($output->client->dt_nais)) {
                $output->birthday_is_closed = true;
            }

            if (isset($this->_alertes) && !empty($this->_alertes)) {
                $output->alertes = $this->_alertes;
            }
            if (isset($this->_user) && !empty($this->_user)) {
                $output->user = $this->_user;
            }

            $layout = new Layout();
            $layout->set_title("RDV clients");
            $layout->view("sections/client_rdv", $output, 'user_page');
        } else {
            redirect('user/dashboard');
        }
    }

    public function notice() {
        $client_id = (int) $this->uri->segment(4);

        if ($client_id > 0) {
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('note_client');
            $crud->set_subject('Note client');
            $crud->required_fields('note_id', 'client_id', 'note', 'dt_note');
            $crud->where("client_id = " . $client_id);
            $crud->columns('note', 'dt_note');

            $crud->display_as(array(
                'client_id' => 'Client',
                'dt_note' => 'Date note',
                'note' => 'Note client'
            ));

            $crud->field_type('client_id', 'hidden', $client_id);
            $crud->field_type('dt_note', 'hidden', date('Y-m-d h:i:s'));
            $crud->field_type('user', 'hidden', $this->oc_auth->get_user_id());

            $user_model = new User_model();
            $logged_user = $user_model->get_user_by_uid($this->oc_auth->get_user_id());
            if (!$logged_user->use_excel) {
                $crud->unset_export();
            }

            $crud->unset_edit();
            $output = $crud->render();

            $this->load->model('Client_model');
            $client_model = new Client_model();

            $output->client = $client_model->get_client_by_id($client_id);

            if (isset($this->_alertes) && !empty($this->_alertes)) {
                $output->alertes = $this->_alertes;
            }
            if (isset($this->_user) && !empty($this->_user)) {
                $output->user = $this->_user;
            }

            $this->load->helper('date');
            if (is_date_close($output->client->dt_nais)) {
                $output->birthday_is_closed = true;
            }

            $layout = new Layout();
            $layout->set_title("Notes clients");
            $layout->view("sections/client_notice", $output, 'user_page');
        } else {
            redirect('user/dashboard');
        }
    }

    function sms_activation() {
        $client_id = (int) $this->uri->segment(4);
        $this->load->model('Client_model');
        $client_model = new Client_model();
        $client_model->update_messaging_status($client_id, 1);
        redirect('user/client/index/read/' . $client_id);
    }

    function sms_desactivation() {
        $client_id = (int) $this->uri->segment(4);
        $this->load->model('Client_model');
        $client_model = new Client_model();
        $client_model->update_messaging_status($client_id, 0);
        redirect('user/client/index/read/' . $client_id);
    }

    function export_all() {
        $data = new stdClass();
        $data->columns = $this->_get_export_columns();
        $data->list = $this->_get_export_list($data->columns);
        $this->_export_data($data);
    }

    protected function _export_data($data) {
        /**
         * No need to use an external library here. The only bad thing without using external library is that Microsoft Excel is complaining
         * that the file is in a different format than specified by the file extension. If you press "Yes" everything will be just fine.
         * */
        $string_to_export = "";
        foreach ($data->columns as $column) {
            $string_to_export .= $column->display_as . "\t";
        }
        $string_to_export .= "\n";

        foreach ($data->list as $num_row => $row) {
            foreach ($data->columns as $column) {
                if (isset($row->{$column->field_name})) {
                    $string_to_export .= $this->_trim_export_string($row->{$column->field_name}) . "\t";
                }
            }
            $string_to_export .= "\n";
        }

        // Convert to UTF-16LE and Prepend BOM
        $string_to_export = "\xFF\xFE" . mb_convert_encoding($string_to_export, 'UTF-16LE', 'UTF-8');

        $filename = "export-" . date("Y-m-d_H:i:s") . ".xls";

        header('Content-type: application/vnd.ms-excel;charset=UTF-16LE');
        header('Content-Disposition: attachment; filename=' . $filename);
        header("Cache-Control: no-cache");
        echo $string_to_export;
        die();
    }

    protected function _trim_export_string($value) {
        $string = str_replace(array("&nbsp;", "&amp;", "&gt;", "&lt;"), array(" ", "&", ">", "<"), $value);
        return strip_tags(str_replace(array("\t", "\n", "\r"), "", $string));
    }

    protected function _get_export_columns() {

        $columns = array();
        $field_name = array('genre', 'nom', 'prenom', 'dt_nais', 'pays', 'adresse', 'cp', 'ville', 'tel_fixe', 'tel_mobile', 'email', 'sms_versaire');
        $display_as = array('Genre', 'Nom', 'Prénom', 'Date naissance', 'Pays', 'Adresse', 'Code postal', 'Ville', 'Téléphone fixe', 'Portable', 'Email', 'Envois SMS');

        $this->load->model('Client_model');
        $client_model = new Client_model();

        $client_fields = $client_model->get_client_fields_by_user($this->oc_auth->get_user_id());
        foreach ($client_fields AS $field) {
            array_push($field_name, $field->field_id);
            array_push($display_as, "\"" . $field->label . "\"");
        }

        $total_columns = sizeof($field_name);
        for ($i = 0; $i < $total_columns; $i++) {
            $field = new stdClass();
            $field->field_name = $field_name[$i];
            $field->display_as = $display_as[$i];
            $columns[] = $field;
        }
        return $columns;
    }

    protected function _get_export_list($columns) {
        $this->load->model('Client_model');
        $client_model = new Client_model();
        $lists = array();

        $clients = $client_model->get_clients_by_uid($this->oc_auth->get_user_id());
        foreach ($clients AS $item) {
            if ($item->dynamic_fields && !empty($item->dynamic_fields)) {
                $dynamic_fields = json_decode($item->dynamic_fields, true);
                foreach ($dynamic_fields AS $key => $value) {
                    $item->$key = $this->_get_client_field_value($key, $value);
                }
            }
            unset($item->dynamic_fields);
            $lists[] = $item;
        }

        return $lists;
    }

    protected function _get_client_field_value($field_id, $value) {
        $this->load->model('Client_model');
        $client_model = new Client_model();
        $field = $client_model->get_client_field_by_field_id($field_id);
        if ($field->field_type == 'dropdown') {
            $option_data = $client_model->get_client_field_option_value($value);
            $option = (isset($option_data->option_value) && !empty($option_data->option_value)) ? $option_data->option_value : null;
        } else {
            $option = $value;
        }
        return $option;
    }

    function callback_load_dynamic_fields($post_array, $primary_key) {
        $this->load->model('Client_model');
        $client_model = new Client_model();
        $client_fields = $client_model->get_client_fields_by_user($this->oc_auth->get_user_id());

        $input = '';
        $counter = 0;
        $dynamic_fields = array();
        $client_id = (int) $this->uri->segment(5);

        if ($client_id > 0) {
            $client = $client_model->get_client_by_id($client_id);
            $dynamic_fields = json_decode($client->dynamic_fields, true);
        }

        foreach ($client_fields AS $item) {
            $row_style = ($counter % 2 == 0) ? ' odd' : ' even';
            if (!empty($item->label)) {
                $input .= '<div class="form-field-box' . $row_style . '">';
                if ($item->field_type == 'boolean') {
                    $input .= '<div class="form-display-as-box">' . $item->label . '</div>';
                    $input .= '<div class="form-input-box">';
                    if ((isset($dynamic_fields[$item->field_id]) && intval($dynamic_fields[$item->field_id]) == 1)) {
                        $checked = ' checked="checked"';
                    } else {
                        $checked = '';
                    }
                    $input .= '    <div class="switch">';
                    $input .= '        <input class="switch-input" id="field_' . $item->field_id . '_true" name="field_' . $item->field_id . '" type="radio" value="1"' . $checked . '/>';
                    $input .= '        <label for="field_' . $item->field_id . '_true" class="switch-label switch-label-off">oui</label>';
                    if ((isset($dynamic_fields[$item->field_id]) && intval($dynamic_fields[$item->field_id]) == 0)) {
                        $checked = ' checked="checked"';
                    } else {
                        $checked = '';
                    }
                    $input .= '        <input class="switch-input" id="field_' . $item->field_id . '_false" name="field_' . $item->field_id . '" type="radio" value="0"' . $checked . '/>';
                    $input .= '        <label for="field_' . $item->field_id . '_true" class="switch-label switch-label-on">non</label>';
                    $input .= '        <span class="switch-selection"></span>';
                    $input .= '    </div>';
                    $input .= '</div>';
                    $input .= '<div class="clear"></div>';
                } elseif ($item->field_type == 'dropdown') {
                    $input .= '<div>';
                    $input .= '   <label for="field_' . $item->field_id . '">' . $item->label . '</label><select name="field_' . $item->field_id . '" id="field_' . $item->field_id . '">';
                    $field_options = $client_model->get_client_field_options($item->field_id);
                    $dropdown = array();
                    $input .= '       <option value="">-- choisir --</option>';
                    foreach ($field_options AS $option) {
                        if (isset($dynamic_fields[$item->field_id]) && $dynamic_fields[$item->field_id] == $option->option_id) {
                            $selected = ' selected="selected"';
                        } else {
                            $selected = '';
                        }
                        $dropdown[$option->option_id] = $option->option_value;
                        $input .= '   <option value="' . $option->option_id . '"' . $selected . '>' . $option->option_value . '</option>';
                    }
                    $input .= '    </select>';
                    $input .= '</div>';
                } else {
                    if ((isset($dynamic_fields[$item->field_id]))) {
                        $value = $dynamic_fields[$item->field_id];
                    } else {
                        $value = '';
                    }
                    $input .= '<div><label for="field_' . $item->field_id . '">' . $item->label . '</label><input id="field_' . $item->field_id . '" name="field_' . $item->field_id . '" type="text" value="' . $value . '"/></div>';
                }
                $input .= '</div>';
            }
            $counter++;
        }
        return $input;
    }

    function callback_format_dynamic_fields($post_array) {
        $this->load->model('Client_model');
        $client_model = new Client_model();
        $dynamic_fields = array();

        $client_fields = $client_model->get_client_fields_by_user($this->oc_auth->get_user_id());

        foreach ($client_fields AS $item) {
            $dynamic_fields[$item->field_id] = $post_array['field_' . $item->field_id];
            unset($post_array['field_' . $item->field_id]);
        }

        $post_array['dynamic_fields'] = json_encode($dynamic_fields);

        $post_array['nom'] = strtoupper($post_array['nom']);
        $post_array['prenom'] = ucwords($post_array['prenom']);

        return $post_array;
    }

    function callback_validate_phone_number($post_array) {
        return $post_array;
    }

    function callback_format_fields($field) {
        return ucwords($field);
    }

    function callback_format_firstname_fields($field) {
        return strtoupper($field);
    }

    function callback_format_lastname_fields($field) {
        return ucwords($field);
    }

}
