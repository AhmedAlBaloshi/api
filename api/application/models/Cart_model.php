<?php
require_once APPPATH.'/core/Main_model.php';
class Cart_model extends Main_model
{
    public $table_name = "cart";
	public function __construct(){
		parent::__construct();
        $this->load->helper('string');
    }

    public function getById($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_name,$where,'results');
        return $data;
    }

    public function getByUid($id){
        $where = 'uid = '.$id;
        $data = $this->get($this->table_name,$where,'results');
        return $data;
    }

    public function getByUidDetailed($id){
        $this->db->select('crt.id as crt_id, crt.uid as crt_uid, crt.pid as crt_pid, crt.pqty as crt_pqty, crt.price_type as crt_price_type, crt.price as crt_price, crt.datetime as crt_datetime, prdct.id as prdct_id, prdct.storeId as prdct_storeId, prdct.cid as prdct_cid, prdct.cover as prdct_cover, prdct.descriptions_en as prdct_descriptions_en, prdct.descriptions_ar as prdct_descriptions_ar, prdct.price as prdct_price, prdct.price_type as prdct_price_type, prdct.rating as prdct_rating, prdct.veg as prdct_veg, prdct.name_en as prdct_name_en, prdct.name_ar as prdct_name_ar, prdct.variations as prdct_variations, prdct.size as prdct_size, prdct.status as prdct_status, prdct.notes_en as prdct_notes_en, prdct.notes_ar as prdct_notes_ar, prdct.internal_tax as prdct_internal_tax, str.*');
        $this->db->from("cart as crt");
        $this->db->join('products as prdct','crt.pid = prdct.id');
        $this->db->join('store as str','prdct.storeId = str.uid');
        $this->db->order_by("crt.id", "desc");
        $this->db->where('crt.uid',$id);
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

     public function editByUid($data,$id){
        $where = "uid = ".$id;
        return $this->update($this->table_name,$data,$where);
    }

    public function deleteList($id){
        $where = "id =".$id;
        return $this->delete($this->table_name,$where);
    }

    public function deleteListByUid($uid){
        //$where = "uid =".$uid;
        //$where = "uid IN(SELECT uid FROM ".$this->table_name." GROUP BY uid HAVING COUNT(*) > 1) AND uid =".$uid; //where FIELD in(SELECT FIELD FROM students GROUP BY FIELD HAVING count(*) > 1) AND FIELD = 'a';
        //return $this->delete($this->table_name,$where);
        return $this->db->delete($this->table_name, array('uid' => $uid));
    }

    public function getByIdValue($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_name,$where);
        return $data;
    }

    public function get_all(){
        $data = $this->get($this->table_name);
        return $data;
    }

    public function saveUserLogs($data){
        $data = $this->saveLogs($data);
        return $data;
    }
}
