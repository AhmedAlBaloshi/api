<?php
  
defined('BASEPATH') OR exit('No direct script access allowed');
include("point-in-polygon.php");

class Drivers extends CI_Controller{
    
    public $_statusOK = 200;
    public $_statusErr = 500;

    public $_OKmessage = 'Success';
    public $_Errmessage = 'Error';
    public $_ParamMessage = 'Invalid Field';
    public $_table_column_array = ['first_name','last_name','email','password','gender','city','current','address','fcm_token','lat','lng','cover','mobile','status','others','rating','total_rating','date','last_active','language','zone','country_code'];
    public $_table_column_edit = ['id','first_name','last_name','email','password','gender','city','current','address','fcm_token','lat','lng','cover','mobile','status','others','rating','total_rating','date','last_active','language','zone','country_code'];
    public $_table_login_array = ['email','password'];

    public $required = ['id'];
    public $phonePasswordArray = ['cc','mobile','password'];
    public function __construct(){
		parent ::__construct();
        $this->load->library('session');
        $this->load->library('json');
		$this->load->database();
        $this->load->helper('url');
        $this->load->model('Driver_model');
        $this->load->model('Order_model');
        $this->load->model('Zones_model');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Basic");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }
 
    public function index(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
             $data = $this->Driver_model->get_all_users();
                foreach($data as $keys){
                    $keys->password = null;
                }
                if($data != null){
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function changeDriver(){
        $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else {
                $result = $this->Driver_model->changeDriver($_POST);
                
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['error'=>'something went wrong.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }
 
    public function getAllDriversByAgent(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
             $data = $this->Driver_model->getAllDriversByAgent();
                foreach($data as $keys){
                    $keys->password = null;
                }
                if($data != null){
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function getUsers(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
             $data = $this->Driver_model->getUsers();
                foreach($data as $keys){
                    $keys->password = null;
                }
                if($data != null){
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }
    
    public function testPointsInPolygons(){
        $pointLocation = new pointLocation();
        // ***** get orders and zones
        $ordersList = $this->Order_model->getByDriverIdWithAddress($_POST['id']);
        $zonesList = $this->Zones_model->getAllGreenZones();
        // ***** get products
        $filteredList = [];
        $totalDeliveryAmount = 0;
        $totalCalculatedOrders = 0;
        foreach ($ordersList as $orderKey=>$orderRow)
        {
            foreach ($zonesList as $zoneKey=>$zoneRow)
            {
                // actual function of pointInPolygon() accept location in (lat, lng) but we are passing (lng, lat) and then we reverse it back in 'pointLocation' class in 'pointStringToCoordinates' function
                $point = $orderRow->customer_loc_lng.' '.$orderRow->customer_loc_lat;
                
                // here we are removing the commas from lng,lat and sqaure brakets
                // input: [[[lng,lat],[lng,lat]]]
                // output: ["lng lat","lng lat"]
                $tempCoordinates = str_replace(",", " ", $zoneRow->coordinates);
                $tempCoordinates = str_replace("[[[", "", $tempCoordinates);
                $tempCoordinates = str_replace("]]]", "", $tempCoordinates);
                $polygon = explode("] [", $tempCoordinates);
                
                // check if the location point is inside the zone and then get the price of delivery of that zone and do summation
                if($pointLocation->pointInPolygon($point, $polygon) == 'inside'){
                    $totalDeliveryAmount += $zoneRow->drivers_shipping_price;
                    $totalCalculatedOrders += 1;
                    $filteredList[$orderKey] = array(
                        'uid' => $orderRow->uid,
                        'address' => $orderRow->address,
                        'did' => $orderRow->did,
                        'restId' => $orderRow->restId,
                        'orderId' => $orderRow->orderId,
                        'delivery_charge' => $orderRow->delivery_charge,
                        'grand_total' => $orderRow->grand_total,
                        'paid' => $orderRow->paid,
                        'status' => $orderRow->status,
                        'total' => $orderRow->total,
                        'customer_loc_lat' => $orderRow->customer_loc_lat,
                        'customer_loc_lng' => $orderRow->customer_loc_lng,
                        'customer_loc' => $orderRow->customer_loc_lng,
                        'zone_coordinates' => $point,
                        'zone_shipping_price' => $zoneRow->drivers_shipping_price,
                        'customer_address_location' => $pointLocation->pointInPolygon($point, $polygon)
                    );
                }
            }
        }
        
        $finalOutput = array(
            'totalDeliveryAmount' => number_format($totalDeliveryAmount, 3),
            'totalCalculatedOrders' => $totalCalculatedOrders
        );
        
        echo $this->json->response($finalOutput,$this->_OKmessage,$this->_statusOK);
        
        // if($filteredList != null){
        //     echo $this->json->response($filteredList,$this->_OKmessage,$this->_statusOK);
        // }else{
        //     echo $this->json->response(['error'=>'something went wrong.'],$this->_Errmessage,$this->_statusErr);
        // }
        
        
        // $pointLocation = new pointLocation();
        // $points = array("50 70","70 40","-20 30","100 10","-10 -10","40 -20","110 -20");
        // $polygon = array("-50 30","50 70","100 50","80 10","110 -10","110 -30","-20 -50","-30 -40","10 -10","-10 10","-30 -20","-50 30");
        // // The last point's coordinates must be the same as the first one's, to "close the loop"
        // $data = "";
        // foreach($points as $key => $point) {
        //     $data = $data . "point " . ($key+1) . " ($point): " . $pointLocation->pointInPolygon($point, $polygon) . "<br>";
        // }
        // echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
    }
    
    public function getDriverStat(){
        $pointLocation = new pointLocation();
        // ***** get orders and zones
        $ordersList = $this->Order_model->getByDriverIdWithAddressForStat($_POST);
        $zonesList = $this->Zones_model->getAllGreenZones();
        // ***** get products
        $filteredList = [];
        $totalDeliveryAmount = 0;
        $totalOrdersAmount = 0;
        $totalOrdersAmountWithoutDelivery = 0;
        $totalCashOrdersAmount = 0;
        $totalCashOrdersAmountWithoutDelivery = 0;
        $totalCalculatedOrders = 0;
        foreach ($ordersList as $orderKey=>$orderRow)
        {
            foreach ($zonesList as $zoneKey=>$zoneRow)
            {
                // actual function of pointInPolygon() accept location in (lat, lng) but we are passing (lng, lat) and then we reverse it back in 'pointLocation' class in 'pointStringToCoordinates' function
                $point = $orderRow->customer_loc_lng.' '.$orderRow->customer_loc_lat;
                
                // here we are removing the commas from lng,lat and sqaure brakets
                // input: [[[lng,lat],[lng,lat]]]
                // output: ["lng lat","lng lat"]
                $tempCoordinates = str_replace(",", " ", $zoneRow->coordinates);
                $tempCoordinates = str_replace("[[[", "", $tempCoordinates);
                $tempCoordinates = str_replace("]]]", "", $tempCoordinates);
                $polygon = explode("] [", $tempCoordinates);
                
                // check if the location point is inside the zone and then get the price of delivery of that zone and do summation
                // $totalDeliveryAmount += $zoneRow->drivers_shipping_price;
                // $totalCalculatedOrders += 1;
                // $filteredList[] = (object) [
                //     'uid' => $orderRow->uid,
                //     'address' => $orderRow->address,
                //     'did' => $orderRow->did,
                //     'restId' => $orderRow->restId,
                //     'orderId' => $orderRow->orderId,
                //     'delivery_charge' => $orderRow->delivery_charge,
                //     'grand_total' => $orderRow->grand_total,
                //     'paid' => $orderRow->paid,
                //     'status' => $orderRow->status,
                //     'total' => $orderRow->total,
                //     'time' => $orderRow->time,
                //     'pay_method' => $orderRow->pay_method,
                //     'paid_to_driver' => $orderRow->paid_to_driver,
                //     'paid_to_driver_time' => $orderRow->paid_to_driver_time,
                //     'customer_adrs_en' => $orderRow->customer_adrs_en,
                //     'customer_adrs_ar' => $orderRow->customer_adrs_ar,
                //     'customer_loc_lat' => $orderRow->customer_loc_lat,
                //     'customer_loc_lng' => $orderRow->customer_loc_lng,
                //     'customer_loc' => $orderRow->customer_loc_lng,
                //     'str_name_en' => $orderRow->str_name_en,
                //     'str_name_ar' => $orderRow->str_name_ar,
                //     'zone_coordinates' => $point,
                //     'zone_polygon' => $polygon,
                //     'zone_shipping_price' => $zoneRow->drivers_shipping_price,
                //     'customer_address_location' => $pointLocation->pointInPolygon($point, $polygon)
                //     ];
                if($pointLocation->pointInPolygon($point, $polygon) == 'inside'){
                    $totalDeliveryAmount += $zoneRow->drivers_shipping_price;
                    $totalOrdersAmount += $orderRow->grand_total;
                    $totalOrdersAmountWithoutDelivery += $orderRow->total;
                    $totalCalculatedOrders += 1;
                    if($orderRow->pay_method == 'Cash') $totalCashOrdersAmount += $orderRow->grand_total;
                    if($orderRow->pay_method == 'Cash') $totalCashOrdersAmountWithoutDelivery += $orderRow->total;
                    $filteredList[] = (object) [
                        'uid' => $orderRow->uid,
                        'address' => $orderRow->address,
                        'did' => $orderRow->did,
                        'restId' => $orderRow->restId,
                        'orderId' => $orderRow->orderId,
                        'delivery_charge' => $orderRow->delivery_charge,
                        'grand_total' => $orderRow->grand_total,
                        'paid' => $orderRow->paid,
                        'status' => $orderRow->status,
                        'total' => $orderRow->total,
                        'time' => $orderRow->time,
                        'pay_method' => $orderRow->pay_method,
                        'paid_to_driver' => $orderRow->paid_to_driver,
                        'paid_to_driver_time' => $orderRow->paid_to_driver_time,
                        'customer_adrs_en' => $orderRow->customer_adrs_en,
                        'customer_adrs_ar' => $orderRow->customer_adrs_ar,
                        'customer_loc_lat' => $orderRow->customer_loc_lat,
                        'customer_loc_lng' => $orderRow->customer_loc_lng,
                        'customer_loc' => $orderRow->customer_loc_lng,
                        'str_name_en' => $orderRow->str_name_en,
                        'str_name_ar' => $orderRow->str_name_ar,
                        'zone_coordinates' => $point,
                        'zone_shipping_price' => $zoneRow->drivers_shipping_price,
                        'customer_address_location' => $pointLocation->pointInPolygon($point, $polygon)
                        ];
                    // $filteredList[$orderKey] = array(
                    //     'uid' => $orderRow->uid,
                    //     'address' => $orderRow->address,
                    //     'did' => $orderRow->did,
                    //     'restId' => $orderRow->restId,
                    //     'orderId' => $orderRow->orderId,
                    //     'delivery_charge' => $orderRow->delivery_charge,
                    //     'grand_total' => $orderRow->grand_total,
                    //     'paid' => $orderRow->paid,
                    //     'status' => $orderRow->status,
                    //     'total' => $orderRow->total,
                    //     'time' => $orderRow->time,
                    //     'pay_method' => $orderRow->pay_method,
                    //     'paid_to_driver' => $orderRow->paid_to_driver,
                    //     'paid_to_driver_time' => $orderRow->paid_to_driver_time,
                    //     'customer_adrs_en' => $orderRow->customer_adrs_en,
                    //     'customer_adrs_ar' => $orderRow->customer_adrs_ar,
                    //     'customer_loc_lat' => $orderRow->customer_loc_lat,
                    //     'customer_loc_lng' => $orderRow->customer_loc_lng,
                    //     'customer_loc' => $orderRow->customer_loc_lng,
                    //     'str_name_en' => $orderRow->str_name_en,
                    //     'str_name_ar' => $orderRow->str_name_ar,
                    //     'zone_coordinates' => $point,
                    //     'zone_shipping_price' => $zoneRow->drivers_shipping_price,
                    //     'customer_address_location' => $pointLocation->pointInPolygon($point, $polygon)
                    // );
                }
            }
        }
        
        $finalOutput = array(
            'totalDeliveryAmount' => number_format($totalDeliveryAmount, 3),
            'totalOrdersAmount' => number_format($totalOrdersAmount, 3),
            'totalOrdersAmountWithoutDelivery' => number_format($totalOrdersAmountWithoutDelivery, 3),
            'totalCashOrdersAmount' => number_format($totalCashOrdersAmount, 3),
            'totalCashOrdersAmountWithoutDelivery' => number_format($totalCashOrdersAmountWithoutDelivery, 3),
            'totalCalculatedOrders' => $totalCalculatedOrders,
            'calculatedOrdersList' => $filteredList
        );
        
        echo $this->json->response($finalOutput,$this->_OKmessage,$this->_statusOK);
    }
    
    public function getDriverStatWhereAllOrders(){
        $pointLocation = new pointLocation();
        // ***** get orders
        $ordersList = $this->Order_model->getByDriverIdWithAddressForStatForAllStatus($_POST);
        // ***** get products
        $filteredList = [];
        $totalDeliveryAmount = 0;
        $totalOrdersAmount = 0;
        $totalOrdersAmountWithoutDelivery = 0;
        $totalCashOrdersAmount = 0;
        $totalCashOrdersAmountWithoutDelivery = 0;
        $totalCalculatedOrders = 0;
        foreach ($ordersList as $orderKey=>$orderRow)
        {
            $totalOrdersAmount += $orderRow->grand_total;
            $totalOrdersAmountWithoutDelivery += $orderRow->total;
            $totalCalculatedOrders += 1;
            if($orderRow->pay_method == 'Cash') $totalCashOrdersAmount += $orderRow->grand_total;
            if($orderRow->pay_method == 'Cash') $totalCashOrdersAmountWithoutDelivery += $orderRow->total;
            $filteredList[] = (object) [
                'uid' => $orderRow->uid,
                'address' => $orderRow->address,
                'did' => $orderRow->did,
                'restId' => $orderRow->restId,
                'orderId' => $orderRow->orderId,
                'delivery_charge' => $orderRow->delivery_charge,
                'grand_total' => $orderRow->grand_total,
                'paid' => $orderRow->paid,
                'status' => $orderRow->status,
                'total' => $orderRow->total,
                'time' => $orderRow->time,
                'pay_method' => $orderRow->pay_method,
                'paid_to_driver' => $orderRow->paid_to_driver,
                'paid_to_driver_time' => $orderRow->paid_to_driver_time,
                'customer_adrs_en' => $orderRow->customer_adrs_en,
                'customer_adrs_ar' => $orderRow->customer_adrs_ar,
                'customer_loc_lat' => $orderRow->customer_loc_lat,
                'customer_loc_lng' => $orderRow->customer_loc_lng,
                'customer_loc' => $orderRow->customer_loc_lng,
                'str_name_en' => $orderRow->str_name_en,
                'str_name_ar' => $orderRow->str_name_ar
            ];
        }
        
        $finalOutput = array(
            'totalDeliveryAmount' => number_format($totalDeliveryAmount, 3),
            'totalOrdersAmount' => number_format($totalOrdersAmount, 3),
            'totalOrdersAmountWithoutDelivery' => number_format($totalOrdersAmountWithoutDelivery, 3),
            'totalCashOrdersAmount' => number_format($totalCashOrdersAmount, 3),
            'totalCashOrdersAmountWithoutDelivery' => number_format($totalCashOrdersAmountWithoutDelivery, 3),
            'totalCalculatedOrders' => $totalCalculatedOrders,
            'calculatedOrdersList' => $filteredList
        );
        
        echo $this->json->response($finalOutput,$this->_OKmessage,$this->_statusOK);
    }

    public function sendEmail(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
      $this->Driver_model->sendMails();
    }

    public function adminEmails(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
             $data = $this->Driver_model->allEmails();
                if($data != null){
                    $send = $this->Driver_model->sendMails($data,$_POST['message'],$_POST['subject']);
                    echo $this->json->response($send,$this->_OKmessage,$this->_statusOK);
                }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function loginWithPhoneAndPassword(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->phonePasswordArray);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $data = $this->Driver_model->LoginWithPhoneAndPassword($_POST);
                if($data != null){
                    unset($data->password);
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['message'=>'invalid phone number /password.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

     public function checkMobileNumber(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $required = ['mobile','cc'];
            $data = $this->check_array_values($_POST,$required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $data = $this->Driver_model->checkMobileNumber($_POST);
                if($data != null){
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['message'=>'invalid phone number'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function login(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->_table_login_array);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $data = $this->Driver_model->login($_POST);
                if($data != null){
                    unset($data->password);
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['message'=>'invalid email/password.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }
    
    // get request
    public function get_by_id($id){
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            if(isset($id) && !empty($id)){
                $result = $this->Driver_model->get_user_by_id($id);
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
                }
            }else{
                echo $this->json->response('please add id into url.',$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }
    
    public function editById(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            $param = $this->check_params($_POST,$this->_table_column_edit);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if(count($param) >0){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->editById($_POST,$_POST['id']);
                
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['error'=>'something went wrong.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function get_admin(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $result = $this->Driver_model->get_admin();
            if($result != null){
                unset($result->password);
                echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function check_params($data,$array_compare){
         $items = array();
          foreach($data as $key=>$value){
              $items[] = $key;
           }
           $result=array_diff($items,$array_compare);
           return $result;
    }

    // get request
    public function getById(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->getById($_POST['id']);
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

     public function geyByCity(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->geyByCity($_POST['id']);
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    // post request
    public function registerUser(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->_table_column_array);
            $param = $this->check_params($_POST,$this->_table_column_array);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if(count($param) >0 ){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->email_exists($_POST['email']);
                if($result != null){
                    echo $this->json->response(['message'=>'Email Already Register in Database'],$this->_Errmessage,$this->_statusErr);
                }else{
                    $result = $this->Driver_model->save_users($_POST);
                    if($result != null){
                        $id = $this->db->insert_id();
                        $data = $this->Driver_model->get_user_by_id($id);
                        unset($data->password);
                        echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                    }else{
                        echo $this->json->response(['error'=>'Something Went Wrong.'],$this->_Errmessage,$this->_statusErr);
                    }
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function sendOTP(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $required = ['email'];
            $data = $this->check_array_values($_POST,$required);
            $param = $this->check_params($_POST,$required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if(count($param) >0 ){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->email_exists($_POST['email']);
                if($result != null){
                    $data = $this->Driver_model->saveOTP($_POST['email']);
                    if($data != null){
                        $data['code'] = null;
                        echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                    }else{
                        echo $this->json->response(['message'=>'Something went wrong'],$this->_Errmessage,$this->_statusErr);
                    }
                }else{
                    echo $this->json->response(['message'=>'Email not exist in Database'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function edit_profile(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            $param = $this->check_params($_POST,$this->_table_column_edit);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if(count($param) >0){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->edit_users($_POST,$_POST['id']);
                
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['error'=>'something went wrong.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function check_array_values($array,$table_array){
        if(isset($array) && !empty($array)){
            $keys = [];
            foreach($array as $key => $value){
                array_push($keys,$key);
            }
            $data = array_diff($table_array,$keys);
            if(isset($data) && !empty($data)){
                $result = [ 
                    'Error_message' => "your post request mising some data.",
                    'Missing_data' => array_values($data)
                ];
                return $result;
            }else{
                return [];
            }
        }else{
            $result = [
                'Error_message' => "your post request is empty.",
                'Missing_data' => $table_array
            ];
            return $result;
        }
    }

    public function upload_file(){
        $auth  = $this->input->get_request_header('Basic');
            if($auth && $auth == $this->config->item('encryption_key')){
                $this->_table_column_array = ['img','type'];
                $data = $this->check_array_values($_POST,$this->_table_column_array);
                if(isset($data) && !empty($data)){
                    echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
                }else{
                    define('UPLOAD_DIR', 'uploads/');
                    $img = $_POST['img'];
                    $img = str_replace('data:image/'.$_POST['type'].';base64,', '', $img);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $fileName = uniqid() . '.'.$_POST['type'];
                    $file = UPLOAD_DIR . $fileName;
                    $success = file_put_contents($file, $data);
                    $data = $success ? $fileName : null;
                    if($data != null){
                        echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                    }else{
                        echo $this->json->response(['message'=>'Something wrong with your base64.'],$this->_Errmessage,$this->_statusErr);
                    }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function verify(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
       if(isset($_GET['uid']) && !empty($_GET['uid'])){
            $result = $this->Driver_model->verify($_GET['uid']);
            if($result != null){
                $this->load->view('Verify');
            }else{
                echo 'Something went wrong';
            }
        } else {
            echo "App is empty";
        }
                
    }

    public function sendVerificationMail(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $input  = ['email','url'];
            $param = $this->check_array_values($_POST,$input);
            if(isset($param) && !empty($param)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
                }else if(count($param) >0){
                    echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
                }else{
                    $data = $this->Driver_model->verificationLink($_POST['email'],$_POST['url']);
                    if($data != null && $data == TRUE){
                        echo $data;
                        // echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                    }else{
                        echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
                    }
                }
            }else{
                echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
        
    }

    public function update_password(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $this->_table_column_array = ['email','pwd'];
            $data = $this->check_array_values($_POST,$this->_table_column_array);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $data = $this->Driver_model->update_password($_POST['pwd'],$_POST['email']);
                if($data != null){
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['message'=>'Old password does not match with database.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function resetPasswordWithPhone(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $this->_table_column_array = ['phone','pwd'];
            $data = $this->check_array_values($_POST,$this->_table_column_array);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $data = $this->Driver_model->resetPasswordWithPhone($_POST['pwd'],$_POST['phone']);
                if($data != null){
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['message'=>'Old password does not match with database.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

      public function validatePhoneForReset(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $required =['phone'];
            $data = $this->check_array_values($_POST,$required);
            $param = $this->check_params($_POST,$required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if(count($param) >0 ){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else{
                $data = $this->Driver_model->phoneExist($_POST['phone']);
                if($data != null){
                    echo $this->json->response(true,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['message'=>'Mobile Phone is not register in Database'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }  
    }

    public function upload_image(){
         $this->load->helper(array('file','directory'));
         $path="./uploads/";
            if( !is_dir($path) ) {
                mkdir($path);
            }
            $config['upload_path']          = $path;
            $config['allowed_types']        = '*';
            // $config['overwrite']=TRUE;

            // $config['max_size']             = 8000;
            // $config['max_width']            = 1024;
            // $config['max_height']           = 768;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('userfile'))
            {
                $error = array('error' => $this->upload->display_errors());
                echo $this->json->response($error,$this->_Errmessage,$this->_statusErr);
            }
            else
            {
                // $data = array('upload_data' => $this->upload->data());
                $data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
                $data['file_name'];
                echo $this->json->response($data['file_name'],$this->_OKmessage,$this->_statusOK);
            // $this->load->view('upload_success', $data);
            }
    }
    
    public function logs(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
       
    }

    public function getDriversData(){
        $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->getDriversData($_POST['id']);
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function getChatsNames(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Driver_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $result = $this->Driver_model->getUsersNames($_POST['id']);
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }
 
}
