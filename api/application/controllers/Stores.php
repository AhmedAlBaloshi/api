<?php
  
defined('BASEPATH') OR exit('No direct script access allowed');

class Stores extends CI_Controller{
    
    public $_statusOK = 200;
    public $_statusErr = 500;

    public $_ParamMessage = 'Invalid Field';
    public $_OKmessage = 'Success';
    public $_Errmessage = 'Error';

    public $_table_column_array = ['uid','name_en','name_ar','mobile','lat','lng','verified','address_en','address_ar','descriptions_en','descriptions_ar','images','cover','cover_hr','status','is_busy','open_time','close_time','isClosed','certificate_url','certificate_type','rating','total_rating','cid','shipping_price','shipping','major_categories','minor_categories','rest_cuisines','payment_method','featured','min_order_price','notes_en','notes_ar','time'];
    public $_table_column_edit = ['id','uid','name_en','name_ar','mobile','lat','lng','verified','address_en','address_ar','descriptions_en','descriptions_ar','images','cover','cover_hr','status','is_busy','open_time','close_time','isClosed','certificate_url','certificate_type','rating','total_rating','cid','shipping_price','shipping','major_categories','minor_categories','rest_cuisines','payment_method','featured','min_order_price','notes_en','notes_ar','time'];
    public $required = ['id'];

    public $cityRequired = ['cid'];
    public function __construct(){
		parent ::__construct();
        $this->load->library('session');
        $this->load->library('json');
		$this->load->database();
        $this->load->helper('url');
        $this->load->model('Stores_model');
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
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->Stores_model->get_all();
            if($data != null){
                echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }
    
    public function getAllStoresbyAgent(){
        $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->Stores_model->getAllStoresbyAgent();
            if($data != null){
                echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    // get request
    public function getById(){
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
            }else{
                $result = $this->Stores_model->getById($_POST['id']);
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

     // get request
    public function getByUid(){
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
            }else{
                $result = $this->Stores_model->getByUid($_POST['id']);
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

    public function getByCity(){
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
            }else{
                $result = $this->Stores_model->getByCity($_POST['id']);
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
 
     public function getStoresData(){
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
            }else{
                $result = $this->Stores_model->getStoresData($_POST['id']);
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

    public function nearMe(){
        $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $required  = ['lat','lng','distance','type'];
            $data = $this->check_array_values($_POST,$required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else {
               $result = $this->Stores_model->nearMe($_POST['lat'],$_POST['lng'],$_POST['distance'],$_POST['type']);
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
    
    public function testPointsInPolygons(){
        $pointLocation = new pointLocation();
        $points = array("50 70","70 40","-20 30","100 10","-10 -10","40 -20","110 -20");
        $polygon = array("-50 30","50 70","100 50","80 10","110 -10","110 -30","-20 -50","-30 -40","10 -10","-10 10","-30 -20","-50 30");
        // The last point's coordinates must be the same as the first one's, to "close the loop"
        foreach($points as $key => $point) {
            echo "point " . ($key+1) . " ($point): " . $pointLocation->pointInPolygon($point, $polygon) . "<br>";
        }
    }
    
    
    public function editByUid(){
        $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $required = ['uid'];
            $data = $this->check_array_values($_POST,$required);
            $param = $this->check_params($_POST,$this->_table_column_edit);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if( count($param) > 0 ){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else {
                $result = $this->Stores_model->editByUid($_POST,$_POST['uid']);
                
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
    
    public function editById(){
        $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $required = ['id'];
            $data = $this->check_array_values($_POST,$required);
            $param = $this->check_params($_POST,$this->_table_column_edit);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if( count($param) > 0 ){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else {
                $result = $this->Stores_model->editById($_POST,$_POST['id']);
                
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

    public function editList(){
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
            $param = $this->check_params($_POST,$this->_table_column_edit);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if( count($param) > 0 ){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else {
                $result = $this->Stores_model->editList($_POST,$_POST['id']);
                
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

    // post request
    public function save(){
        $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->_table_column_array);
            $param = $this->check_params($_POST,$this->_table_column_array);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else if( count($param) >0 ){
                echo $this->json->response(array_values($param),$this->_ParamMessage,$this->_statusErr);
            }else{
                $result = $this->Stores_model->saveList($_POST);
                if($result != null){
                    $id = $this->db->insert_id();
                    $data = $this->Stores_model->getByIdValue($id);
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['error'=>'Something Went Wrong.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }else{
            echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        }
    }

    public function deleteList(){
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
            }else{
                $result = $this->Stores_model->deleteList($_POST['id']);
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['error'=>'Something Went Wrong.'],$this->_Errmessage,$this->_statusErr);
                }
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

    public function getChatsNames(){
        $agent = $this->input->request_headers();
    //     $saveLogInfo = array(
    //         'url' => $this->uri->uri_string(),
    //         'agent' => json_encode($agent),
    //         'datetime' => date('Y-m-d h:i:s') 
    //     );
    //   $this->Stores_model->saveUserLogs($saveLogInfo);
        $auth  = $this->input->get_request_header('Basic');
        if($auth && $auth == $this->config->item('encryption_key')){
            $data = $this->check_array_values($_POST,$this->required);
            if(isset($data) && !empty($data)){
                echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
            }else{
                $result = $this->Stores_model->getUsersNames($_POST['id']);
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
    public function getStoresIds(){

        // $agent = $this->input->request_headers();
        // $saveLogInfo = array(
        //     'url' => $this->uri->uri_string(),
        //     'agent' => json_encode($agent),
        //     'datetime' => date('Y-m-d h:i:s') 
        // );
        // $this->Stores_model->saveUserLogs($saveLogInfo);
        // $auth  = $this->input->get_request_header('Basic');
        // if($auth && $auth == $this->config->item('encryption_key')){
        //     $data = $this->check_array_values($_POST,$this->required);
        //     if(isset($data) && !empty($data)){
        //         echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        //     }else{
                $result = $this->Stores_model->getStoresIds($_POST['id']);
                if($result != null){
                    echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
                    }else{
                    echo $this->json->response(['error'=>'Something Went Wrong.'],$this->_Errmessage,$this->_statusErr);
                }
        //     }
        // }else{
        //     echo $this->json->response('No Token Found',$this->_Errmessage,$this->_statusErr);
        // }

        
    }

 
}
