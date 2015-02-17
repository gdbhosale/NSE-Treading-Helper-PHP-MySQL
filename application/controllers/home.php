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
	}
    
    public function update() {
        
        $query = $this->db->get('companies');
		if($query->num_rows() > 0)
        
        
        
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