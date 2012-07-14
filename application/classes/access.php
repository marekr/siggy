<?php

require_once APPPATH.'classes/groupUtils.php';
require_once APPPATH.'classes/mapUtils.php';
require_once APPPATH.'classes/miscUtils.php';

class AuthStatus
{
		const TRUSTREQUIRED = 0;
		const NOACCESS = 1;
		const GPASSWRONG = 2;

		const ACCEPTED = 3;

		//igb state default state/not logged in
		const APILOGINREQUIRED = 4;

		//bad key or api errored
		const APILOGININVALID = 5;

		//nuff said
		const APILOGINNOTENABLED = 6;

		//api char has no access/group
		const APILOGINNOACCESS = 7;
}


class access
{
		private $trusted = false;
		private $igb = false;
		
		private $authPassword = '';
		private $authStatus = AuthStatus::NOACCESS;
		public $accessData = array();
		
		function __construct()
		{
			Cookie::$salt = 'y[$e.swbDs@|Gd(ndtUSy^';
			
			$this->trusted = miscUtils::getTrust();
			$this->igb = miscUtils::isIGB();
		}

		public function authenticate()
		{
				if (Kohana::$profiling === TRUE)
				{
						// Start a new benchmark
						$benchmark = Profiler::start('access', __FUNCTION__);
				}			
				
				if( $this->igb )
				{
						$this->authPassword = Cookie::get('authPassword', '');
						
						if( $this->trusted )
						{
								$this->accessData = $this->getAccessData( $_SERVER['HTTP_EVE_CORPID'], $_SERVER['HTTP_EVE_CHARID'] );
								$this->accessData['charID'] = $_SERVER['HTTP_EVE_CHARID'];
								$this->accessData['corpID'] = $_SERVER['HTTP_EVE_CORPID'];
								$this->accessData['charName'] = $_SERVER['HTTP_EVE_CHARNAME'];
								
								if( $this->accessData['groupID'] )
								{
											if( $this->accessData['authMode'] == 1 )
											{
											
													if( (!empty($this->accessData['sgAuthPassword']) && $this->authPassword == $this->accessData['sgAuthPassword'] )	 
															|| ( empty($this->accessData['sgAuthPassword']) && $this->authPassword == $this->accessData['authPassword']) 
														) 
													{
															$this->authStatus = AuthStatus::ACCEPTED;

															if (isset($benchmark))
															{
																	// Stop the benchmark
																	Profiler::stop($benchmark);
															}
															
															return $this->authStatus;
													}
													else
													{
															$this->authStatus = AuthStatus::GPASSWRONG;

															if (isset($benchmark))
															{
																	// Stop the benchmark
																	Profiler::stop($benchmark);
															}
															
															return $this->authStatus;
													}
													 
											}
											else if( $this->accessData['authMode'] == 2 )
											{
													$auth = simpleauth::instance();
													if ( $auth->logged_in() )
													{
															if( $this->apiCharInfo = $auth->apiCharInfo() )
															{
															
																	if( ( $this->accessData['charID'] == $this->apiCharInfo['charID'] ) && ( $this->accessData['corpID'] == $this->apiCharInfo['corpID'] ) )
																	{
																			$this->authStatus = AuthStatus::ACCEPTED;
																			
																			if (isset($benchmark))
																			{
																					// Stop the benchmark
																					Profiler::stop($benchmark);
																			}
																				
																			return $this->authStatus;
																	}
																	else
																	{
																			$this->authStatus = AuthStatus::APILOGINNOACCESS;
																			
																			if (isset($benchmark))
																			{
																					// Stop the benchmark
																					Profiler::stop($benchmark);
																			}
																						
																			return $this->authStatus;																	
																	}
															}
													}
													else
													{
															$this->authStatus = AuthStatus::APILOGINREQUIRED;

															if (isset($benchmark))
															{
																	// Stop the benchmark
																	Profiler::stop($benchmark);
															}
																														
															
															return $this->authStatus;
													}
											}
											else
											{
													$this->authStatus = AuthStatus::ACCEPTED;
													
													if (isset($benchmark))
													{
															// Stop the benchmark
															Profiler::stop($benchmark);
													}
													
													return $this->authStatus;
											}
								}
								else
								{
										$this->authStatus = AuthStatus::NOACCESS;
										if (isset($benchmark))
										{
												// Stop the benchmark
												Profiler::stop($benchmark);
										}
										
										return $this->authStatus;
								}
						}
						else
						{
								$this->authStatus = AuthStatus::TRUSTREQUIRED;
								
								if (isset($benchmark))
								{
										// Stop the benchmark
										Profiler::stop($benchmark);
								}
			
								return $this->authStatus;
						}
				}
				else
				{
					//	$accessKey = Cookie::get('accessKey', '');
					//	$oogPass = Cookie::get('oogPass', '');
						return $this->__checkAccountAuth();
				}
		}
		
		private function __checkAccountAuth()
		{
					$auth = simpleauth::instance();
					
					if ( $auth->logged_in() )
					{
							if( $this->apiCharInfo = $auth->apiCharInfo() )
							{
									$this->accessData = $this->getAccessData( $this->apiCharInfo['corpID'], $this->apiCharInfo['charID'] );
									$this->accessData['charID'] = $this->apiCharInfo['charID'];
									$this->accessData['corpID'] = $this->apiCharInfo['corpID'];
									$this->accessData['charName'] = $this->apiCharInfo['charName'];
									
									if( $this->accessData['groupID'] )
									{
											//if( $this->accessData['authMode'] == 4 )
											//{
													$this->authStatus = AuthStatus::ACCEPTED;
													
													if (isset($benchmark))
													{
															// Stop the benchmark
															Profiler::stop($benchmark);
													}
														
													return $this->authStatus;
											//}
											//else
											//{
											//		$this->authStatus = AuthStatus::APILOGINNOTENABLED;
											//		return;
											//}
									}
									else
									{
											$this->authStatus = AuthStatus::APILOGINNOACCESS;
											
											if (isset($benchmark))
											{
													// Stop the benchmark
													Profiler::stop($benchmark);
											}
														
											return $this->authStatus;
									}
							}
							else
							{
									$this->authStatus = AuthStatus::APILOGININVALID;
									
									if (isset($benchmark))
									{
											// Stop the benchmark
											Profiler::stop($benchmark);
									}
														
									return $this->authStatus;
							}
					} 
					else 
					{
							$this->authStatus = AuthStatus::APILOGINREQUIRED;

							if (isset($benchmark))
							{
									// Stop the benchmark
									Profiler::stop($benchmark);
							}
																						
							
							return $this->authStatus;
					}
		}
			
		public function getAccessData($corpID = 0, $charID = 0)
		{
				if (Kohana::$profiling === TRUE)
				{
						// Start a new benchmark
						$benchmark = Profiler::start('access', __FUNCTION__);
				}	

				$default = array('groupID' =>0, 'authMode' => 0, 'authPassword' => '');
				$memberData = array();
				
				$corpList = groupUtils::getCorpList();
				$charList = groupUtils::getCharList();
				
				$membershipChoice = Cookie::get('membershipChoice', '');
				$chosenGroupID = 0;
				$chosenSubGroupID = 0;
				if( !empty($membershipChoice) )
				{
					$t = explode('-', $membershipChoice);
					$chosenGroupID = intval($t[0]);
					$chosenSubGroupID = intval($t[1]);
				}
				
				//corp based members
				if( !empty( $corpID ) )
				{
						$corpID = intval( $corpID );
				}
				else
				{
						//no corp sent, fuck off with your headers
						if (isset($benchmark))
						{
								// Stop the benchmark
								Profiler::stop($benchmark);
						}
						return $default;
				}
				
				//character based members
				if( !empty($charID) )
				{
					$charID = intval($charID);
				}
				else
				{
					//no char sent, fuck off with your headers
					if (isset($benchmark))
					{
							// Stop the benchmark
							Profiler::stop($benchmark);
					}
					return $default;
				}
				$accessGroupID = 0;
				$accessSubGroupID = 0;
				if( empty( $chosenGroupID ) )
				{
					
							$corpMemberData = array();
							if( in_array($corpID, $corpList) )
							{
									$corpMemberData = groupUtils::getCorpData( $corpID );
							}
							$charMemberData = array();
							if( in_array($charID, $charList) )
							{
									$charMemberData = groupUtils::getCharData( $charID );
							}				
							
									
							//just moved it here
							if( !empty($corpMemberData) )
							{
									$memberData = $corpMemberData;
									
									//$firstGroup = current($memberData['groups']);
									//$accessGroupID = $firstGroup[0];
									//$accessSubGroupID = $firstGroup[1];
							}
							else
							{
									$memberData = $charMemberData;
									if( !$memberData )
									{
											//no access anyway
											if (isset($benchmark))
											{
													// Stop the benchmark
													Profiler::stop($benchmark);
											}
											return $default;
									}
									//else
									//{
											//$firstGroup = current($memberData['groups']);
										//	$accessGroupID = $firstGroup[0];
										//	$accessSubGroupID = $firstGroup[1];			
									//}
							}
							$keys = array_keys($memberData['groups']);
							$firstGroup = $keys[0];
							$accessGroupID = $firstGroup;
							$accessSubGroupID = $memberData['groups'][ $firstGroup ][0];	//picking the first subGroupID
							
							if(  isset( $corpMemberData['groups'] ) &&  isset( $charMemberData['groups'] ) )
							{
						//			$memberData['groups'] =  array_merge($corpMemberData['groups'],$charMemberData['groups']);
									$memberData['groups'] =  $this->_blah($corpMemberData['groups'],$charMemberData['groups']);
									$memberData['groupDetails']['group'] =  $corpMemberData['groupDetails']['group'] + $charMemberData['groupDetails']['group'];
									$memberData['groupDetails']['subgroup'] =  $corpMemberData['groupDetails']['subgroup'] + $charMemberData['groupDetails']['subgroup'];
									//$memberData['groups'] = array_unique( $memberData['groups'] );
							}						
							
				}
				else
				{
						$corpMemberData = array();
						if( in_array($corpID, $corpList) )
						{
								$corpMemberData = groupUtils::getCorpData( $corpID );
						}
						$charMemberData = array();
						if( in_array($charID, $charList) )
						{
								$charMemberData = groupUtils::getCharData( $charID );
						}				
						
						$found = false;
						if( isset( $corpMemberData['groups'][ $chosenGroupID ] ) )
						{
								foreach( $corpMemberData['groups'][ $chosenGroupID ] as $sg )
								{
										if( $sg == $chosenSubGroupID )
										{
												$accessGroupID = $chosenGroupID;
												$accessSubGroupID = $sg;
												$memberData = $corpMemberData;
												$found  = true;
												break;
										}
								}
						}
						
						if ( !$found && isset( $charMemberData['groups'][ $chosenGroupID ] ) )
						{
								foreach( $charMemberData['groups'][ $chosenGroupID ] as $sg )
								{
										if( $sg == $chosenSubGroupID )
										{
												$accessGroupID = $chosenGroupID;
												$accessSubGroupID = $sg;
												$memberData = $charMemberData;
												$found = true;
												break;
										}
								}
						}
						//print_r($charMemberData['groups']);
						//print_r($chosenGroupID);
						//print_r($chosenSubGroupID);
						
						if(  isset( $corpMemberData['groups'] ) &&  isset( $charMemberData['groups'] ) )
						{
							//	$memberData['groups'] =  $corpMemberData['groups'] + $charMemberData['groups'];
								$memberData['groups'] =  $this->_blah($corpMemberData['groups'],$charMemberData['groups']);
								$memberData['groupDetails']['group'] =  $corpMemberData['groupDetails']['group'] + $charMemberData['groupDetails']['group'];
								$memberData['groupDetails']['subgroup'] =  $corpMemberData['groupDetails']['subgroup'] + $charMemberData['groupDetails']['subgroup'];
							//	$memberData['groups'] = array_unique( $memberData['groups'] );
						}
						
						//accessGroupID may sitll be empty at this point
						if ( empty($accessGroupID) )
						{
								Cookie::set('membershipChoice', '', 365*60*60*24);
								
								if( in_array($corpID, $corpList) )
								{
										$memberData = $corpMemberData;
								}
								else
								{
										if( in_array($charID, $charList) )
										{
												$memberData = $charMemberdata;
										}
										else
										{
											//no access anyway
											if (isset($benchmark))
											{
													// Stop the benchmark
													Profiler::stop($benchmark);
											}
											return $default;
										}
								}
						}
				}
				
				$groupData = groupUtils::getGroupData( $accessGroupID, $accessSubGroupID );
				
				if( !$groupData )
				{
						return $default;
				}
				
				$out = array_merge($groupData, $memberData);
				$out['groupID'] = $accessGroupID;
				$out['subGroupID'] = $accessSubGroupID;
				$out['accessName'] = $out['groupDetails']['subgroup'][ $accessSubGroupID ]['accessName'];
				if (isset($benchmark))
				{
						// Stop the benchmark
						Profiler::stop($benchmark);
				}			
				
				return $out;
		}

		private function _blah( $arr1, $arr2 )
		{
				$keys1 = array_keys( $arr1 );
				$keys2 = array_keys( $arr2 );
				$keys = array_merge($keys1, $keys2);
				
				$out = array();
				foreach( $keys as $k )
				{
						if( isset( $arr1[ $k ]) )
						{
								if( isset( $arr2[ $k] ) )
								{
										$out[ $k ] = array_merge($arr1[ $k ], $arr2[ $k ]);
										$out[ $k ] = array_unique( $out[ $k ] );
								}
								else
								{
										$out[ $k ] = $arr1[ $k ];
								}
						}
						else
						{
								if( isset($arr2[ $k ] ) )
								{
										$out[ $k ] = $arr2[ $k ];
								}
						}
				}
				
				return $out;
		}
}