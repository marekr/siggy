<?php


class UserSession
{
	private $dataStore = array();
	
	
	public $charID = 0;
	public $charName = "";
	public $corpID = 0;
	public $trusted = false;
	public $igb = false;
	
	private $sessionID = "";

	public function __construct()
	{
	
		$this->charID = isset($_SERVER['HTTP_EVE_CHARID']) ? $_SERVER['HTTP_EVE_CHARID'] : 0;
		$this->charName = isset($_SERVER['HTTP_EVE_CHARNAME']) ? $_SERVER['HTTP_EVE_CHARNAME'] : '';
		$this->corpID = isset($_SERVER['HTTP_EVE_CORPID']) ? $_SERVER['HTTP_EVE_CORPID'] : 0;
		$this->igb = miscUtils::isIGB();
		$this->trusted = miscUtils::getTrust();	
		
	
		$this->sessionID = Cookie::get('sessionID');
		if( $this->sessionID == NULL )
		{
			$this->sessionID = $this->__generateSessionID();
            
			$userData = array();
			
			//reauth the member
			//remember me check
			$memberID = Cookie::get('userID');
			$passHash = Cookie::get('passHash');
			if( $memberID && $passHash )
			{
				
				if( !Auth::autoLogin($memberID, $passHash) )
				{
                    Cookie::delete('userID');
                    Cookie::delete('passHash');
				}
				
			}
			
			$this->__generateSession($this->sessionID);
		}
        else
        {
            $sess = DB::query(Database::SELECT, 'SELECT sessionID,userID,groupID FROM siggysessions WHERE sessionID=:id')->param(':id', $this->sessionID)->execute()->current();
                
            if( isset($sess['sessionID']) )
            {
                Auth::$user->loadByID( $sess['userID'] );
                $this->__updateSession( $this->sessionID );
            }
            else
            {
                $this->sessionID = $this->__generateSessionID();
                
                $memberID = Cookie::get('userID');
                $passHash = Cookie::get('passHash');
                if( $memberID && $passHash )
                {
                    if( !Auth::autoLogin($memberID, $passHash) )
                    {
                        Cookie::delete('userID');
                        Cookie::delete('passHash');
                    }
                }
				
                $this->__generateSession($this->sessionID);
            }
        }
		
		
	}
	
	public function destroy()
	{
		DB::delete('siggysessions')->where('sessionID', '=', $this->sessionID)->execute();
		
		//session_destroy();
		
		Cookie::delete('sessionID');
	}
	
	private function __generateSessionID()
	{
		$sessionID = md5(uniqid(microtime()) . Request::$client_ip . Request::$user_agent);
		return $sessionID;
	}
	
	public function reloadUserSession()
	{
		if( empty($this->sessionID) )
		{
			return;
		}

		$update = array( 'userID' => Auth::$user->data['id'],
						 'groupID' => Auth::$user->data['groupID'],
						 'chainmap_id' => Auth::$user->data['active_chain_map'],
                         'charName' => Auth::$user->data['apiCharName'],
                         'charID' => Auth::$user->data['apiCharID']
						 );

		DB::update('siggysessions')->set( $update )->where('sessionID', '=',  $this->sessionID)->execute();
		
		
		//$_SESSION['userData'] = Auth::$user->data;
		//$_SESSION['userPerms'] = Auth::$user->perms;
	}
	
	private function __generateSession()
	{
		$insert = array( 'sessionID' => $this->sessionID,
					'charID' => $this->charID,
					'charName' => $this->charName,
					'created' => time(),
					'ipAddress' => Request::$client_ip,
					'userAgent' => Request::$user_agent,
					'sessionType' => ( $this->igb ? 'igb' : 'oog' ),
					'userID' => ( isset(Auth::$user->data['id']) ? Auth::$user->data['id'] : 0 ),
					'groupID' => ( isset(Auth::$user->data['groupID']) ? Auth::$user->data['groupID'] : 0 ),
					'chainmap_id' =>  ( isset(Auth::$user->data['active_chain_map']) ? Auth::$user->data['active_chain_map'] : 0 ),
				  );
							
		DB::insert('siggysessions', array_keys($insert) )->values(array_values($insert))->execute();
		
		
		//$_SESSION['init'] = TRUE;
		Cookie::set('sessionID', $this->sessionID);

		return TRUE;
	}
	
	private function __updateSession()
	{
		if( empty($this->sessionID) )
		{
			return;
		}
		
		$update = array( 'lastBeep' => time(),
						'groupID' => ( isset(Auth::$user->data['groupID']) ? Auth::$user->data['groupID'] : 0 ),
						'subGroupID' =>  ( isset(Auth::$user->data['subGroupID']) ? UAuth::$user->ata['subGroupID'] : 0 ),
		);
		
		DB::update('siggysessions')->set( $update )->where('sessionID', '=',  $this->sessionID)->execute();
	}
}