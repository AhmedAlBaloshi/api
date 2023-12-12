<?php
  
require_once APPPATH.'/core/Main_model.php';
class Thawani_model extends Main_model
{
    public $table_cart = "cart";
    public $table_products = "products";
    public $table_users = "users";
    public $table_thawani_sessions = "thawani_sessions";

	public function __construct(){
		parent::__construct();
        $this->load->helper('string');
    }

    public function getCartByUid($uid){
        $where = 'uid = '.$uid;
        $data = $this->get($this->table_cart,$where,'results');
        return $data;
    }
    
    public function getProductById($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_products,$where,'results');
        return $data;
    }

    public function getUserById($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_users,$where,'results');
        return $data;
    }

    public function saveList($data){
        return $this->insert($this->table_thawani_sessions,$data);
    }
}
