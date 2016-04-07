<?php

class Board extends CI_Controller {
     
    function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
	    	session_start();
    } 
          
    public function _remap($method, $params = array()) {
        // enforce access control to protected functions	
        
        if (!isset($_SESSION['user']))
            redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again

        $this->load->model('user_model');
        $user = $this->user_model->get($_SESSION['user']->login);
        if( !isset($user) ||
            (
                $user->user_status_id != User::PLAYING &&
                $user->user_status_id != User::WAITING
            )
         )
        {
            redirect('arcade/index');
            return;
        }

        if( strcmp($method,'getGameState') == 0 &&
            $user->user_status_id != User::PLAYING )
        {
            echo json_enocde(array('status'=>'failure','reason'=>'not valid access'));
           return; 
        }
        
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
    
    
    function index() {
		$user = $_SESSION['user'];
    		    	
	    	$this->load->model('user_model');
	    	$this->load->model('invite_model');
	    	$this->load->model('match_model');
	    	
	    	$user = $this->user_model->get($user->login);
	    	
	    	if ($user->user_status_id == User::WAITING) {
	    		$invite = $this->invite_model->get($user->invite_id);
	    		$otherUser = $this->user_model->getFromId($invite->user2_id);
	    	}
	    	else if ($user->user_status_id == User::PLAYING) {
	    		$match = $this->match_model->get($user->match_id);
	    		if ($match->user1_id == $user->id)
	    			$otherUser = $this->user_model->getFromId($match->user2_id);
	    		else
	    			$otherUser = $this->user_model->getFromId($match->user1_id);
	    	}
	    	
	    	$data['user']=$user;
	    	$data['otherUser']=$otherUser;
	    	
	    	switch($user->user_status_id) {
	    		case User::PLAYING:	
	    			$data['status'] = 'playing';
	    			break;
	    		case User::WAITING:
	    			$data['status'] = 'waiting';
	    			break;
	    	}
	    	
		$this->loadview('Game','match/board',$data);
    }

    function postGameState($column) {
        //TODO : Game Implementaion
        //right now, it when server get this request, it process as giving up the game.
        //if game ends, just change game state. you don't need to change user state to available. it is already handled.

        $column = (int)$column;
        if( !is_int($column) ) {
            echo json_encode(array('status'=>'fail','reason'=>'argument'));
            return;
        }
        
        $user = $_SESSION['user'];
        $this->load->model('user_model');
        $this->load->model('match_model');

        $this->db->trans_begin();
        $user = $this->user_model->getExclusive($user->login);
        $match = $this->match_model->getExclusive($user->match_id);
        $board = new BoardState();
        $board->unserialize($match->board_state);


        if( $column < 0 || $column > 6)
        {
            if( $match->user1_id == $user->id )
                $this->match_model->updateStatus($match->id,Match::U2WON);
            else
                $this->match_model->updateStatus($match->id,Match::U1WON);
        }
        else
        {
            $flag = 0;
            if( $match->user1_id == $user->id )
                $flag = 1;
            else
                $flag = 2;

            $cnt = 0;
            for($i = 0; $i < 6; $i++)
            {
                for($j = 0; $j < 7; $j++)
                {
                    if( $board->state[$i][$j] != 0 )
                    {
                        $cnt++;
                    }
                }
            }

            if( $cnt % 2 == 0 )
                $turn = 1;
            else
                $turn = 2;

            if( $turn != $flag )
            {
                $this->db->trans_rollback();
                echo json_encode(array('status'=>'failure','reason'=>'not your turn'));
                return;
            }
            else
            {
                for($i = 5; $i >= 0; $i--)
                {
                    if( $board->state[$i][$column] == 0 )
                    {
                        $board->state[$i][$column] = $turn;
                        break;
                    }
                }

                if( $i < 0 )
                {
                    $this->db->trans_rollback();
                    echo json_encode(array('status'=>'failure','reason'=>'not possible move'));
                    return;
                }
                else
                {
                    $winner = 0;

                    //check left diagoonal
                    $cont = 1;
                    for($j = 1; $j <=3; $j++)
                    {
                        if($i - $j >= 0 && $column - $j >= 0 && $board->state[$i-$j][$column-$j] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    for($j = 1; $j <= 3; $j++)
                    {
                        if( $i + $j < 6 && $column + $j < 7 && $board->state[$i+$j][$column+$j] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    if($cont >= 4)
                    {
                        $winner = $turn;
                    }

                    //check right diagoonal
                    $cont = 1;
                    for($j = 1; $j <=3; $j++)
                    {
                        if($i - $j >= 0 && $column + $j < 7 && $board->state[$i-$j][$column+$j] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    for($j = 1; $j <= 3; $j++)
                    {
                        if( $i + $j < 6 && $column - $j >= 0 && $board->state[$i+$j][$column-$j] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    if($cont >= 4)
                    {
                        $winner = $turn;
                    }

                    //check horizontal
                    $cont = 1;
                    for($j = 1; $j <=3; $j++)
                    {
                        if($column - $j >= 0 && $board->state[$i][$column-$j] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    for($j = 1; $j <= 3; $j++)
                    {
                        if( $column + $j < 7 && $board->state[$i][$column+$j] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    if($cont >= 4)
                    {
                        $winner = $turn;
                    }

                    //check vertical   
                    $cont = 1;
                    for($j = 1; $j <=3; $j++)
                    {
                        if($i - $j >= 0 && $board->state[$i-$j][$column] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    for($j = 1; $j <= 3; $j++)
                    {
                        if( $i + $j < 7 && $board->state[$i+$j][$column] == $turn )
                            $cont++;
                        else
                            break;
                    }
                    if($cont >= 4)
                    {
                        $winner = $turn;
                    }


                    if($winner != 0 )
                    {
                        if( $winner == 2 )
                            $this->match_model->updateStatus($match->id,Match::U2WON);
                        else
                            $this->match_model->updateStatus($match->id,Match::U1WON);
                    }
                    else if( $cnt + 1 == 42 )
                    {
                        $this->match_model->updateStatus($match->id,Match::TIE);
                    }
                }

                $this->match_model->updateBoard($match->id,$board->serialize());
            }

        }

        if( $this->db->trans_status() === FALSE )
            $this->db->trans_rollback();
        else
            $this->db->trans_commit();

        echo json_encode(array('status'=>'success','user'=>$user,'match'=>$match,'board'=>$board));
    }

    function getGameState() {
        $user = $_SESSION['user'];
        $this->load->model('user_model');
        $this->load->model('match_model');
        
        $user = $this->user_model->get($user->login);
        $match = $this->match_model->get($user->match_id);
        $board = new BoardState();
        $board->unserialize($match->board_state);
        
        if( $match->user1_id == $user->id)
            $otherUser = $this->user_model->getFromId($match->user2_id);
        else
            $otherUser = $this->user_model->getFromId($match->user1_id);

        if( $match->match_status_id != Match::ACTIVE )
        { //game end.
            $this->db->trans_begin();
            $user = $this->user_model->getExclusive($user->login);
            $this->user_model->updateStatus($user->id,User::AVAILABLE);
            $this->user_model->updateMatchNull($user->id);
            if( $this->db->trans_status() === FALSE )
                $this->db->trans_rollbak();
            else
                $this->db->trans_commit();
        }


        echo json_encode(array('status'=>'success','user'=>$user,'otherUser'=>$otherUser,'match'=>$match,'board'=>$board));
        return;
    }

 	function postMsg() {
 		$this->load->library('form_validation');
 		$this->form_validation->set_rules('msg', 'Message', 'required');
 		
 		if ($this->form_validation->run() == TRUE) {
 			$this->load->model('user_model');
 			$this->load->model('match_model');

 			$user = $_SESSION['user'];
 			 
 			$user = $this->user_model->getExclusive($user->login);
 			if ($user->user_status_id != User::PLAYING) {	
				$errormsg="Not in PLAYING state";
 				goto error;
 			}
 			
 			$match = $this->match_model->get($user->match_id);			
 			
 			$msg = $this->input->post('msg');
 			
 			if ($match->user1_id == $user->id)  {
 				$msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
 				$this->match_model->updateMsgU1($match->id, $msg);
 			}
 			else {
 				$msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
 				$this->match_model->updateMsgU2($match->id, $msg);
 			}
 				
 			echo json_encode(array('status'=>'success'));
 			 
 			return;
 		}
		
 		$errormsg="Missing argument";
 		
		error:
			echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 
	function getMsg() {
 		$this->load->model('user_model');
 		$this->load->model('match_model');
 			
 		$user = $_SESSION['user'];
 		 
 		$user = $this->user_model->get($user->login);
 		if ($user->user_status_id != User::PLAYING) {	
 			$errormsg="Not in PLAYING state";
 			goto error;
 		}
 		// start transactional mode  
 		$this->db->trans_begin();
 			
 		$match = $this->match_model->getExclusive($user->match_id);			
 			
 		if ($match->user1_id == $user->id) {
			$msg = $match->u2_msg;
 			$this->match_model->updateMsgU2($match->id,"");
 		}
 		else {
 			$msg = $match->u1_msg;
 			$this->match_model->updateMsgU1($match->id,"");
 		}

 		if ($this->db->trans_status() === FALSE) {
 			$errormsg = "Transaction error";
 			goto transactionerror;
 		}
 		
 		// if all went well commit changes
 		$this->db->trans_commit();
 		
 		echo json_encode(array('status'=>'success','message'=>$msg));
		return;
		
		transactionerror:
		$this->db->trans_rollback();
		
		error:
		echo json_encode(array('status'=>'failure','message'=>$errormsg));
 	}
 	
 }

