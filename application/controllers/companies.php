<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Companies extends CI_Controller {
	
    var $base_url;
    var $base_path;
    var $stock_files;
    var $opt_files;
    var $data;
    var $dbCom;
    
    public function __construct() {
        parent::__construct();
        
        $this->base_url = $this->config->item("base_url");
		$this->base_path = $this->config->item("base_path");
		$this->stock_files = $this->config->item("stock_files");
        $this->opt_files = $this->config->item("opt_files");
        $this->data = array();
        $this->data['base_url'] = $this->base_url;
        $this->data['load_from'] = "companies";

        $this->db = $this->load->database('default', TRUE);
        $config['hostname'] = "localhost";
        $config['username'] = "rupeemax_user1";
        $config['password'] = $this->db->password;
        $config['database'] = "rupeemax_tread_companies";
        $config['dbdriver'] = "mysqli";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";
        $this->dbCom = $this->load->database($config, TRUE);
            
        $this->load->library('csvreader');
    }
    
    public function index() {
        $query = $this->db->get('companies');
        if($query->num_rows() > 0) {
            $companies = array();
            foreach ($query->result_array() as $row) {
                $this->dbCom->select("id");
                $q = $this->dbCom->get($row['table']);
                $row['data_days'] = $q->num_rows();
                $companies[] = $row;
            }
            $this->data['companies'] = $companies;

            // get success days records
            $this->db->where("status", "SUCCESS");
            $query = $this->db->get('date_reports');
            $this->data['load_success'] = $query->num_rows();

            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        } else {
            $this->data['showError'] = array('title'=> "Company Data",
                'message' => "Company data not present in Database !!!
                    <br><br>Need initial setup. Click <a href='".$this->base_url."/home/init_setup'>here</a> to proceed. It will take around 4-6 Minutes depands on connection &amp; computer speed.");
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        }
    }

    public function view() {
        $com_id = $this->input->get("com_id", TRUE);

        $this->db->where("id", $com_id);
        $query = $this->db->get('companies');
        if($query->num_rows() > 0) {
            $company = $query->row_array();

            $this->dbCom->order_by("date", "ASC");
            $q = $this->dbCom->get($company['table']);
            $data_days = $q->result_array();
            
            // get success days records
            $this->db->where("status", "SUCCESS");
            $query = $this->db->get('date_reports');
            $load_success = $query->num_rows();

            $this->data['company'] = $company;
            $this->data['data_days'] = $data_days;
            $this->data['load_success'] = $load_success;

            //print_r($company);
            //echo "<br><br><br>";
            //print_r($data_days);

            // header('Content-Type: text/csv; charset=utf-8');
            // header('Content-Disposition: attachment; filename=COM_'.$company['table'].'_'.date("dmy").'.csv');
            // $output = fopen('php://output', 'w');
            // fputcsv($output, array('CNT_DATE', 'OPEN_PRICE', 'HIGH_PRICE', 'LOW_PRICE', 'CLOSE_PRICE'));
            // foreach ($data_days as $row) {
            //     fputcsv($output, array($row['date'], $row['open_price'], $row['high_price'], $row['low_price'], $row['close_price']));
            // }
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        } else {
            $this->data['showError'] = array('title'=> "Company not found.",
                'message' => "Company with id ".$com_id." not present in Database !!!");
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        }
    }

    public function export() {
        $com_id = $this->input->get("com_id", TRUE);

        $this->db->where("id", $com_id);
        $query = $this->db->get('companies');
        if($query->num_rows() > 0) {
            $company = $query->row_array();

            $this->dbCom->order_by("date", "ASC");
            $q = $this->dbCom->get($company['table']);
            $data_days = $q->result_array();
            
            // get success days records
            $this->db->where("status", "SUCCESS");
            $query = $this->db->get('date_reports');
            $load_success = $query->num_rows();

            //print_r($company);
            //echo "<br><br><br>";
            //print_r($data_days);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=COM_'.$company['table'].'_'.date("dmy").'.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('CNT_DATE', 'OPEN_PRICE', 'HIGH_PRICE', 'LOW_PRICE', 'CLOSE_PRICE'));
            foreach ($data_days as $row) {
                fputcsv($output, array($row['date'], $row['open_price'], $row['high_price'], $row['low_price'], $row['close_price']));
            }
            exit;
        } else {
            $this->data['showError'] = array('title'=> "Company not found.",
                'message' => "Company with id ".$com_id." not present in Database !!!");
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        }
    }
}