<?php
  
require_once APPPATH.'/core/Main_model.php';
class Product_model extends Main_model
{
    public $table_name = "products";
	public function __construct(){
		parent::__construct();
        $this->load->library('upload','encrypt');
        $this->load->helper('string');

    }

    public function getById($id){
        $where = 'id = '.$id;
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->where($where);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getByStoreId($id,$limit){
        $where = 'storeId = '.$id;
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->where($where);
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function getAllByStoreId($id){
        $where = 'storeId = '.$id;
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->where($where);
        return $this->db->get()->result();
    }

    public function getFoodByCid($id,$cid){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->where('storeId',$id);
        $this->db->where('cid',$cid);
        return $this->db->get()->result();
    }

    public function getByCid($id){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->where('storeId',$id);
        return $this->db->get()->result();
    }
    

    public function getSearchItems($query,$id){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->like('name',$query);
        $this->db->where('storeId',$id);
        return $this->db->get()->result();
    }

    public function getFavs($ids){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $prodIds = explode(',',$ids);
        $this->db->where_in('id',$prodIds);
        $data = $this->db->get()->result();
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

    public function getProductByStores($ids,$limit){
        $this->db->select("*");
        $this->db->from($this->table_name);
        $where = "status = 1 AND storeId = ".$ids;
        $this->db->where($where);
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
    
    public function get_all(){
        $data = $this->get($this->table_name);
        return $data;
    }
    
    public function getAllProductsbyAgent(){
        $this->db->select('prod.*, str.cid AS cityId, str.uid AS uid');
        $this->db->from("products as prod");
        $this->db->join('store as str','prod.storeId = str.uid');
        $where = 'str.cid = 7';
        $this->db->where($where);
        $data = $this->db->get()->result();
        return $data;
    }

    public function saveUserLogs($data){
        $data = $this->saveLogs($data);
        return $data;
    }
    
}
