<?php defined('SYSPATH') or die('No direct script access.');


require_once APPPATH.'classes/authsystem.php';


/**
 * App controller class.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */
class Controller_App extends Controller {


   /**
    * @var string Filename of the template file.
    */
   public $template = 'template/default';

   /**
    * @var boolean Whether the template file should be rendered automatically.
    * 
    * If set, then the template view set above will be created before the controller action begins.
    * You then need to just set $this->template->content to your content, without needing to worry about the containing template.
    *
    **/
   public $auto_render = TRUE;

   /**
    * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified
    *
    * Can be set to a string or an array, for example array('login', 'admin') or 'login'
    */
   public $auth_required = FALSE;

   /** Controls access for separate actions
    * 
    *  Examples:
    * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    */
   public $secure_actions = FALSE;

   /**
    * Called from before() when the user does not have the correct rights to access a controller/action.
    *
    * Override this in your own Controller / Controller_App if you need to handle
    * responses differently.
    *
    * For example:
    * - handle JSON requests by returning a HTTP error code and a JSON object
    * - redirect to a different failure page from one part of the application
    */
   public function access_required() {
      $this->request->redirect('user/noaccess');
   }

   /**
    * Called from before() when the user is not logged in but they should.
    *
    * Override this in your own Controller / Controller_App.
    */
   public function login_required() {
      Request::current()->redirect('account/login?bounce=manage');
   }

   public function __construct(Kohana_Request $request, Kohana_Response $response)
   {
   
		Auth::initialize();
				
		parent::__construct($request, $response);
	}
   
   /**
    * The before() method is called before your controller action.
    * In our template controller we override this method so that we can
    * set up default values. These variables are then available to our
    * controllers if they need to be modified.
    *
    * @return  void
    */
   public function before()
   {
      // Execute parent::before first
      parent::before();

      // Check user auth and role
      $action_name = Request::current()->action();
      
      if (($this->auth_required !== FALSE && ($this->auth_required == 'admin' && Auth::$user->isAdmin() === FALSE || $this->auth_required == 'gadmin' && Auth::$user->isGroupAdmin() === FALSE) ) )
       {
         if (Auth::loggedIn()){
            // user is logged in but not on the secure_actions list
            $this->access_required();
         } else {
            $this->login_required();
         }
      }

      if ($this->auto_render) {

         // only load the template if the template has not been set..
         $this->template = View::factory($this->template);
         
         // Initialize empty values
         // Page title
         $this->template->title   = '';
         // Page content
         $this->template->content = '';
         // Styles in header
         $this->template->styles = array();
         // Scripts in header
         $this->template->scripts = array();
         // ControllerName will contain the name of the Controller in the Template
         $this->template->controllerName = $this->request->controller();
         // ActionName will contain the name of the Action in the Template
         $this->template->actionName = $this->request->action();
         // next, it is expected that $this->template->content is set e.g. by rendering a view into it.
     }
   }

   /**
    * The after() method is called after your controller action.
    * In our template controller we override this method so that we can
    * make any last minute modifications to the template before anything
    * is rendered.
    */
   public function after() {
      if ($this->auto_render === TRUE) {         
         $styles = array( 'public/css/manage.css' => 'screen');
         $scripts = array();

         $this->template->styles = array_merge( $this->template->styles, $styles );
         $this->template->scripts = array_merge( $this->template->scripts, $scripts );
         // Assign the template as the request response and render it
         $this->response->body( $this->template );
      }
      parent::after();
   }

}
