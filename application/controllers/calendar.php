<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar extends CI_Controller {
	
    var $base_url;
    var $base_path;
    var $stock_files;
    var $data;
    var $dbCom;
    
    public function __construct() {
        parent::__construct();
        
        $this->base_url = $this->config->item("base_url");
		$this->base_path = $this->config->item("base_path");
		$this->stock_files = $this->config->item("stock_files");
        $this->data = array();
        $this->data['base_url'] = $this->base_url;
        $this->data['load_from'] = "calendar";

        $this->db = $this->load->database('default', TRUE);
        $config['hostname'] = "localhost";
        $config['username'] = "root";
        $config['password'] = "root";
        $config['database'] = "nse_tread_companies";
        $config['dbdriver'] = "mysql";
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
            $this->data['cnt_year'] = 2015;
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        } else {
            $this->data['showError'] = array('title'=> "Company Data",
                'message' => "Company data not present in Database !!!
                    <br><br>Need initial setup. Click <a href='".$this->base_url."/home/init_setup'>here</a> to proceed.");
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        }
    }

    public function update_db_datewise() {
        $date = $this->input->get("date", TRUE);
        $query = $this->db->get('companies');
        if($query->num_rows() > 0) {
            $this->data['date'] = $date;

            // 26-02-15
            $this->loadDaywiseReport($date);

            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        } else {
            $this->data['showError'] = array('title'=> "Company Data", 'message' => "Company data not present in Database !!!");
            $this->data['main_content'] = __FUNCTION__;
            $this->load->view('template', $this->data);
        }
    }

    function loadDaywiseReport($day) {
        $this->data['message'] = "Load Data for date : (".$day.")<br>";
        $this->data['error'] = "";

        $arr = explode("-", $day);
        $day = $arr[0];
        $month = $arr[1];
        $year = $arr[2];

        $filename = "PR".$day.$month.$year.".zip";
        
        // do we have file ?
        $fileExists = false;
        if(file_exists($this->base_path . "/stock_files/".$filename) && filesize($this->base_path . "/stock_files/".$filename) > 1000) {
            $fileExists = true;
        }
        if(!$fileExists) {
            $path = "http://www.nseindia.com/archives/equities/bhavcopy/pr/".$filename;

            $zipResource = fopen($this->base_path . "/stock_files/".$filename, "w");

            $arr1 = downloadFile($path, $zipResource);
            $page = $arr1['page'];

            if(!$page) {
                $this->data['error'] .= "File failed: ".$path." : ".$arr1['curl_error_str']."\n<br>";
                log_message("error", "File failed: ".$path." : ".$arr1['curl_error_str']);
                return 0;
            } else {
                $this->data['message'] .= "Got file: ".$path."\n<br>";
                log_message("debug", "Got file: ".$path." : ".$arr1['curl_error_str']);
            }
        }
        emptyFolder($this->base_path."/stock_files/temp");
        $this->data['error'] .= unzipFile($this->base_path . "/stock_files/".$filename, $this->base_path."/stock_files/temp");

        // load daily company data to their db
        $csvData = $this->csvreader->parse_file($this->base_path."/stock_files/temp/Pd".$day.$month.$year.".csv");

        $comUpdated = 0;
        $comLateUpdated = 0;
        foreach($csvData as $field) {
            //$this->data['message'] .= "".$field['SYMBOL']."<br>";
            if($field['SERIES'] == "EQ") {
                $tableName = processSymbol($field['SYMBOL']);

                // check if value already exists
                if ($this->dbCom->table_exists($tableName)) {

                    // check if date values already exists
                    $this->dbCom->where("date", "20".$year."-".$month."-".$day);
                    $q = $this->dbCom->get($tableName);
                    if($q->num_rows() == 0) {
                        $da = array(
                            'date' => "20".$year."-".$month."-".$day,
                            'open_price' => $field['OPEN_PRICE'],
                            'high_price' => $field['HIGH_PRICE'],
                            'low_price' => $field['LOW_PRICE'],
                            'close_price' => $field['CLOSE_PRICE']
                        );
                        //echo json_encode($da);
                        $this->dbCom->insert($tableName, $da);
                    }
                    $comUpdated = $comUpdated + 1;
                } else {
                    if($tableName != "") {
                        $this->data['message'] .= "Table not exists: (".$field['SYMBOL']."-".$tableName.")<br>";
                        $da = array(
                            'symbol' => $field['SYMBOL'],
                            'table' => $tableName,
                            'isin' => ""
                        );
                        $this->db->insert('companies', $da);
                        
                        $this->dbCom->query("CREATE TABLE IF NOT EXISTS ".$tableName." (
                            id int(11) AUTO_INCREMENT,
                            date date NOT NULL,
                            open_price int(11) DEFAULT 0,
                            high_price int(11) DEFAULT 0,
                            low_price int(11) DEFAULT 0,
                            close_price int(11) DEFAULT 0,
                            PRIMARY KEY (id)
                        )");
                        $da = array(
                            'date' => "20".$year."-".$month."-".$day,
                            'open_price' => $field['OPEN_PRICE'],
                            'high_price' => $field['HIGH_PRICE'],
                            'low_price' => $field['LOW_PRICE'],
                            'close_price' => $field['CLOSE_PRICE']
                        );
                        //echo json_encode($da);
                        $this->dbCom->insert($tableName, $da);
                        $comLateUpdated = $comLateUpdated + 1;
                    } else {
                        $da = array(
                            'date' => "20".$year."-".$month."-".$day,
                            'open_price' => $field['OPEN_PRICE'],
                            'high_price' => $field['HIGH_PRICE'],
                            'low_price' => $field['LOW_PRICE'],
                            'close_price' => $field['CLOSE_PRICE']
                        );
                        $this->data['message'] .= "Table not exists + Blank: (".$field['SYMBOL']."-".$tableName.")".json_encode($da)."<br>";
                        $comLateUpdated = $comLateUpdated + 1;
                    }
                }
            }
        }
        $this->data['message'] .= "Total Updated: ".$comUpdated." * ".$comLateUpdated."<br>";
    }
}