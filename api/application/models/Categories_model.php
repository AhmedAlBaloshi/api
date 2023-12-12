<?php
  
require_once APPPATH.'/core/Main_model.php';
class Categories_model extends Main_model
{
    public $table_name = "categories";
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

    public function getByStoreId($id){
        $where = 'storeId = '.$id;
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
        // $data = $this->get($this->table_name);
        // return $data;
        $this->db->select('cates.name_en as name_en,cates.name_ar as name_ar,cates.id as cid,usr.name_en as store_name_en,usr.name_ar as store_name_ar,cates.status as status,cates.storeId as storeId');
        $this->db->from("categories as cates");
        $this->db->join('store as usr','cates.storeId = usr.uid');
        $data = $this->db->get()->result();
        return $data;
    }

    public function getAllCategoriesByAgent(){
        $this->db->select('cates.name_en as name_en,cates.name_ar as name_ar,cates.id as cid,usr.name_en as store_name_en,usr.name_ar as store_name_ar,cates.status as status,cates.storeId as storeId');
        $this->db->from("categories as cates");
        $this->db->join('store as usr','cates.storeId = usr.uid');
        $where = 'usr.cid = 7';
        $this->db->where($where);
        $data = $this->db->get()->result();
        return $data;
    }

    public function saveUserLogs($data){
        $data = $this->saveLogs($data);
        return $data;
    }

}
