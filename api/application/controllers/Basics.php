<?php
  
defined('BASEPATH') OR exit('No direct script access allowed');
include("point-in-polygon.php");
class Basics extends CI_Controller{
    
    public $_statusOK = 200;
    public $_statusErr = 500;

    public $_OKmessage = 'Success';
    public $_Errmessage = 'Error';

    public $_table_column_array = ['user_id','title','description'];
    public $_table_column_edit = ['id','user_id','title','description'];
    public $required = ['user_id'];
    public $deleteRequired = ['id'];
    public function __construct(){
		parent ::__construct();
        $this->load->library('session');
        $this->load->library('json');
		$this->load->database();
        $this->load->helper('url');
        $this->load->model('Lists_model');
        
        $this->load->model('Zones_model');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }
    
    public function index()
	{
		echo('hello');
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
    
    public function getAllZones(){
        $data = $this->Zones_model->getAllZones();
        $result = json_decode($data[0]->data, true);
        if($data != null){
            echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                echo "<br>";
            var_dump($result);
            foreach($data[0]->data as $key => $polygon) {
                echo "point " . $polygon[0]->data . "<br>";
                
            }
        }else{
            echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            
        }
    }
    
    // get request
    public function getById(){
        
        $data = $this->check_array_values($_POST,$this->required);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            $result = $this->Lists_model->getById($_POST['user_id']);
            if($result != null){
                echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }
    }

 
    public function editList(){
        
        $data = $this->check_array_values($_POST,$this->_table_column_edit);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            $result = $this->Lists_model->editList($_POST,$_POST['id']);
            
            if($result != null){
                echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response(['error'=>'something went wrong.'],$this->_Errmessage,$this->_statusErr);
            }
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
        $data = $this->check_array_values($_POST,$this->_table_column_array);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            $result = $this->Lists_model->saveList($_POST);
            if($result != null){
                $id = $this->db->insert_id();
                $data = $this->Lists_model->getByIdValue($id);
                echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response(['error'=>'Something Went Wrong.'],$this->_Errmessage,$this->_statusErr);
            }
        }
    }

    public function deleteList(){
        $data = $this->check_array_values($_POST,$this->deleteRequired);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            $result = $this->Lists_model->deleteList($_POST['id']);
            if($result != null){
                echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response(['error'=>'Something Went Wrong.'],$this->_Errmessage,$this->_statusErr);
            }
        }
    }
 
}
