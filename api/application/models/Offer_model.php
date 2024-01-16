<?php
  
require_once APPPATH.'/core/Main_model.php';
class Offer_model extends Main_model
{
    public $table_name = "offers";
	public function __construct(){
		parent::__construct();
        $this->load->library('upload','encrypt');
        $this->load->helper('string');
        
    }

    public function getById($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_name,$where,'results');
        return $data;
    }

    public function getByCode($code){
        $where = "code = '".$code."' ";
        $data = $this->get($this->table_name,$where,'results');
        return $data;
    }

    public function saveList($data){
        return $this->insert($this->table_name,$data);
    }

    public function editList($data,$id){
        $where = "id = ".$id;
        return $this->update($this->table_name,$data,$where);
    }


    public function deleteList($id){
        $where = "id =".$id;
        return $this->delete($this->table_name,$where);
    }

    public function getByIdValue($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_name,$where);
        return $data;
    }

    public function get_all(){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->order_by('id', 'desc');
        $data = $this->db->get()->result();
     
        return $data;
    }

    public function saveUserLogs($data){
        $data = $this->saveLogs($data);
        return $data;
    }
}
