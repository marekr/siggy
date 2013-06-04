<?php 

require_once APPPATH.'classes/FrontController.php';
require_once APPPATH.'classes/access.php';

class Controller_Pages extends FrontController
{
		private $auth;
		private $user;
		public $template = 'template/public';
		protected $noAutoAuthRedirects = true;
		
		function __construct(Kohana_Request $request, Kohana_Response $response)
		{	
			parent::__construct($request, $response);
		}
		
		public function action_viewPage()
		{	
			$page = $this->request->param('page','');
			if( $page == '' )
			{
				if( Auth::loggedIn() )
				{
					$this->request->redirect('/');
				}
			}
			$this->template->title = 'siggy - getting siggy';
			
			$this->template->selectedTab = 'home';
			$this->template->layoutMode = 'blank';
			
			if( $page == 'getting-siggy' )
			{
				$this->template->content = View::factory('pages/gettingSiggy');
			}
			else if( $page == 'create-group' || $page == 'createGroup' )
			{
				$this->template->selectedTab = 'createGroup';
				$this->template->content = View::factory('pages/createGroupIntro');
			}
			else if( $page == 'costs' )
			{
				$this->template->content = View::factory('pages/costs');
			}
			else if( $page == 'accessMessage')
			{
				$this->template->title = "Trust required";
				$this->template->selectedTab = 'home';
				
				$this->template->content = $view = View::factory('siggy/accessMessage');
				$view->bind('groupData', $this->groupData);
				$view->trusted = $this->trusted;
				$view->igb = $this->igb;
				$view->set('offlineMode', false);
			}
			else
			{
				$this->template->content = View::factory('pages/home');
			}
			
			$this->template->loggedIn = Auth::loggedIn();
			$this->template->user = Auth::$user->data;
		}


		public function action_createGroup()
		{
			if( $this->igb || !Auth::loggedIn() )
			{
				$this->request->redirect('/');
			}
		
			$this->template->title = 'Create siggy group';
			$this->template->selectedTab = 'createGroup';
			$this->template->layoutMode = 'blank';
		
			$this->template->loggedIn = Auth::loggedIn();
			$this->template->user = Auth::$user->data;
			
			$id = intval($this->request->param('id'));
			
			if( $id == 0 || $id == 1 )
			{
				$this->template->content = View::factory('pages/createGroupIntro');
			}
			else if( $id == 2 )
			{
				$errors = array();
				if ($this->request->method() == "POST") 
				{
					$validator = Validation::factory($_POST)
								->rule('groupName', 'not_empty')
								->rule('groupTicker', 'not_empty')
								->rule('ingameContact', 'not_empty')
								->rule('confirmGroupPassword',  'matches', array(':validation', 'groupPassword', 'confirmGroupPassword'));
								
					if( intval($_POST['authMode']) == 1 )
					{
						$validator->rule('groupPassword', 'not_empty');
					}			
					
					if ( $validator->check() )
					{
							$groupID = groupUtils::createNewGroup( array( 'groupName' => $_POST['groupName'],
																'groupTicker' => $_POST['groupTicker'],
																'groupPassword' => $_POST['groupPassword'],
																'authMode' => intval($_POST['authMode']),
																'homeSystems' => $_POST['homeSystems'],
																'ingameContact' => $_POST['ingameContact']
															)
														);
														
							if( $groupID )
							{
								Auth::$user->data['gadmin'] = 1;
								Auth::$user->data['groupID'] = $groupID;
								Auth::$user->save();
								
								$this->request->redirect('pages/createGroup/3');
							}
							else
							{
								$errors[] = 'Unknown error ocurred';
							}
					}
					else
					{
							$errors = $validator->errors('pages/createGroup');
					}
				}
				$this->template->content = View::factory('pages/createGroupForm')->bind('errors', $errors);
			}
			else if ( $id == 3 )
			{
				$this->template->content = View::factory('pages/createGroupComplete');
			}
		}
}