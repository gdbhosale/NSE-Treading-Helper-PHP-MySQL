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
            $year = $this->input->get("year", TRUE);
            if(isset($year) && $year != "") {
                $this->data['cnt_year'] = $year;
            } else {
                $this->data['cnt_year'] = date("Y");
            }

            $this->db->where("date >= ", "".$this->data['cnt_year']."-01-01");
            $this->db->where("date <= ", "".$this->data['cnt_year']."-12-31");
            $q = $this->db->get("date_reports");
            $days = array();
            foreach($q->result_array() as $day) {
                $days[$day['date']] = $day;
            }
            //print_r($days);
            $this->data['report_days'] = $days;
            
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

        $status = "";
        $data1 = array('file_name' => '', 'file_offline_status' => "false", 'file_download_status' => "false", "data_companies" => array(), "new_companies" => array(), "error_message" => "");

        $arr = explode("-", $day);
        $day = $arr[0];
        $month = $arr[1];
        $year = $arr[2];

        $filename = "PR".$day.$month.$year.".zip";

        $data1['file_name'] = $filename;
        
        // do we have file ?
        $fileExists = false;
        if(file_exists($this->base_path . "/stock_files/".$filename) && filesize($this->base_path . "/stock_files/".$filename) > 1000) {
            $fileExists = true;
            $data1['file_offline_status'] = 'true';
        }

        if(!$fileExists) {
            $data1['file_offline_status'] = 'false';

            $path = "http://www.nseindia.com/archives/equities/bhavcopy/pr/".$filename;

            $zipResource = fopen($this->base_path . "/stock_files/".$filename, "w");

            $arr1 = downloadFile($path, $zipResource);
            $page = $arr1['page'];
            
            if(!$page) {
                $data1['file_download_status'] = "false";

                $this->data['error'] .= "File failed: ".$path." : ".$arr1['curl_error_str']."\n<br>";
                log_message("error", "File failed: ".$path." : ".$arr1['curl_error_str']);
                
                $data1['error_message'] = "File Download failed: ".$path." : ".$arr1['curl_error_str'];

                $this->db->where("date", "20".$year."-".$month."-".$day);
                $q = $this->db->get("date_reports");
                if($q->num_rows() == 0) {
                    $arr2 = array('date' => "20".$year."-".$month."-".$day, 'status' => "FAILED", 'tot_com_load' => 0, 'new_com_load' => 0, 'data' => json_encode($data1));
                    $this->db->insert('date_reports', $arr2);
                } else {
                    $arr2 = array('status' => "FAILED", 'tot_com_load' => 0, 'new_com_load' => 0, 'data' => json_encode($data1), 'time_updated' => date("Y-m-d H:i:s"));
                    $this->db->where("date", "20".$year."-".$month."-".$day);
                    $this->db->update('date_reports', $arr2);
                }
                return 0;
            } else {
                $data1['file_download_status'] = "true";
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
                    } else {
                        // update
                        $da = array(
                            'open_price' => $field['OPEN_PRICE'],
                            'high_price' => $field['HIGH_PRICE'],
                            'low_price' => $field['LOW_PRICE'],
                            'close_price' => $field['CLOSE_PRICE']
                        );
                        //echo json_encode($da);
                        $this->dbCom->where("date", "20".$year."-".$month."-".$day);
                        $this->dbCom->update($tableName, $da);
                    }

                    $data1['data_companies'][] = $tableName;

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

                        $data1['data_companies'][] = $tableName;
                        $data1['new_companies'][] = $tableName;

                    } else {
                        $da = array(
                            'date' => "20".$year."-".$month."-".$day,
                            'open_price' => $field['OPEN_PRICE'],
                            'high_price' => $field['HIGH_PRICE'],
                            'low_price' => $field['LOW_PRICE'],
                            'close_price' => $field['CLOSE_PRICE']
                        );
                        $this->data['message'] .= "Table not exists + Blank: (".$field['SYMBOL']."-".$tableName.")".json_encode($da)."<br>";
                    }
                }
            }
        }
        $this->data['message'] .= "Total Updated: ".$comUpdated." * ".$comLateUpdated."<br>";

        $data1['tot_com'] = $comUpdated;
        $data1['new_com'] = $comLateUpdated;

        $this->db->where("date", "20".$year."-".$month."-".$day);
        $q = $this->db->get("date_reports");
        if($q->num_rows() == 0) {
            $arr2 = array('date' => "20".$year."-".$month."-".$day, 'status' => "SUCCESS", 'tot_com_load' => $comUpdated, 'new_com_load' => $comLateUpdated, 'data' => json_encode($data1));
            $this->db->insert('date_reports', $arr2);
        } else {
            $arr2 = array('status' => "SUCCESS", 'tot_com_load' => $comUpdated, 'new_com_load' => $comLateUpdated, 'data' => json_encode($data1), 'time_updated' => date("Y-m-d H:i:s"));
            $this->db->where("date", "20".$year."-".$month."-".$day);
            $this->db->update('date_reports', $arr2);
        }
    }
}