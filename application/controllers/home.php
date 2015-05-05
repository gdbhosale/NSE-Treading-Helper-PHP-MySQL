<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	
    var $base_url;
    var $base_path;
    var $stock_files;
    var $opt_files;
    var $data;
    var $dbCom;
    var $feas_out;
    
    public function __construct() {
        parent::__construct();
        
        $this->base_url = $this->config->item("base_url");
		$this->base_path = $this->config->item("base_path");
        $this->stock_files = $this->config->item("stock_files");
		$this->opt_files = $this->config->item("opt_files");

        $this->data = array();
        $this->data['base_url'] = $this->base_url;
        $this->data['load_from'] = "home";

        $this->db = $this->load->database('default', TRUE);
        $config['hostname'] = "localhost";
        $config['username'] = "rupeemax_user1";
        $config['password'] = $this->db->password;
        $config['database'] = "rupeemax_tread_companies";
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

        $this->feas_out = $this->test_feasibility();
    }

    public function deleting() {
        //chmod($this->base_path . "/data/temp/TodaysFile.zip", 0777);
        //chown($this->base_path . "/data/temp/TodaysFile.zip", 666);
        echo process_perm(fileperms($this->base_path . "/data/temp/TodaysFile.zip"));
        delete_file($this->base_path . "/data/temp/TodaysFile.zip");
        $deleteError = 0;
        $lines = array();
        //exec("DEL /F/Q \"".$this->base_path . "/data/temp/TodaysFile.zip\"", $lines, $deleteError);
        //emptyFolder($this->base_path."/data/temp");
    }

    public function test_feasibility() {
        // Database feasibility
        if (! $this->db->table_exists('companies')) {
            $q = "CREATE TABLE `companies` ( `id` int(11) NOT NULL AUTO_INCREMENT, `symbol` varchar(50) NOT NULL, `table` varchar(50) NOT NULL, `isin` varchar(20) NOT NULL, `has_duplicate` varchar(10) NOT NULL DEFAULT 'FALSE', `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`))";
            $this->db->query($q);
        }
        if (! $this->db->table_exists('date_reports')) {
            $q = "CREATE TABLE `date_reports` ( `id` int(11) NOT NULL AUTO_INCREMENT, `date` date NOT NULL, `status` varchar(50) NOT NULL DEFAULT 'None', `data` text NOT NULL, `tot_com_load` int(11) NOT NULL DEFAULT '0', `new_com_load` int(11) NOT NULL DEFAULT '0', `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`))";
            $this->db->query($q);
        }
        // File system feasibility
        $data_folder = $this->base_path.'/data';
        if(file_exists($data_folder)) {
            if (is_writable($data_folder)) {
                //echo "Writable";
                if(!file_exists($data_folder."/".$this->stock_files)) {
                    mkdir($data_folder."/".$this->stock_files);
                }
                if(!file_exists($data_folder."/".$this->opt_files)) {
                    mkdir($data_folder."/".$this->opt_files);
                }
                if(!file_exists($data_folder."/init")) {
                    mkdir($data_folder."/init");
                }
                if(!file_exists($data_folder."/temp")) {
                    mkdir($data_folder."/temp");
                }
            } else {
                return "Folder '".$data_folder."' not writable !!!";
                log_message("error", "Folder '".$data_folder."' not writable");
            }
        } else {
            log_message("error", "Folder '".$data_folder."' not exists");
            $opt = mkdir($data_folder);
            if($opt) {
                return "creating Folder structure under '".$data_folder."'";
                log_message("debug", "creating Folder structure under '".$data_folder."'");
                if(!file_exists($data_folder."/".$this->stock_files)) {
                    mkdir($data_folder."/".$this->stock_files);
                }
                if(!file_exists($data_folder."/".$this->opt_files)) {
                    mkdir($data_folder."/".$this->opt_files);
                }
                if(!file_exists($data_folder."/init")) {
                    mkdir($data_folder."/init");
                }
                if(!file_exists($data_folder."/temp")) {
                    mkdir($data_folder."/temp");
                }
            } else {
                return "Cannot Create data folder !!!";
                log_message("error", "Cannot Create data folder '".$data_folder."'");
            }
        }
    }
    
    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $query = $this->db->get('companies');
        $this->data['total_companies'] = $query->num_rows();

        $this->db->where("status", "SUCCESS");
        $query = $this->db->get('date_reports');
        $this->data['total_reports'] = $query->num_rows();
        
        $this->data['feas_out'] = $this->feas_out;
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
            log_message("debug", "Drop Company table '".$row->table."'.");
            $this->dbCom->query("DROP TABLE IF EXISTS ".$row->table);
        }
        log_message("debug", "Dropping Database.");
        $this->db->empty_table("companies");
        $this->db->empty_table("date_reports");
        $this->db->query("ALTER TABLE companies AUTO_INCREMENT = 1");
        $this->db->query("ALTER TABLE date_reports AUTO_INCREMENT = 1");
        //$this->dbCom->empty_table('mytable');
        
        log_message("debug", "Cleaning Directories.");

        emptyFolder($this->base_path."/data/temp");
        emptyFolder($this->base_path."/data/idx_opt");
        
        $this->dashboard();
    }

    function loadCompanyList() {

        $query = $this->db->get('companies');
        if($query->num_rows() == 0) {
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

                $zipResource = fopen($this->base_path . "/data/init/TodaysFile.zip", "w");

                $arr1 = downloadFile($path, $zipResource);
                fclose($zipResource);

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

            unzipFile($this->base_path . "/data/init/TodaysFile.zip", $this->base_path."/data/init");
            // delete zip file
            //delete_file($this->base_path . "/data/init/TodaysFile.zip");
            
            $this->data['message'] .= "Extraction complete"."\n<br>";

            $csvData = $this->csvreader->parse_file($this->base_path."/data/init/".$filename);

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
        } else {
            return $query->num_rows();
        }
    }

    function get_sync_data() {
        $di = new RecursiveDirectoryIterator($this->base_path."/data/idx_opt/");
        $to = get_timezone_offset("Asia/Kolkata");
        $arr = array();
        foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
            if(!endsWith($filename, "/.") && !endsWith($filename, "/..")) {
                $ff = array('filename' => $filename, 'size' => $file->getSize(), 'mtime' => ($file->getMTime() - $to));
                $arr[] = $ff;
            }
            //echo $filename . ' - ' . $file->getSize() . ' bytes, '.date ("F d Y H:i:s.", "".($file->getMTime() - $to)).' <br/>';
        }
        //echo count($arr);
        echo json_encode($arr);
    }
}