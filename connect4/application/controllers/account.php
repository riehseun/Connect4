<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';

class Account extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	session_start();
    }
        
    public function _remap($method, $params = array()) {
	    	// enforce access control to protected functions	

    		$protected = array('updatePasswordForm','updatePassword','index','logout');
    		
    		if (in_array($method,$protected) && !isset($_SESSION['user']))
   			    redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
            else if( !in_array($method,$protected) && isset($_SESSION['user']))
                redirect('arcade/index', 'refresh');
 	    	
	    	return call_user_func_array(array($this, $method), $params);
    }
          
    function loadview($title,$view_name,$data) {
        $data['title'] = $title;
        $data['main'] = $view_name;
        if (isset($_SESSION['login'])) {
          $data['user'] = $_SESSION['user'];
        }
        $this->load->view('template.php',$data);
    }
    
    function loginForm() {
    		$this->loadview('Login','account/loginForm',[]);
    }
    
    function login() {
    		$this->load->library('form_validation');
    		$this->form_validation->set_rules('username', 'Username', 'required');
    		$this->form_validation->set_rules('password', 'Password', 'required');

    		if ($this->form_validation->run() == FALSE)
    		{
    		    $this->loadview('Login','account/loginForm',[]);
    		}
    		else
    		{
    			$login = $this->input->post('username');
    			$clearPassword = $this->input->post('password');
    			 
    			$this->load->model('user_model');
    		
    			$user = $this->user_model->get($login);
    			 
    			if (isset($user) && $user->comparePassword($clearPassword)) {
    				$_SESSION['user'] = $user;
    				$data['user']=$user;
    				
                    if( $user->user_status_id == User::OFFLINE )
                    {
    				    $this->user_model->updateStatus($user->id, User::AVAILABLE);
                        redirect('arcade/index', 'refresh'); //redirect to the main application page
                    }
                    else if( $user->user_status_id == User::WAITING ||
                        $user->user_status_id == User::PLAYING )
                    {
                        redirect('board/index','refresh');
                    }
                    else
                    {
                        redirect('arcade/index', 'refresh'); //redirect to the main application page
                    }
    			}
 			else {   			
				$data['errorMsg']='Incorrect username or password!';
 				$this->loadview('Login','account/loginForm',$data);
 			}
    		}
    }

    function logout() {
		$user = $_SESSION['user'];
        $this->load->model('user_model');
        $this->load->model('invite_model');
        $this->load->model('match_model');

        $this->db->trans_begin();
        
        $user = $this->user_model->getExclusive($user->login);
        $invite = $this->invite_model->getExclusive($user->invite_id);
        $match = $this->match_model->getExclusive($user->match_id);

        //echo(json_encode(array('user'=>$user,'invite'=>$invite,'match'=>$match)));
        if( isset($invite) && $invite->invite_status_id == Invite::PENDING)
        {//consider move this code to another place. it is similar to decline and accpetInvitation.
            $this->invite_model->updateStatus($invite->id,Invite::REJECTED);
        }

        if( isset($match) && $match->match_status_id == Match::ACTIVE )
        {//consider move this code to another place. it is similar as give up on board.php
            if( $user->id == $match->user1_id )
            {
                $match->match_status_id = Match::U2WON;
            } 
            else
            {
                $match->match_status_id = Match::U1WON;
            }
            
            $this->match_model->updateStatus($match->id,$match->match_status_id);
            $this->user_model->updateMatchNull($user->id);
            //for another user on the game, it have to be cleared on getBoardStatus.
        }

        $this->user_model->updateInvitationNull($user->id);
        $this->user_model->updateMatchNull($user->id);

        $this->user_model->updateStatus($user->id, User::OFFLINE);

        if( $this->db->trans_status() === FALSE )
            $this->db->trans_rollback();
        else
            $this->db->trans_commit();

        session_destroy();
        redirect('account/index', 'refresh'); //Then we redirect to the index page again
    }

    function newForm() {
	    	$this->loadview('Sign Up','account/newForm',[]);
    }
    
    function createNew() {
    		$this->load->library('form_validation');
    	    $this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.login]');
	    	$this->form_validation->set_rules('password', 'Password', 'required');
	    	$this->form_validation->set_rules('first', 'First', "required");
	    	$this->form_validation->set_rules('last', 'last', "required");
	    	$this->form_validation->set_rules('email', 'Email', "required|is_unique[user.email]|valid_email");
            $this->form_validation->set_rules('capcha','Capcha',"required|callback_check_capcha");
	    
            if ($this->form_validation->run() == FALSE)
	    	{
	    		$this->loadview('Sign Up','account/newForm',[]);
	    	}
	    	else  
	    	{
	    		$user = new User();
	    		 
	    		$user->login = $this->input->post('username');
	    		$user->first = $this->input->post('first');
	    		$user->last = $this->input->post('last');
	    		$clearPassword = $this->input->post('password');
	    		$user->encryptPassword($clearPassword);
	    		$user->email = $this->input->post('email');
	    		
	    		$this->load->model('user_model');
	    		 
	    		
	    		$error = $this->user_model->insert($user);
	    		
                redirect('/account/loginForm');
	    	}
    }
    function check_capcha($capcha){
        $securimage = new Securimage();
        if($securimage->check($capcha) == false) {
            $this->form_validation->set_message('check_capcha','Not valid capcha');
            return false;
        }
        return true;
    }

    
    function updatePasswordForm() {
	    	$this->loadview('Change Password','account/updatePasswordForm',[]);
    }
    
    function updatePassword() {
	    	$this->load->library('form_validation');
	    	$this->form_validation->set_rules('oldPassword', 'Old Password', 'required');
	    	$this->form_validation->set_rules('newPassword', 'New Password', 'required');
	    	 
	    	 
	    	if ($this->form_validation->run() == FALSE)
	    	{
	    	    $this->loadview('Change Password','account/updatePasswordForm',[]);
	    	}
	    	else
	    	{
	    		$user = $_SESSION['user'];
	    		
	    		$oldPassword = $this->input->post('oldPassword');
	    		$newPassword = $this->input->post('newPassword');
	    		 
	    		if ($user->comparePassword($oldPassword)) {
	    			$user->encryptPassword($newPassword);
	    			$this->load->model('user_model');
	    			$this->user_model->updatePassword($user);
	    			redirect('arcade/index', 'refresh'); //Then we redirect to the index page again
	    		}
	    		else {
	    			$data['errorMsg']="Incorrect password!";
	    			$this->loadview('Change Password','account/updatePasswordForm',$data);
	    		}
	    	}
    }
    
    function recoverPasswordForm() {
    		$this->loadview('Recover Password','account/recoverPasswordForm',[]);
    }
    
    function recoverPassword() {
	    	$this->load->library('form_validation');
	    	$this->form_validation->set_rules('email', 'email', 'required');
	    	
	    	if ($this->form_validation->run() == FALSE)
	    	{
	    		$this->loadview('Recover Password','account/recoverPasswordForm',[]);
	    	}
	    	else
	    	{ 
	    		$email = $this->input->post('email');
	    		$this->load->model('user_model');
	    		$user = $this->user_model->getFromEmail($email);

	    		if (isset($user)) {
	    			$newPassword = $user->initPassword();
	    			$this->user_model->updatePassword($user);
	    			
	    			$this->load->library('email');
	    			$this->email->from('csc309Login@cs.toronto.edu', 'Login App');
	    			$this->email->to($user->email);
	    			
	    			$this->email->subject('Password recovery');
	    			$this->email->message("Your new password is $newPassword");
	    			$result = $this->email->send();
	    			
	    			//$data['errorMsg'] = $this->email->print_debugger();	
	    			
	    			//$this->load->view('emailPage',$data);
	    			$this->loadview('Email sent','account/emailPage',[]);
	    			
	    		}
	    		else {
	    			$data['errorMsg']="No record exists for this email!";
	    			$this->loadview('Recover Password','account/recoverPasswordForm',$data);
	    		}
	    	}
    }    
 }

