<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Thawani extends CI_Controller
{
    public $_statusOK = 200;
    public $_statusErr = 500;

    public $_ParamMessage = 'Invalid Field';
    public $_OKmessage = 'Success';
    public $_Errmessage = 'Error';

    function  __construct() {
        parent ::__construct();
        $this->load->library('session');
        $this->load->library('json');
		$this->load->database();
        $this->load->helper('url');
        $this->load->helper('db_helper');
        $this->load->model('Thawani_model');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Basic");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }
    
    public function confirmPrice(){
        $uid = $_POST['uid'];
        // ***** get cart
        $cartList = $this->Thawani_model->getCartByUid($uid);
        // ***** get products
        $productsList = [];
        foreach ($cartList as $key=>$row)
        {
            $productsList[$key] = [
                'name' => get_column('products','id',$row->pid,'name_en'),
                'unit_amount' => $row->price_type == 'single' ? floor($row->price * 1000) : floor(json_decode($row->price)->price * 1000), //floor($row->price * 1000),
                'quantity' =>  $row->pqty
            ];
        }
        
        if(sizeof($productsList) > 0){
            echo $this->json->response($productsList,$this->_OKmessage,$this->_statusOK);
        }else{
            echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
        }
    }

    function index(){
        // set init data
        $uid = $_GET['uid'];
        $token = $_GET['token'];
        $shipping = $_GET['shipping'];
        // prepare request data
        // ***** start prepare
        $sessionRequestdata = new \stdClass();
        $sessionRequestdata->client_reference_id = $token;
        // ***** get cart
        $cartList = $this->Thawani_model->getCartByUid($uid);
        // ***** get products
        $productsList = [];
        foreach ($cartList as $key=>$row)
        {
            $productsList[$key] = [
                'name' => get_column('products','id',$row->pid,'name_en'),
                'unit_amount' => $row->price_type == 'single' ? floor($row->price * 1000) : floor(json_decode($row->price)->price * 1000), //floor(get_column('products','id',$row->pid,'price') * 1000),
                'quantity' =>  $row->pqty
            ];
        }
        if($shipping === "0" || $shipping === "0.00" || $shipping === "0.000"){}
        else{
          $productsList[$key + 1] = ['name'=>'Shipping','unit_amount'=>floor($shipping * 1000),'quantity'=> 1];  
        }
        $sessionRequestdata->products = $productsList;
        // ***** assign success failed links
        $sessionRequestdata->success_url = "https://jimgate.com/jim/api/payment_result/success.php";
        $sessionRequestdata->cancel_url =  "https://jimgate.com/jim/api/payment_result/fail.php";
        // ***** get user data
        $userObj = $this->Thawani_model->getUserById($uid);
        $customerData = [];
        foreach ($userObj as $key=>$row)
        {
            $customerData = array(
                'customer_email' => $row->id,
                'customer_id' => $row->email,
                'customer_first_name' => $row->first_name,
                'customer_last_name' => $row->last_name,
                'customer_phone' => $row->mobile
            );
        }
        $sessionRequestdata->metadata = $customerData;
        // create thawani session
        $thawaniSessionResponse = $this->createThawaniSession(json_encode($sessionRequestdata));
        $decodedSessionResponse = json_decode($thawaniSessionResponse);
        // save thawani session
        $sessionResponseForSaving = array(
            'code' => $decodedSessionResponse->code,
            'description' => $decodedSessionResponse->description,
            'success' => $decodedSessionResponse->success,
            'session_id' => $decodedSessionResponse->data->{"session_id"},
            'client_reference_id' => $decodedSessionResponse->data->{"client_reference_id"},
            'customer_id' => $decodedSessionResponse->data->{"customer_id"},
            'products' => json_encode($decodedSessionResponse->data->{"products"}),
            'total_amount' => $decodedSessionResponse->data->{"total_amount"},
            'currency' => $decodedSessionResponse->data->{"currency"},
            'success_url' => $decodedSessionResponse->data->{"success_url"},
            'cancel_url' => $decodedSessionResponse->data->{"cancel_url"},
            'payment_status' => $decodedSessionResponse->data->{"payment_status"},
            'invoice' => $decodedSessionResponse->data->{"invoice"},
            'save_card_on_success' => $decodedSessionResponse->data->{"save_card_on_success"},
            'metadata' => json_encode($decodedSessionResponse->data->{"metadata"}),
            'created_at' => $decodedSessionResponse->data->{"created_at"},
            'expire_at' => $decodedSessionResponse->data->{"expire_at"},
        );
        $sessionResponseForSavingResult = $this->Thawani_model->saveList($sessionResponseForSaving);

		$session_id = $decodedSessionResponse->data->session_id;

		$publishable_key = 'qjVUxrqyBzsqEtONoQufp542NZI61R';
		return redirect("https://checkout.thawani.om/pay/$session_id?key=$publishable_key");
    }

    public function createThawaniSession($sessionRequestdata) {
		$curl = curl_init();
		curl_setopt_array($curl, [
            CURLOPT_URL => "https://checkout.thawani.om/api/v1/checkout/session",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $sessionRequestdata,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "thawani-api-key: Vy78gMjtyGZW3TBXdyYS9WJvS8a45l"
            ],
		]);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			return  $response;
		}
    }

    public function getStatusByOrderId(){
        $result = get_column('thawani_sessions','client_reference_id',$_POST['orderId'],'code');
        if($result != null){
            echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
        }else{
            echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
        }
    }
    
    // test new method
    function thawaniPaymentNewMethod(){
        // set init data
        $uid = $_GET['uid'];
        $token = $_GET['token'];
        $shipping = $_GET['shipping'];
        // prepare order data
        $orderDataAfterSuccessPayment = new \stdClass();
        $orderDataAfterSuccessPayment->shipping = $shipping;
        $orderDataAfterSuccessPayment->uid = $uid;
        //$orderDataAfterSuccessPayment = json_encode($orderDataAfterSuccessPayment);
        
        // prepare request data
        // ***** start prepare
        $sessionRequestdata = new \stdClass();
        $sessionRequestdata->client_reference_id = $token;
        // ***** get cart
        $cartList = $this->Thawani_model->getCartByUid($uid);
        // ***** get products
        $productsList = [];
        foreach ($cartList as $key=>$row)
        {
            $productsList[$key] = [
                'name' => get_column('products','id',$row->pid,'name_en'),
                'unit_amount' => $row->price_type == 'single' ? floor($row->price * 1000) : floor(json_decode($row->price)->price * 1000), //floor(get_column('products','id',$row->pid,'price') * 1000),
                'quantity' =>  $row->pqty
            ];
        }
        if($shipping === "0" || $shipping === "0.00" || $shipping === "0.000"){}
        else{
          $productsList[$key + 1] = ['name'=>'Shipping','unit_amount'=>floor($shipping * 1000),'quantity'=> 1];  
        }
        $sessionRequestdata->products = $productsList;
        // ***** assign success failed links
        // $jsonData = array(
        //     "comments" => "Fresh food",
        //     "container" => false,
        //     "cookedTime" => 2,
        //     "description" => "biryani",
        //     "refridgeration" => true,
        //     "serves" => 2,
        //     "veg" => true
        // );
        
        // $orderDataAfterSuccessPayment = new \stdClass();
        // $orderDataAfterSuccessPayment->shipping = "hh";
        // $orderDataAfterSuccessPayment->uid = $uid;
        // $orderDataAfterSuccessPayment = new stdClass();
        // $orderDataAfterSuccessPayment->address = $_GET['address'];
        // $orderDataAfterSuccessPayment->applied_coupon = $_GET['applied_coupon'];
        // $orderDataAfterSuccessPayment->coupon_id = $_GET['coupon_id'];
        // $orderDataAfterSuccessPayment->pay_method = $_GET['pay_method'];
        // $orderDataAfterSuccessPayment->did = $_GET['did'];
        // $orderDataAfterSuccessPayment->delivery_charge = $_GET['delivery_charge'];
        //$orderDataAfterSuccessPayment->discount = $_GET['discount'];
        //$orderDataAfterSuccessPayment->grand_total = $_GET['grand_total'];
        // $orderDataAfterSuccessPayment->orders = $_GET['orders'];
        // $orderDataAfterSuccessPayment->paid = $_GET['paid'];
        // $orderDataAfterSuccessPayment->restId = $_GET['restId'];
        // $orderDataAfterSuccessPayment->serviceTax = $_GET['serviceTax'];
        // $orderDataAfterSuccessPayment->status = $_GET['status'];
        // $orderDataAfterSuccessPayment->time = $_GET['time'];
        // $orderDataAfterSuccessPayment->total = $_GET['total'];
        // $orderDataAfterSuccessPayment->uid = $_GET['uid'];
        // $orderDataAfterSuccessPayment->notes = $_GET['notes'];
        $sessionRequestdata->success_url = "https://jimgate.com/jim/api/payment_result/success-resp.php?json=$orderDataAfterSuccessPayment";
        $sessionRequestdata->cancel_url =  "https://jimgate.com/jim/api/payment_result/fail.php";
        // ***** get user data
        $userObj = $this->Thawani_model->getUserById($uid);
        $customerData = [];
        foreach ($userObj as $key=>$row)
        {
            $customerData = array(
                'customer_email' => $row->id,
                'customer_id' => $row->email,
                'customer_first_name' => $row->first_name,
                'customer_last_name' => $row->last_name,
                'customer_phone' => $row->mobile
            );
        }
        $sessionRequestdata->metadata = $customerData;
        // create thawani session
        $thawaniSessionResponse = $this->createThawaniSessionNewMethod(json_encode($sessionRequestdata));
        $decodedSessionResponse = json_decode($thawaniSessionResponse);
        // save thawani session
        $sessionResponseForSaving = array(
            'code' => $decodedSessionResponse->code,
            'description' => $decodedSessionResponse->description,
            'success' => $decodedSessionResponse->success,
            'session_id' => $decodedSessionResponse->data->{"session_id"},
            'client_reference_id' => $decodedSessionResponse->data->{"client_reference_id"},
            'customer_id' => $decodedSessionResponse->data->{"customer_id"},
            'products' => json_encode($decodedSessionResponse->data->{"products"}),
            'total_amount' => $decodedSessionResponse->data->{"total_amount"},
            'currency' => $decodedSessionResponse->data->{"currency"},
            'success_url' => $decodedSessionResponse->data->{"success_url"},
            'cancel_url' => $decodedSessionResponse->data->{"cancel_url"},
            'payment_status' => $decodedSessionResponse->data->{"payment_status"},
            'invoice' => $decodedSessionResponse->data->{"invoice"},
            'save_card_on_success' => $decodedSessionResponse->data->{"save_card_on_success"},
            'metadata' => json_encode($decodedSessionResponse->data->{"metadata"}),
            'created_at' => $decodedSessionResponse->data->{"created_at"},
            'expire_at' => $decodedSessionResponse->data->{"expire_at"},
        );
        //$sessionResponseForSavingResult = $this->Thawani_model->saveList($sessionResponseForSaving);

		$session_id = $decodedSessionResponse->data->session_id;

		//$publishable_key = 'qjVUxrqyBzsqEtONoQufp542NZI61R'; //for production
		//return redirect("https://checkout.thawani.om/pay/$session_id?key=$publishable_key"); //for production
		$publishable_key = 'HGvTMLDssJghr9tlN9gr4DVYt0qyBy'; // for testing
		//$returnedResult = redirect("https://uatcheckout.thawani.om/pay/$session_id?key=$publishable_key"); // for testing
		$redirect = "https://uatcheckout.thawani.om/pay/$session_id?key=$publishable_key";
        $returnedResult = header("Location: $redirect");
		return $returnedResult;
    }

    public function createThawaniSessionNewMethod($sessionRequestdata) {
		$curl = curl_init();
		curl_setopt_array($curl, [
            //CURLOPT_URL => "https://checkout.thawani.om/api/v1/checkout/session", // for production
            CURLOPT_URL => "https://uatcheckout.thawani.om/api/v1/checkout/session", // for testing
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $sessionRequestdata,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                // "thawani-api-key: Vy78gMjtyGZW3TBXdyYS9WJvS8a45l" // for production
                "thawani-api-key: rRQ26GcsZzoEhbrP2HZvLYDbn9C9et" // for testing
            ],
		]);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			return  $response;
		}
    }
}