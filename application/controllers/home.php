<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	
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
        $this->data['load_from'] = "home";

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
        $this->dashboard();
    }

    public function dashboard() {
        $query = $this->db->get('companies');
        $this->data['total_companies'] = $query->num_rows();
        $query = $this->db->get('date_reports');
        $this->data['total_reports'] = $query->num_rows();
        
        $this->data['main_content'] = __FUNCTION__;
        $this->load->view('template', $this->data);
    }
    
    public function init_setup() {
		$totalCompaniesLoaded = $this->loadCompanyList();
        $this->data['total_companies'] = $totalCompaniesLoaded;
        $this->data['main_content'] = __FUNCTION__;
        $this->load->view('template', $this->data);
	}
    
    public function cleanComTables() {
        $query = $this->db->get("companies");
        foreach($query->result() as $row) {
            //$this->dbCom->empty_table($row->table);
            $this->dbCom->query("DROP TABLE IF EXISTS ".$row->table);
        }
        $this->db->empty_table("companies");
        $this->db->empty_table("date_reports");
        $this->db->query("ALTER TABLE companies AUTO_INCREMENT = 1");
        $this->db->query("ALTER TABLE date_reports AUTO_INCREMENT = 1");
        //$this->dbCom->empty_table('mytable');
        
        $this->dashboard();
    }

    function loadCompanyList() {

        $this->data['error'] = "";
        $this->data['message'] = "";
        $totalCompaniesLoaded = 0;
        
        // download last day file
        $daypast = 1;
        $filename = "";
        $isFileReceived = false;
        while(!$isFileReceived) {
            $day = date('d',strtotime("-".$daypast." days"));
            $month_str = strtoupper(date('M',strtotime("-".$daypast." days")));
            $year = date('Y',strtotime("-".$daypast." days"));

            $filename = "cm".$day.$month_str.$year."bhav.csv";
            $path = "http://www.nseindia.com/content/historical/EQUITIES/".$year."/".$month_str."/".$filename.".zip";

            $zipResource = fopen($this->base_path . "/TodaysFile.zip", "w");

            $arr1 = downloadFile($path, $zipResource);
            $page = $arr1['page'];

            if(!$page) {
                $this->data['error'] .= "File failed: ".$path." : ".$arr1['curl_error_str']."\n<br>";
                log_message("error", "File failed: ".$path." : ".$arr1['curl_error_str']);
                $daypast += 1;
                if($daypast > 3) {
                    break;
                }
                $isFileReceived = false;
            } else {
                $this->data['message'] .= "Got file: ".$path."\n<br>";
                log_message("debug", "Got file: ".$path." : ".$arr1['curl_error_str']);
                $isFileReceived = true;
            }
        }

        unzipFile($this->base_path . "/TodaysFile.zip", $this->base_path."/temp");
        // delete zip file
        unlink($this->base_path . "/TodaysFile.zip");
        
        $this->data['message'] .= "Extraction complete"."\n<br>";

        $csvData = $this->csvreader->parse_file($this->base_path."/temp/".$filename);

        foreach($csvData as $field) {
            //$this->data['message'] .= "".$field['SYMBOL']."<br>";
            if($field['SERIES'] == "EQ") {
                $tableName = processSymbol($field['SYMBOL']);
                $this->db->where("table", $tableName);
                $query = $this->db->get('companies');
                if($query->num_rows() == 0) {
                    $da = array(
                        'symbol' => $field['SYMBOL'],
                        'table' => $tableName,
                        'isin' => $field['ISIN']
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
                    $totalCompaniesLoaded = $totalCompaniesLoaded + 1;
                } else {
                    $this->data['error'] .= "Table / Company Already Present: ".$tableName."\n<br>";
                }
            }
        }
        return $totalCompaniesLoaded;
    }
}