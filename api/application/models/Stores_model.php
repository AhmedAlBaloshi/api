<?php

require_once APPPATH . '/core/Main_model.php';
class Stores_model extends Main_model
{
    public $table_name = "store";
    public function __construct()
    {
        parent::__construct();
        $this->load->library('upload', 'encrypt');
        $this->load->helper('string');
    }

    public function getById($id)
    {
        $where = 'id = ' . $id;
        $data = $this->get($this->table_name, $where, 'results');
        foreach ($data as &$d) {
            $id = $d->id;
            $typeId = $d->type_id;

            $this->db->select('store_types.*');
            $this->db->from("stores_types");
            $this->db->join("store_types", 'store_types.id = stores_types.type_id');
            $this->db->where('stores_types.store_id', $id);
            $majorCategories = $this->db->get()->result();
            // $d->rest_cuisines = json_encode($restCuisines);

            $d->major_categories = json_encode($majorCategories);

            $this->db->select('rest_cuisines.*');
            $this->db->from("stores_cuisines");
            $this->db->join("rest_cuisines", 'rest_cuisines.id = stores_cuisines.cuisine_id');
            $this->db->where('stores_cuisines.store_id', $id);
            $restCuisines = $this->db->get()->result();
            $d->rest_cuisines = json_encode($restCuisines);
        }
        return $data;
    }

    public function getByCity($id)
    {
        $where = 'cid = ' . $id . " AND status = 1 ";
        $data = $this->get($this->table_name, $where, 'results');
        return $data;
    }

    public function getByUid($id)
    {
        $where = 'uid = ' . $id;
        $data = $this->get($this->table_name, $where, 'results');
        return $data;
    }

    public function addProducts($data, $id){

        $products = $data['products'];
        foreach($products as $prod){
            unset($prod['id']);
            unset($prod['isSelected']);
            $prod['storeId'] = $id;
               
            $this->db->select('*');
            $this->db->from("products");
            $this->db->where('storeId', $id);
            $this->db->where('name_en', $prod['name_en']);
            $restCuisines = $this->db->get()->result();
          if(count($restCuisines) == 0){
            $this->insert('products', $prod);
          }
        }
        return true;
    }
    
    public function addCuisines($data, $id){

        $cuisines = $data['cuisines'];
        foreach($cuisines as $prod){
            $this->db->select('*');
            $this->db->from("stores_cuisines");
            $this->db->where('store_id', $id);
            $this->db->where('cuisine_id', $prod['id']);
            $restCuisines = $this->db->get()->result();
          if(count($restCuisines) == 0){
            $data = ['store_id'=>$id, 'cuisine_id' => $prod['id']];
            $this->insert('stores_cuisines', $data);}
        }
        return true;
    }

    public function saveList($data)
    {
        $data['user_id'] = $data['uid'];
        // $data['type_id'] = $data['major_categories'];
        $majorCategories  = json_decode($data['major_categories']);
        $restCuisines  = json_decode($data['rest_cuisines']);
        unset($data['uid']);
        unset($data['major_categories']);
        unset($data['rest_cuisines']);

        $this->insert($this->table_name, $data);
        $id = $this->db->insert_id();

        foreach ($restCuisines as $cuisine) {
            $this->insert('stores_cuisines', ['store_id' => $id, 'cuisine_id' => $cuisine->id]);
        }
        foreach ($majorCategories as $type) {
            $this->insert('stores_types', ['store_id' => $id, 'type_id' => $type->id]);
        }
        return $id;
    }

    public function editList($data, $id)
    {
        $where = "id = " . $id;
        $majorCategories  = json_decode($data['major_categories']);
        $restCuisines  = json_decode($data['rest_cuisines']);
        unset($data['id']);
        unset($data['major_categories']);
        unset($data['rest_cuisines']);


        $this->db->where('store_id', $id);
        $this->db->delete('stores_cuisines');

        foreach ($restCuisines as $cuisine) {
            $this->insert('stores_cuisines', ['store_id' => $id, 'cuisine_id' => $cuisine->id]);
        }

        $this->db->where('store_id', $id);
        $this->db->delete('stores_types');

        foreach ($majorCategories as $type) {
            $this->insert('stores_types', ['store_id' => $id, 'type_id' => $type->id]);
        }

        return $this->update($this->table_name, $data, $where);
    }

    public function editByUid($data, $id)
    {
        $where = "uid = " . $id;
        return $this->update($this->table_name, $data, $where);
    }

    public function editById($data, $id)
    {
        $where = "id = " . $id;
        return $this->update($this->table_name, $data, $where);
    }


    public function deleteList($id)
    {
        $where = "id =" . $id;
        return $this->delete($this->table_name, $where);
    }
   
    public function deleteFromSilver($id)
    {
        $data = ['is_silver_display' => 0];
    
        $this->db->where('id',$id);
        return $this->db->update($this->table_name, $data);
    }

    public function addToSilver($post){
        foreach($post['id'] as $p){
        $data = ['is_silver_display' => 1];
    
        $this->db->where('id',$p);
         $this->db->update($this->table_name, $data);
    }
    return true;
    }

    public function getByIdValue($id)
    {
        $where = 'id = ' . $id;
        $data = $this->get($this->table_name, $where);
        return $data;
    }

    public function get_all()
    {
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->order_by('isClosed', 'asc');
        $this->db->order_by('is_busy', 'asc');
        $this->db->order_by('id', 'desc');
        $data = $this->db->get()->result();
        return $data;
    }
    public function get_for_silver_display()
    {
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->where('is_silver_display',1);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getAllStoresbyAgent()
    {
        $where = 'cid = 7';
        $data = $this->get($this->table_name, $where);
        return $data;
    }

    public function saveUserLogs($data)
    {
        $data = $this->saveLogs($data);
        return $data;
    }

    public function getStoresData($ids)
    {
        $this->db->select('store.uid as uid, store.cover as cover, store.cover_hr as cover_hr, store.lat as lat,store.lng as lng,store.name_en as name_en,store.name_ar as name_ar,store.payment_method as payment_method,user.fcm_token as token,store.status as status,store.address_en as address_en,store.address_ar as address_ar,user.email as email,store.mobile as mobile');
        $this->db->from("store as store");
        $this->db->join('users as user', 'store.uid = user.id');
        $storeIds = explode(',', $ids);
        $this->db->where_in('uid', $storeIds);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getStoresIds($id)
    {
        $sql = "SELECT group_concat(`uid` separator ',') as `uid` FROM `store` where cid = " . $id;
        $query = $this->db->query($sql);
        $array1 = $query->row_array();
        return $array1['uid'];
    }


    public function nearMe($lat, $lng, $distance, $type)
    {
        //3959 Miles
        //6371 KMS
        $values;
        if ($type == '0') {
            $values = 3959;
        } else {
            $values = 6371;
        }
        $sql = "SELECT address_en,address_ar,certificate_type,certificate_url,cid,close_time,shipping_price,shipping,cover,cover_hr,major_categories,minor_categories,rest_cuisines,payment_method,featured,descriptions_en,descriptions_ar,min_order_price,id,images,isClosed,lat,lng,mobile,name_en,name_ar,open_time,rating,status,is_busy,time,total_rating,uid,verified,notes_en,notes_ar, ( $values * acos(cos(radians($lat)) * cos(radians(lat)) * cos(radians(lng) - radians($lng)) + sin(radians($lat)) * sin(radians(lat ))) ) AS distance FROM store HAVING distance < $distance ORDER BY distance";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getUsersNames($ids)
    {
        $this->db->select('*');
        $this->db->from($this->table_name);
        $uid = explode(',', $ids);
        $this->db->where_in('user_id', $uid);
        return $this->db->get()->result();
    }
}