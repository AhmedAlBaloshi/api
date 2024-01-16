<?php
  
require_once APPPATH.'/core/Main_model.php';
class Driver_model extends Main_model
{
    public $table_name = "drivers";
    public $mail_app_name = 'Jim app';
	public $mail_address = 'Muscat, Oman';
	public $mail_email = 'info@jimgate.com';
	public $mail_phone = '+968 96126613';
	public function __construct(){
		parent::__construct();
        $this->load->library('upload','encrypt');
        $this->load->helper('string');
        $this->load->library('encryption');
    }

   public function login($user_data){
        $where = "email = '".$user_data['email']."' ";
        $password = $this->get_by($this->table_name,$where,'password','row');
        if($password != null){
            if(password_verify($user_data['password'],$password->password)){
                $data = $this->get($this->table_name,$where);
                return $data;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
    
    public function sendBulk($to_address,$message,$subject){
        $this->load->library('email');
        $this->email->clear();
        $this->email->from('info@jimgate.com');
        $this->email->reply_to('noreply@jimgate.com');
        $this->email->to($to_address);
        $this->email->subject($subject);
        $this->email->set_mailtype("html");
        $this->email->message($message);
        if($this->email->send()===TRUE){
            return true;
        }
        return false;
    }

    public function sendMails($emails,$message,$subject){
        foreach($emails as $victim){
            $this->sendBulk($victim,$message,$subject);
        }
        return true;
    }

    public function verificationLink($email,$url){
        $this->load->library('email');
        $this->email->clear();
        $this->email->from('info@jimgate.com');
        $this->email->reply_to('noreply@jimgate.com');
        $this->email->to($email);
        $this->email->subject('Verification Link');
        $this->email->set_mailtype("html");
        // 
          $data = array(
                'url' => $url,
            );
        $msg = $this->load->view('Email',$data,true);

        $this->email->message($msg);
        if($this->email->send()===TRUE){
            return true;
        }
        return false;
    }
    
     public function get_admin(){
        $where = "type = 'admin' ";
        $data = $this->get($this->table_name,$where);
        return $data;
    }

     public function getById($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_name,$where,'results');
        return $data;
    }
    
    public function editById($data,$id){
        $where = "id = ".$id;
        return $this->update($this->table_name,$data,$where);
    }

     public function LoginWithPhoneAndPassword($user_data){
        $where = "mobile = '".$user_data['mobile']."' AND country_code = '".$user_data['cc']."'";
        $password = $this->get_by($this->table_name,$where,'password','row');
        if($password != null){
            if(password_verify($user_data['password'],$password->password)){
                $data = $this->get($this->table_name,$where);
                return $data;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

    public function checkMobileNumber($user_data){
        $where = "mobile = '".$user_data['mobile']."' AND country_code = '".$user_data['cc']."'";
        $password = $this->get_by($this->table_name,$where,'id','row');
        if($password != null){
            return $password;
        }else{
            return null;
        }
    }

    public function geyByCity($id){
        $where = 'city = '.$id;
        $data = $this->get($this->table_name,$where,'results');
        return $data;
    }

    public function get_all_users(){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $this->db->order_by('id', 'desc');
        $data = $this->db->get()->result();
        return $data;
    }

    public function changeDriver($post){
        // return $data;
        $where = "id = ".$post['id'];
        $data = ['did' => $post['selectedDriver'], 'status'=> 'changed_driver_to_another'];
        $this->update('orders',$data,$where);

        $data = [
            'order_id' => $post['id'],
            'status' => 'changed_driver_to_another',
            'reason' => $post['reason']
    ];
        $track = $this->insert('order_track',$data);
        return true;
       
    }

    public function getAllDriversByAgent(){
        $where = 'city = 7';
        $data = $this->get($this->table_name, $where);
        return $data;
    }

    public function getUsers(){
        $where = "type = 'user'";
        $data = $this->get($this->table_name,$where,'results');
        return $data;
    }

    public function allEmails(){
        $sql = "SELECT group_concat(email separator ',') as email FROM `users`";
        $query = $this->db->query($sql);
        $array1 = $query->row_array();
        $arr = explode(',',$array1['email']);
        return $arr;
    }

    public function get_user_by_id($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_name,$where);
        return $data;
    }

    public function save_users($data){
        $data['password'] = password_hash($data['password'],PASSWORD_BCRYPT);
        return $this->insert($this->table_name,$data);
    }

    public function edit_users($data,$id){
        $where = "id = ".$id;
        return $this->update($this->table_name,$data,$where);
    }

    public function verify($id){
        $where = "id = ".$id;
        $data = [
            'verified' => '1'
        ];
        return $this->update($this->table_name,$data,$where);
    }    
    

    public function update_latlng($data,$id){
        $values = [
            'lat' => $data['lat'],
            'lng' => $data['lng'],
        ];
        $where = "id = ".$id;
        return $this->update($this->table_name,$values,$where);
    }

    public function update_fcm($data,$id){
        $values = [
            'fcm_token' => $data['fcm_token'],
        ];
        $where = "id = ".$id;
        return $this->update($this->table_name,$values,$where);
    }

    public function update_status($data,$id){
        $values = [
            'status' => $data['status'],
        ];
        $where = "id = ".$id;
        return $this->update($this->table_name,$values,$where);
    }
    
    public function update_verified($data,$id){
        $values = [
            'verified' => $data['verified'],
        ];
        $where = "id = ".$id;
        return $this->update($this->table_name,$values,$where);
    }
    
   public function update_password($data,$id){
        $where = 'email ="'.$id.'"';
        $values = [
            'password' => password_hash($data,PASSWORD_BCRYPT)
        ];
        return $this->update($this->table_name,$values,$where);
    }

    public function resetPasswordWithPhone($data,$id){
        $where = "mobile = ".$id;
        $values = [
            'password' => password_hash($data,PASSWORD_BCRYPT)
        ];
        return $this->update($this->table_name,$values,$where);
    }

    public function phoneExist($mobile){
        $where = 'mobile ="'.$mobile.'"';
        $data = $this->get($this->table_name,$where);
        if($data != null){
            return true;
        }else{
            return false;
        }
    }
    
    public function delete_users($id){
        $where = "id =".$id;
        return $this->delete($this->table_name,$where);
    }

    public function email_exists($email){
        $where = 'email ="'.$email.'"';
        $data = $this->get($this->table_name,$where);
        if($data != null){
            return true;
        }else{
            return false;
        }
    }

    public function saveOTP($email){
         $otp = $this->random_number();
         $values = [
            'otp' => $otp,
            'email' => $email,
            'status' => 0
        ];
        $info = $this->insert('otp',$values);
        if($info != null){
            $this->load->library('email');
            $this->email->clear();
            $this->email->from('info@jimgate.com');
            $this->email->reply_to('noreply@jimgate.com');
            $this->email->to($email);
            $this->email->subject('Email Verification');
            $this->email->set_mailtype("html");
            // 
            $data = array(
                'code' => $otp,
                'address' => $this->mail_address,
                'name' => $this->mail_app_name,
            );
            $msg = $this->load->view('Reset',$data,true);

            $this->email->message($msg);
            $this->email->send();
            $id = $this->db->insert_id();
            $returnValue = [
                'code' => $otp,
                'id' =>$id
            ];
            return $returnValue;
        }else{
            return $info;
        }
    }

    public function upload_user_file($file){
        return $this->upload_file($file);
    }
    
    public function saveUserLogs($data){
        $data = $this->saveLogs($data);
        return $data;
    }

    function random_number($maxlength = 5) {
        $chary = array(
                        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
                        );
        $return_str = "";
        for ( $x=0; $x<=$maxlength; $x++ ) {
            $return_str .= $chary[rand(0, count($chary)-1)];
        }
        return $return_str;
    }

    public function getDriversData($ids){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $storeIds = explode(',',$ids);
        $this->db->where_in('id',$storeIds);
        $data = $this->db->get()->result();
        return $data;
    }

     public function getUsersNames($ids){
        $this->db->select('*');
        $this->db->from($this->table_name);
        $uid = explode(',',$ids);
        $this->db->where_in('id',$uid);
        return $this->db->get()->result();
    }
}