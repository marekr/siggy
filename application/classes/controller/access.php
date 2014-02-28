<?php defined('SYSPATH') or die('No direct script access.');

require_once APPPATH.'classes/FrontController.php';

class Controller_Access extends FrontController 
{
	public $groupData = array();
	public $trusted = false;
	
	public $template = 'template/main';
	
	public function action_group_password()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		if( !$this->trusted )
		{
			return;
		}
		
		$view = View::factory('access/groupPassword');
		$view->groupData = $this->groupData;
		$view->trusted = $this->trusted;
		$view->wrongPass = false;
		$this->template->siggyMode = false;

		//load header tools
		$this->template->headerTools = '';		
		
		$groupID = intval($this->groupData['groupID']);
		$subGroupID = intval($this->groupData['subGroupID']);
		
		if( isset($_POST['authPassword']) )
		{
			$pass = sha1($_POST['authPassword'].$this->groupData['authSalt']);
			if( !empty( $this->groupData['sgAuthPassword'] ) )
			{
				if( $pass == $this->groupData['sgAuthPassword'] )
				{
					Cookie::set('authPassword-' .$groupID .'-'.$subGroupID, $pass, 365*60*60*24);
					HTTP::redirect('/');
				}
				else
				{
					$view->wrongPass = true;
				}
			}
			elseif( !empty($this->groupData['authPassword']) )
			{
				if( $pass == $this->groupData['authPassword'] )
				{
					Cookie::set('authPassword-' .$groupID .'-'.$subGroupID, $pass, 365*60*60*24);
					HTTP::redirect('/');
				}
				else
				{
					$view->wrongPass = true;
				}
			}
		}
		
		
		$this->template->content = $view;
	}
	
	
	public function action_switch_membership()
	{
		$k = $_GET['k'];
        if( count( $this->groupData['groups'] ) > 1 || count( current($this->groupData['groups']) > 1) )
        {
            foreach( $this->groupData['groups'] as $g => $sgs )
            {
                foreach( $sgs as $sg )
                {
                    if( md5($g.'-'.$sg) == $k )
                    {
                        Cookie::set('membershipChoice', $g.'-'.$sg, 365*60*60*24);
                        break;
                    }
                }
            }
        }
		HTTP::redirect('/');
	}
	
	public function before()
	{
		if( $this->request->action() == 'group_password' || $this->request->action() == "switch_membership" )
		{
			$this->noAutoAuthRedirects = TRUE;
		}
	
		parent::before();
	}
}