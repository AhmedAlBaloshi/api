<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Notifications extends CI_Controller
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
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Basic");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }
    
    function index(){
        
    }
    
    public function sendFcmNotification(){
        $senderId = empty($_POST['senderId']) ? '' : $_POST['senderId'];
        $senderName = empty($_POST['senderName']) ? '' : $_POST['senderName'];
        $receiverToken = empty($_POST['receiverToken']) ? '' : $_POST['receiverToken'];
        $receiverId = empty($_POST['receiverId']) ? '' : $_POST['receiverId'];
        $receiverName = empty($_POST['receiverName']) ? '' : $_POST['receiverName'];
        $orderStatus = empty($_POST['orderStatus']) ? '' : $_POST['orderStatus'];
        $msgTitle = '';
        $msgBody = '';
        switch($_POST['msgType']){
            case 'new-order':
                $msgTitle = "طلب جديد";
                $msgBody = "لديك طلب جديد من العميل"
                           ." "
                           .$senderName;
                break;
            case 'accepted-order-by-store-for-drivers':
                $msgTitle = "طلبية جديدة";
                $msgBody = "طلبية جديدة للتوصيل";
                $receiverToken = "/topics/jimGateDrivers";
                break;
        }
        
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = 'AAAASbXS_t4:APA91bFaXtDapBtDRRDot8uA3NJU_L0CHKzHvfVdGxufOy_uqOX71RUSpmeAuR5hiZkVjf7Y_L5S_YtltgLW6czQvaPKjvmNrT49f_U1HSyElWAcvLHOmsn1Pn4vEp5go2E3OA0Oe7lZ';
        $notification = array('title' =>$msgTitle , 'body' => $msgBody, 'sound' => 'default', 'click_action' => 'FCM_PLUGIN_ACTIVITY', 'icon' => 'ic_launcher', 'badge' => '1');
        $arrayToSend = array('to' => $receiverToken, 'notification' => $notification,'priority'=>'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        //Send the request
        $response = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);
		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			return  $response;
		}
    }
}