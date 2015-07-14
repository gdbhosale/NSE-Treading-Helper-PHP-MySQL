<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Indexo extends CI_Controller {
	
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
        $this->data['load_from'] = "indexo";

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
        $type = $this->input->get("type", TRUE);
   	if(isset($type) && $type != "") {
        	$this->data['cnt_type'] = $type;	
	} else {
        	$this->data['cnt_type'] = "B";
    	}
        $year = $this->input->get("year", TRUE);
   	if(isset($year) && $year != "") {
        	$this->data['cnt_year'] = $year;
	} else {
        	$this->data['cnt_year'] = date("Y");
    	}
    	$month = $this->input->get("month", TRUE);
   	if(isset($month) && $month != "") {
        	$this->data['cnt_month'] = $month;
	} else {
        	$this->data['cnt_month'] = date("m");
    	}
    	// get files
    	$datarows = array();
    	$files = glob($this->base_path."/data/idx_opt/".$this->data['cnt_year']."/".$this->data['cnt_month']."/*");
	foreach($files as $file) {
		if(is_file($file)) {
			if($this->data['cnt_type'] != "B") {
				if($this->data['cnt_type'] == "BN" && !startsWith(basename($file), "B")) {
					continue;
				}
				if($this->data['cnt_type'] == "N" && !startsWith(basename($file), "N")) {
					continue;
				}
			}
			$datarows[] = $file;
	        }
	}
    	$this->data['datarows'] = $datarows;
        $this->data['main_content'] = __FUNCTION__;
        $this->load->view('template', $this->data);
    }
    
    public function view() {
        $year = $this->input->get("year", TRUE);
        $month = $this->input->get("month", TRUE);
        $file = $this->input->get("file", TRUE);
	
	if(is_file($this->base_path."/data/idx_opt/".$year."/".$month."/".$file)) {
            $csvData = $this->csvreader->parse_file($this->base_path."/data/idx_opt/".$year."/".$month."/".$file);
            $this->data['csvData'] = $csvData;
            $this->data['file'] = $file;
            $this->data['year'] = $year;
            $this->data['month'] = $month;
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        } else {
            $this->data['showError'] = array('title'=> "Index option not found.",
                'message' => "Index option with file name ".$file." not present on server !!!");
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        }
    }

    public function export() {
        $year = $this->input->get("year", TRUE);
        $month = $this->input->get("month", TRUE);
        $file = $this->input->get("file", TRUE);
	
	if(is_file($this->base_path."/data/idx_opt/".$year."/".$month."/".$file)) {
            header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.$file);
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($this->base_path."/data/idx_opt/".$year."/".$month."/".$file));
	    readfile($this->base_path."/data/idx_opt/".$year."/".$month."/".$file);
	    exit;
        } else {
            $this->data['showError'] = array('title'=> "Index option not found.",
                'message' => "Index option with file name ".$file." not present on server !!!");
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        }
    }
}