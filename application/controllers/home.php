<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	
    var $base_url;
    var $base_path;
    var $stock_files;
    var $data;
    
    public function __construct() {
        parent::__construct();
        
        $this->base_url = $this->config->item("base_url");
		$this->base_path = $this->config->item("base_path");
		$this->stock_files = $this->config->item("stock_files");
        $this->data = array();
        $this->data['base_url'] = $this->base_url;
    }
    
    public function index() {
		$this->load->view('index', $this->data);
        
        //echo getcwd();
	}
    
    public function update() {
        
        $query = $this->db->get('companies');
		if($query->num_rows() > 0) {
            $this->data['message'] = "Companies presents";
        } else {
            $this->data['message'] = "Companies not presents";
            $filename = date("");
            $path = "http://www.nseindia.com/content/historical/EQUITIES/2015/FEB/cm16FEB2015bhav.csv.zip";
            
            $this->data['message'] = $path;
            
            
            /*
            // download latest files
            file_put_contents($this->base_path . "Tmpfile.zip", fopen("http://someurl/file.zip", 'r'));
            
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
}