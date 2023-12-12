<?php
  
require_once APPPATH.'/core/Main_model.php';
class Blogs_model extends Main_model
{
    public $table_name = "blogs";
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
    public function getTops(){
        $this->db->select("*");
        $this->db->from($this->table_name);
        $where = "status = 1";
        $this->db->where($where);
        $this->db->order_by("id", "DESC");
        $this->db->limit(3);
        return $this->db->get()->result();
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
        $this->db->select("*");
        $this->db->from($this->table_name);
        $this->db->order_by("id", "DESC");
        return $this->db->get()->result();
    }

    public function saveUserLogs($data){
        $data = $this->saveLogs($data);
        return $data;
    }
}
