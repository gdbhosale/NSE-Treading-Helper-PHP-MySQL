<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	
    var $base_url;
    var $base_path;
    var $stock_files;
    var $data;
    var $curl_error_str;
    
    public function __construct() {
        parent::__construct();
        
        $this->base_url = $this->config->item("base_url");
		$this->base_path = $this->config->item("base_path");
		$this->stock_files = $this->config->item("stock_files");
        $this->data = array();
        $this->data['base_url'] = $this->base_url;
        
        $this->load->library('csvreader');
    }
    
    public function index() {
		
		$this->load->view('index', $this->data);
        
        //echo getcwd();
	}
    
    public function update() {
        $this->data['error'] = "";
        
        $query = $this->db->get('companies');
		if($query->num_rows() > 0) {
            $this->data['message'] = "Companies presents"."\n<br>";
        } else {
            $this->data['message'] = "Companies not presents"."\n<br>";
            
            // download last day file
            $daypast = 2;
            $filename = "";
            $isFileReceived = false;
            while(!$isFileReceived) {
                $day = date('d',strtotime("-".$daypast." days"));
                $month_str = strtoupper(date('M',strtotime("-".$daypast." days")));
                $year = date('Y',strtotime("-".$daypast." days"));

                $filename = "cm".$day.$month_str.$year."bhav.csv";
                $path = "http://www.nseindia.com/content/historical/EQUITIES/".$year."/".$month_str."/".$filename.".zip";

                $zipResource = fopen($this->base_path . "/TodaysFile.zip", "w");
                
                $page = $this->downloadFile($path, $zipResource);
                
                if(!$page) {
                    $this->data['error'] .= "File: ".$path." : ".$this->curl_error_str."\n<br>";
                    log_message("error", "File failed: ".$path." : ".$this->curl_error_str);
                    $daypast += 1;
                    if($daypast > 3) {
                        break;
                    }
                    $isFileReceived = false;
                } else {
                    $this->data['message'] .= "Got file: ".$path."\n<br>";
                    log_message("debug", "Got file: ".$path." : ".$this->curl_error_str);
                    $isFileReceived = true;
                }
            }
            
            $this->unzipFile($this->base_path . "/TodaysFile.zip", $this->base_path."/zipFolder");
            
            $this->data['message'] .= "Extraction complete"."\n<br>";
            
            $csvData = $this->csvreader->parse_file($this->base_path."/zipFolder/".$filename);
            
            foreach($csvData as $field) {
                //$this->data['message'] .= "".$field['SYMBOL']."<br>";
                if($field['SERIES'] == "EQ") {
                    $da = array(
                        'symbol' => $field['SYMBOL'],
                        'isin' => $field['ISIN']
                    );
                    $this->db->insert('companies', $da);
                }
            }
            $this->data['message'] .= "Got total ".($this->db->insert_id())." companies...\n<br>";
            
            /*
            // download latest files
            
            // Open the Zip file
            $zip = new ZipArchive;
            $extractPath = "path_to_extract";
            if($zip->open($zipFile) != "true"){
             echo "Error :- Unable to open the Zip File";
            } 
            // Extract Zip File
            $zip->extractTo($extractPath);
            $zip->close();
            */
        }
        
        // select any company table
        // check if latest date is todays
        //if yes
            // Go Back with success
        //else
            // download opline files of missing days
            // generate log
            // push them to database
            // go back

        $this->load->view('update', $this->data);
	}
    
    function unzipFile($zipFile, $folder) {
        $zip = new ZipArchive;
            if($zip->open($zipFile) != "true") {
                $this->data['error'] .= "Error :- Unable to open the Zip File: ".$zipFile."\n<br>";
                log_message("error", "Unable to open the Zip File: ".$zipFile);
            }
            //Empty zipFolder
            $files = glob($folder."/*");
            foreach($files as $file) {
                if(is_file($file))
                    unlink($file);
            }
            // delete zip file
            unlink($zipFile);
            // Extract Zip File
            $zip->extractTo($folder);
            $zip->close();
    }
    
    function downloadFile($src, $dest) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $src);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_FILE, $dest);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13');
        $page = curl_exec($ch);
        $this->curl_error_str = curl_error($ch);
        curl_close($ch);
        return $page;
    }
}