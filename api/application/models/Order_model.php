<?php
  
require_once APPPATH.'/core/Main_model.php';
class Order_model extends Main_model
{
    public $table_name = "orders";
	public function __construct(){
		parent::__construct();
        $this->load->library('upload','encrypt');
        $this->load->helper('string');
        
    }

    public function getById($id){
        $this->db->select('order.uid as uid,order.address as address,order.notes as notes,order.did as did,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id,order.restId as restId,str.name_en as str_name_en,str.name_ar as str_name_ar,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,str.address_en as str_address_en,str.address_ar as str_address_ar,str.cover as str_cover,usr.fcm_token as str_fcm_token,usr.mobile as str_mobile');
        $this->db->from("orders as order");
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->join('users as usr','order.restId = usr.id');
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.id',$id);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getByUid($id){
        $this->db->select('order.uid as uid,order.address as address,order.notes as notes,order.did as did,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id, str.id as strId, str.uid as strUsrId, str.name_en as str_name_en,str.name_ar as str_name_ar,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total, str.address_en as str_address_en,str.address_ar as str_address_ar,str.cover as str_cover');
        $this->db->from("orders as order");
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.uid',$id);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getByUidOnActiveStatus($id){
        $status = array('created', 'accepted', 'ongoing');
        $this->db->select('order.uid as uid,order.address as address,order.notes as notes,order.did as did,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id, str.id as strId, str.uid as strUsrId, str.name_en as str_name_en,str.name_ar as str_name_ar,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total, str.address_en as str_address_en,str.address_ar as str_address_ar,str.cover as str_cover');
        $this->db->from("orders as order");
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.uid', $id);
        $this->db->where_in('order.status', $status);
        $data = $this->db->get()->result();
        return $data;
    }

     public function getByStoreId($id){
        $this->db->select('order.uid as uid,order.address as address,order.did as did,order.notes as notes,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id,order.restId as restId,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,usr.first_name as user_first_name,usr.last_name as user_last_name,usr.cover as user_cover,usr.fcm_token as user_fcm_token');
        $this->db->from("orders as order");
        $this->db->join('users as usr','order.uid = usr.id');
        $this->db->order_by("order.id", "desc");
        $this->db->limit(50);
        $this->db->where('order.restId',$id);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getByStoreWithNames($id){
        $this->db->select('order.uid as uid,order.address as address,order.assignee as assignee,order.notes as notes,order.coupon_code as coupon_code,order.date_time as date_time,order.delivery_charge as delivery_charge,order.discount as discount,order.driver_id as driver_id,order.grand_total as grand_total,order.id as id,order.notes as notes,order.order_to as order_to,order.orders as orders,order.paid_method as paid_method,order.pay_key as pay_key,order.status as status,order.store_id as store_id,order.tax as tax,order.total as total,user.cover as cover,user.first_name as first_name,user.last_name as last_name');
        $this->db->from("orders as order");
        $this->db->join('users as user','order.uid = user.id');
        $this->db->where("FIND_IN_SET(".$id.",store_id) >", 0);
        $this->db->order_by("id", "desc");
        // $this->db->limit(10);
        $data = $this->db->get()->result();
        return $data;
    }

    // public function getByDriverId($id){
    //     $this->db->select('order.uid as uid,order.address as address,order.did as did,order.notes as notes,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id,order.restId as restId,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,usr.first_name as user_first_name,usr.last_name as user_last_name,usr.cover as user_cover');
    //     $this->db->from("orders as order");
    //     $this->db->join('users as usr','order.uid = usr.id');
    //     $this->db->order_by("order.id", "desc");
    //     $this->db->where('order.did',$id);
    //     $data = $this->db->get()->result();
    //     return $data;
    // }
    
    public function getCountCurrentOrdersByDriverId($id){
        $this->db->select('COUNT(id) AS totalCurrentOrders');
        $this->db->from("orders");
        $this->db->where('did',$id);
        $this->db->where("status IN ('accepted' , 'ongoing')");
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getTotalAmountOfCashOrdersByDriverId($data){
        $this->db->select('SUM(grand_total) AS totalAmountOfCashOrders');
        $this->db->from("orders");
        $this->db->where('did',$data['did']);
        $this->db->where("pay_method = 'Cash'");
        $this->db->where("paid_to_driver IS NULL");
        $this->db->where("status IN ('delivered', 'undefined')");
        //$where = "did = ".$data['did']." AND pay_method = 'Cash' AND paid_to_driver IS NULL";
        //$this->db->where($where);
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getByDriverId($id){
        $this->db->select('order.uid as uid,order.address as address,order.did as did,order.notes as notes,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id,order.restId as restId,str.name_en as str_name_en,str.name_ar as str_name_ar,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,usr.first_name as user_first_name,usr.last_name as user_last_name,usr.cover as user_cover,usr.fcm_token as user_fcm_token,str.address_en as str_address_en,str.address_ar as str_address_ar,str.cover as str_cover,str.lat as str_lat,str.lng as str_lng,str_usr.fcm_token as str_user_fcm_token,str_usr.mobile as str_user_mobile');
        $this->db->from("orders as order");
        $this->db->join('users as usr','order.uid = usr.id');
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->join('users as str_usr','str.uid = str_usr.id');
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.did',$id);
        $this->db->limit(50);
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getCurrentOrdersByDriverIdForDrivers($id){
        $this->db->select('order.uid as uid,order.address as address,order.did as did,order.notes as notes,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id,order.restId as restId,str.name_en as str_name_en,str.name_ar as str_name_ar,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,usr.first_name as user_first_name,usr.last_name as user_last_name,usr.cover as user_cover,usr.fcm_token as user_fcm_token,str.address_en as str_address_en,str.address_ar as str_address_ar,str.cover as str_cover,str.lat as str_lat,str.lng as str_lng,str_usr.fcm_token as str_user_fcm_token,str_usr.mobile as str_user_mobile');
        $this->db->from("orders as order");
        $this->db->join('users as usr','order.uid = usr.id');
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->join('users as str_usr','str.uid = str_usr.id');
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.did',$id);
        $this->db->where("order.status IN ('accepted','ongoing')");
        $this->db->limit(50);
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getOldOrdersByDriverIdForDrivers($id){
        $this->db->select('order.uid as uid,order.did as did,order.restId as restId,str.name_en as str_name_en,str.name_ar as str_name_ar,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,usr.first_name as user_first_name,usr.last_name as user_last_name,usr.cover as user_cover,usr.fcm_token as user_fcm_token,str.cover as str_cover,str_usr.fcm_token as str_user_fcm_token');
        $this->db->from("orders as order");
        $this->db->join('users as usr','order.uid = usr.id');
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->join('users as str_usr','str.uid = str_usr.id');
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.did',$id);
        $this->db->limit(50);
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getAcceptedOngoingOrdersByDriverIdForDrivers($id){
        $this->db->select('order.did as did,order.id as orderId,order.status as status');
        $this->db->from("orders as order");
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.did',$id);
        $this->db->where("order.status IN ('accepted','ongoing')");
        $this->db->limit(25);
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getByDriverIdForAdmin($id){
        $this->db->select('order.uid as uid,order.address as address,order.did as did,order.notes as notes,order.applied_coupon as applied_coupon,order.coupon_id as coupon_id,order.restId as restId,str.name_en as str_name_en,str.name_ar as str_name_ar,order.id as orderId,order.delivery_charge as delivery_charge, order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,usr.first_name as user_first_name,usr.last_name as user_last_name,usr.cover as user_cover,usr.fcm_token as user_fcm_token,str.address_en as str_address_en,str.address_ar as str_address_ar,str.cover as str_cover,str.lat as str_lat,str.lng as str_lng,str_usr.fcm_token as str_user_fcm_token,str_usr.mobile as str_user_mobile');
        $this->db->from("orders as order");
        $this->db->join('users as usr','order.uid = usr.id');
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->join('users as str_usr','str.uid = str_usr.id');
        $this->db->order_by("order.id", "desc");
        $this->db->where('order.did',$id);
        $this->db->limit(900);
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getByDriverIdWithAddress($id){
        $this->db->select('order.uid as uid,order.address as address,order.did as did,order.restId as restId,order.id as orderId,order.delivery_charge as delivery_charge,order.grand_total as grand_total,order.paid as paid,order.status as status,order.total as total,adrs.lat as customer_loc_lat,adrs.lng as customer_loc_lng');
        $this->db->from("orders as order");
        $this->db->join('address as adrs','order.address = adrs.id' , 'left');
        $this->db->where('order.paid_to_driver IS NULL');
        $this->db->where('order.did',$id);
        $this->db->where("status IN ('accepted','ongoing','delivered', 'undefined')");
        $this->db->order_by("order.id", "desc");
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getByDriverIdWithAddressForStat($data){
        $this->db->select('order.uid as uid,order.address as address,order.did as did,order.restId as restId,order.id as orderId,order.delivery_charge as delivery_charge,order.grand_total as grand_total,order.paid as paid,order.status as status,order.total as total,order.time as time,order.pay_method as pay_method,order.paid_to_driver as paid_to_driver,order.paid_to_driver_time as paid_to_driver_time,adrs.address_en as customer_adrs_en,adrs.address_ar as customer_adrs_ar,adrs.lat as customer_loc_lat,adrs.lng as customer_loc_lng, str.name_en as str_name_en, str.name_ar as str_name_ar');
        $this->db->from("orders as order");
        $this->db->join('address as adrs','order.address = adrs.id' , 'left');
        $this->db->join('store as str','order.restId = str.uid' , 'left');
        $this->db->where('order.time >=', $data['start']);
        $this->db->where('order.time <=', $data['end']);
        if($data['includePaidOrders'] == 0){
            $this->db->where('order.paid_to_driver IS NULL');    
        }
        $this->db->where('order.did',$data['did']);
        $this->db->where("order.status IN ('accepted','ongoing','delivered', 'undefined')");
        $this->db->order_by("order.id", "desc");
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getByDriverIdWithAddressForStatForAllStatus($data){
        $this->db->select('order.uid as uid,order.address as address,order.did as did,order.restId as restId,order.id as orderId,order.delivery_charge as delivery_charge,order.grand_total as grand_total,order.paid as paid,order.status as status,order.total as total,order.time as time,order.pay_method as pay_method,order.paid_to_driver as paid_to_driver,order.paid_to_driver_time as paid_to_driver_time,adrs.address_en as customer_adrs_en,adrs.address_ar as customer_adrs_ar,adrs.lat as customer_loc_lat,adrs.lng as customer_loc_lng, str.name_en as str_name_en, str.name_ar as str_name_ar');
        $this->db->from("orders as order");
        $this->db->join('address as adrs','order.address = adrs.id' , 'left');
        $this->db->join('store as str','order.restId = str.uid' , 'left');
        $this->db->where('order.time >=', $data['start']);
        $this->db->where('order.time <=', $data['end']);
        if($data['includePaidOrders'] == 0){
            $this->db->where('order.paid_to_driver IS NULL');    
        }
        $this->db->where('order.did',$data['did']);
        $this->db->where("order.status NOT IN ('cancel','rejected')");
        $this->db->order_by("order.id", "desc");
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function setDriverOrdersToPaid($data){
        $this->db->set('paid_to_driver', 1);
        $this->db->set('paid_to_driver_time', $data['changeDateTime']);
        $this->db->where('time >=', $data['start']);
        $this->db->where('time <=', $data['end']);
        $this->db->where('did',$data['did']);
        return $this->db->update($this->table_name);
    }

    public function driverStats($data){
        $this->db->select('*')->from($this->table_name);
        $this->db->where('time >=', $data['start']);
        $this->db->where('time <=', $data['end']);
        $where = "did = ".$data['did']." AND status IN ('delivered', 'undefined')";
        $this->db->where($where);
        $this->db->order_by("id", "desc");
        return $this->db->get()->result();
    }

    public function storeStats($data){
        $paymentMethods = array();
        $this->db->select('*')->from($this->table_name);
        $this->db->where('time >=', $data['start']);
        $this->db->where('time <=', $data['end']);
        
        if($data['cashPaidOrders'] == 1){
            array_push($paymentMethods, 'Cash');
        }
        
        if($data['visaPaidOrders'] == 1){
            array_push($paymentMethods, 'Thawani');
        }
        
        //$where = 'restId = '.$data['sid'];
        $where = "restId = ".$data['sid']." AND pay_method IN ('".implode("','",$paymentMethods)."')";
        $this->db->where($where);
        $this->db->order_by("id", "desc");
        return $this->db->get()->result();
    }

    public function getAdminTop(){
        $this->db->select('order.id as id,order.address as address,order.notes as notes,order.applied_coupon,order.coupon_id as coupon_id,order.did as did,order.delivery_charge as delivery_charge,order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.restId as restId,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,user.cover as cover,user.first_name as first_name,user.last_name as last_name,str.name_en as str_name_en,str.name_ar as str_name_ar');
        $this->db->from("orders as order");
        $this->db->join('users as user','order.uid = user.id');
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->order_by("id", "desc");
        $this->db->limit(10);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getAdminTopByAgent(){
        $this->db->select('order.id as id,order.address as address,order.notes as notes,order.applied_coupon,order.coupon_id as coupon_id,order.did as did,order.delivery_charge as delivery_charge,order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.restId as restId,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,user.cover as cover,user.first_name as first_name,user.last_name as last_name,str.name_en as str_name_en,str.name_ar as str_name_ar');
        $this->db->from("orders as order");
        $this->db->join('users as user','order.uid = user.id');
        $this->db->join('store as str','order.restId = str.uid');
        $where = 'str.cid = 7';
        $this->db->where($where);
        $this->db->order_by("id", "desc");
        $this->db->limit(10);
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
    
    public function cancelOrderByUser($data,$id){
        $where = array('id' => $id, 'status' => 'created');
        return $this->update($this->table_name,$data,$where);
    }
    
    public function acceptRejectOrderByStore($data,$id){
        $where = array('id' => $id, 'status !=' => 'cancel');
        return $this->update($this->table_name,$data,$where);
    }
    
    public function selectOrderByDriver($data,$id){
        $where = array('id' => $id, 'did' => 0);
        return $this->update($this->table_name,$data,$where);
    }
    
    public function checkIfOrderRated($id){
        $this->db->select('*');
        $this->db->from("rating");
        $this->db->where('oid',$id);
        $data = $this->db->get()->result();
        return $data;
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
        $this->db->select('order.id as id,order.address as address,order.notes as notes,order.applied_coupon,order.coupon_id as coupon_id,order.did as did,order.delivery_charge as delivery_charge,order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.restId as restId,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,user.cover as cover,user.first_name as first_name,user.last_name as last_name,str.name_en as str_name_en,str.name_ar as str_name_ar');
        $this->db->from("orders as order");
        $this->db->join('users as user','order.uid = user.id');
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->order_by("id", "desc");
        $this->db->limit(1000);
        $data = $this->db->get()->result();
        return $data;
    }

    public function getAllOrdersByAgent(){
        $this->db->select('order.id as id,order.address as address,order.notes as notes,order.applied_coupon,order.coupon_id as coupon_id,order.did as did,order.delivery_charge as delivery_charge,order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.restId as restId,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,user.cover as cover,user.first_name as first_name,user.last_name as last_name,str.name_en as str_name_en,str.name_ar as str_name_ar');
        $this->db->from("orders as order");
        $this->db->join('users as user','order.uid = user.id');
        $this->db->join('store as str','order.restId = str.uid');
        $where = 'str.cid = 7';
        $this->db->where($where);
        $this->db->order_by("id", "desc");
        $this->db->limit(1000);
        $data = $this->db->get()->result();
        return $data;
    }
    
    public function getByDrivers(){
        $this->db->select('order.id as id,order.address as address,order.notes as notes,order.applied_coupon,order.coupon_id as coupon_id,order.did as did,order.delivery_charge as delivery_charge,order.discount as discount,order.grand_total as grand_total,order.orders as orders,order.paid as paid,order.pay_method as pay_method,order.restId as restId,order.serviceTax as serviceTax,order.status as status,order.time as time,order.total as total,order.uid as uid,user.cover as cover,user.first_name as first_name,user.last_name as last_name,str.name_en as str_name_en,str.name_ar as str_name_ar');
        $this->db->from("orders as order");
        $this->db->where('order.did','0');
        $this->db->where('order.status','accepted');
        $this->db->join('users as user','order.uid = user.id');
        $this->db->join('store as str','order.restId = str.uid');
        $this->db->order_by("id", "asc");
        $data = $this->db->get()->result();
        return $data;
    }

    public function adminAllOrders(){
        $this->db->select('*')->from($this->table_name);
        $this->db->order_by("id", "desc");
        return $this->db->get()->result();
        return $data;
    }
    
    public function countAllOrders(){
        $this->db->select('COUNT(id) AS totalOrders')->from($this->table_name);
        return $this->db->get()->result();
        return $data;
    }

    public function saveUserLogs($data){
        $data = $this->saveLogs($data);
        return $data;
    }
}
