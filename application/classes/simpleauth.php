<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
* Simple_Auth - user authorization library for KohanaPHP framework
*
* @package			simpleauth for Kohana 3.x
* @author				thejw23
* @copyright			(c) 2010 thejw23
* @license			http://www.opensource.org/licenses/isc-license.txt
* @version			2.0
* @last change			initial release
* 
* based on KohanaPHP Auth and Simple_Modeler
*/
class simpleauth extends simpleauth_Core 
{
		public function isAdmin()
		{
				$user = $this->get_user();
				if( $user !== FALSE )
				{
						if( $user->admin == 1 )
						{
							return TRUE;
						}
				}
				return FALSE;
		}
		
		public function isGroupAdmin()
		{
				$user = $this->get_user();
			
				if( $user !== FALSE )
				{
						if( $user->gadmin == 1 )
						{
							return TRUE;
						}
				}
				return FALSE;
		}
	
		public function apiCharInfo()
		{
				$user = $this->get_user();
				if( $user !== FALSE )
				{
						if( $user->apiLastCheck < time()-60*80 )
						{
								if( $user->apiID == 0 ||  $user->apiKey == '' || $user->apiCharID == 0 || $user->apiInvalid || ($user->apiFailures >= 3) )
								{
									return FALSE;
								}
								
								//recheck
								require_once( Kohana::find_file('vendor', 'pheal/Pheal') );
								spl_autoload_register( "Pheal::classload" );
								PhealConfig::getInstance()->cache = new PhealFileCache(APPPATH.'cache/api/');
								$pheal = new Pheal( $user->apiID, $user->apiKey, 'eve' );
								
								try 
								{
										try 
										{
												$results = $pheal->CharacterInfo( array( 'characterID' => $user->apiCharID )  );
												
												$this->update_user( $user->id, array('apiCorpID' => $results->corporationID, 'apiLastCheck' => time(),'apiInvalid' => 0, 'apiFailures' => 0 ) );
												$this->reload_user();
												
												return array( 'corpID' => $user->apiCorpID, 'charID' => $user->apiCharID, 'charName' => $user->apiCharName );
										}
										catch( PhealAPIException $e )
										{
												 switch( $e->code )
												 {
														//bad char
														case 105:
															$this->update_user( $user->id, array('apiCharID' => 0, 'apiCorpID' => 0 ) );
															$this->reload_user();
															return FALSE;
															break;
															
														case 202:
														case 203:
														case 204:
														case 205:
														case 210:
														case 212:
															//increment fuck you count
															$this->update_user( $user->id, array('apiFailures' => $user->apiFailures+1 ) );
															$this->reload_user();
															return FALSE;
															break;
															
														case 221:
														case 222:
														case 223:
														case 521:
															//bad api entirely
															$this->update_user( $user->id, array('apiInvalid' => 1) );
															$this->reload_user();
															return FALSE;
															break;

														case 211:
															//expired account
															return FALSE;
															break;
															
														default:
															echo 'error: ' . $e->code . ' meesage: ' . $e->getMessage();
															return array( 'corpID' => $user->apiCorpID, 'charID' => $user->apiCharID, 'charName' => $user->apiCharName );
															break;
												 }
										}
								}
								catch( PhealException $e )
								{
										return array( 'corpID' => $user->apiCorpID, 'charID' => $user->apiCharID, 'charName' => $user->apiCharName );
								}
								
						}
						else
						{
								return array( 'corpID' => $user->apiCorpID, 'charID' => $user->apiCharID, 'charName' => $user->apiCharName );
						}
				}
				return FALSE;
		}
}
