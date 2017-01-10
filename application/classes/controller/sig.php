<?php

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

class Controller_Sig extends FrontController {

	public function before()
	{
		parent::before();

		if( Auth::$session->accessData['active_chain_map'] )
		{
			$this->chainmap = Chainmap::find(Auth::$session->accessData['active_chain_map'], Auth::$session->group->id);
		}
	}

	public function action_add()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if(	 !$this->siggyAccessGranted() )
		{
			$this->response->body(json_encode(['error' => 1, 'errorMsg' => 'Invalid auth']));
			return;
		}

		$sigData = json_decode($this->request->body(), true);

		if( !empty($sigData) && isset($sigData['systemID']) )
		{
			$insert['systemID'] = intval($sigData['systemID']);
			$insert['sig'] = strtoupper($sigData['sig']);
			$insert['description'] = htmlspecialchars($sigData['desc']);
			$insert['created_at'] = Carbon::now()->toDateTimeString();
			$insert['siteID'] = intval($sigData['siteID']);
			$insert['type'] = $sigData['type'];
			$insert['groupID'] = Auth::$session->group->id;

			if( Auth::$session->group->show_sig_size_col )
			{
				$insert['sigSize'] = ( is_numeric( $sigData['sigSize'] ) ? $sigData['sigSize'] : '' );
			}

			$insert['creator'] = Auth::$session->character_name;

			$id = DB::table('systemsigs')->insertGetId($insert);

			$this->chainmap->update_system($insert['systemID'], array('lastUpdate' => time(),
																'lastActive' => time() )
										);

			Auth::$session->group->incrementStat('adds', Auth::$session->accessData);

			$this->notifierCheck($insert);

			$insert['id'] = $id;
			$this->response->body(json_encode(array($id => $insert )));
		}
	}

	private function notifierCheck($sigData)
	{
		foreach( Notifier::all(Auth::$session->group->id, Auth::$session->character_id) as $notifier )
		{
			if( $notifier->type == NotificationTypes::SiteFound )
			{
				$data = $notifier->data;
				if( $sigData['siteID'] == $data->site_id )
				{
					$eventData = array(
										'system_id' => $sigData['systemID'],
										'system_name' => miscUtils::systemNameByID($sigData['systemID']),
										'site_id' => $sigData['siteID'],
										'discoverer_name' => Auth::$session->character_name,
										'discoverer_id' => Auth::$session->character_id,
										'signature' => $sigData['sig']
										);

					$charID = 0;
					if( $notifier->scope == 'personal' )
					{
						$charID = Auth::$session->character_id;
					}

					Notification::create(Auth::$session->group->id, $charID, $notifier->type, $eventData);
				}
			}
		}
	}

	public function action_mass_add()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if(	 !$this->siggyAccessGranted() )
		{
			$this->response->body(json_encode(['error' => 1, 'errorMsg' => 'Invalid auth']));
			return;
		}

		//load settings to trigger localization
		$this->loadSettings();

		if( isset($_POST['systemID']) && isset($_POST['blob']) && !empty($_POST['blob']) )
		{
			$sigs = miscUtils::parseIngameSigExport( $_POST['blob'] );

			$systemID = intval($_POST['systemID']);

			$addedSigs = [];

			$deleteNonExisting = isset($_POST['delete_nonexistent_sigs']) ? (int)$_POST['delete_nonexistent_sigs'] : 0;
			if( $deleteNonExisting )
			{
				$sigList = [];
				foreach( $sigs as $sig )
				{
					$sigList[] = $sig['sig'];
				}

				DB::table('systemsigs')
					->where('groupID', Auth::$session->group->id)
					->where('systemID', $systemID)
					->whereNotIn('sig',$sigList)
					->delete();
			}


			if( count($sigs) > 0 && count($sigs) < 200 )	//200 is safety limit to prevent attacks, no system should have this many sigs
			{
				$doingUpdate = FALSE;
				foreach( $sigs as $sig )
				{
					$sigData = DB::selectOne("SELECT id,sig, type, siteID, description, created_at
															FROM systemsigs
															WHERE systemID=:id
																AND groupID=:group
																 AND sig=:sig", [
																	'id' => $systemID,
																	'group' => Auth::$session->group->id,
																	'sig' => $sig['sig']
																 ]);
					if( $sigData != null )
					{
						if(  $sig['type'] != 'none' || $sig['siteID'] != 0 )
						{
							$doingUpdate = TRUE;
							$update = array(
											'updated_at' => Carbon::now()->toDateTimeString(),
											'siteID' => ( $sig['siteID'] != 0 ) ? $sig['siteID'] : $sigData->siteID,
											'type' => $sig['type'],
											'lastUpdater' => Auth::$session->character_name
											);

							DB::table('systemsigs')->where('id', '=', $sigData->id)->update($update);
						}
					}
					else
					{
						$insert = array();
						$insert['systemID'] = intval($systemID);
						$insert['sig'] = strtoupper($sig['sig']);
						$insert['description'] = "";
						$insert['created_at'] = Carbon::now()->toDateTimeString();
						$insert['siteID'] = intval($sig['siteID']);
						$insert['type'] = $sig['type'];
						$insert['groupID'] = Auth::$session->group->id;
						$insert['sigSize'] = "";	//need to return this value for JS to fail gracefully
						$insert['creator'] = Auth::$session->character_name;

						$id = DB::table('systemsigs')->insert($insert);

						$insert['id'] = $id[0];

						$addedSigs[ $id[0] ] = $insert;

						if( $insert['type'] != 'none' )
						{
							Auth::$session->group->incrementStat('adds', Auth::$session->accessData);
						}
					}
				}

				if( $doingUpdate )
				{
					$this->chainmap->update_system($systemID, array('lastUpdate' => time(),'lastActive' => time() ) );
				}

				$this->response->body(json_encode($addedSigs));
			}
		}
	}

	public function action_edit()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$sigData = json_decode($this->request->body(), true);

		if( !empty($sigData) && isset($sigData['id']) )
		{
			$update['sig'] = strtoupper($sigData['sig']);
			if(isset($sigData['desc']))
			{
				$update['description'] = htmlspecialchars($sigData['desc']);
			}
			$update['updated_at'] = Carbon::now()->toDateTimeString();
			$update['siteID'] = isset($sigData['siteID']) ? intval($sigData['siteID']) : 0;
			$update['type'] = $sigData['type'];

			if( Auth::$session->group->show_sig_size_col )
			{
				$update['sigSize'] = ( is_numeric( $sigData['sigSize'] ) ? $sigData['sigSize'] : ''  );
			}

			$update['lastUpdater'] = Auth::$session->character_name;

			$id = intval($sigData['id']);

			$sig = Signature::findWithGroup(Auth::$session->group->id,$id);
			if($sig == null)
			{
				$this->response->body(json_encode('0'));
				return;
			}

			$sig->fill($update);
			$sig->save();

			$this->chainmap->update_system($sigData['systemID'], array('lastUpdate' => time(), 'lastActive' => time() ) );

			if(!empty($sigData['chainmap_wormhole']) && !empty($sigData['chainmap_wormhole']['hash']))
			{
				if($sigData['chainmap_wormhole']['hash'] == 'none')
				{
					DB::table('wormhole_signatures')
						->where('chainmap_id',  $sigData['chainmap_wormhole']['chainmap_id'])
						->where('signature_id', $id)
						->delete();
				}
				else
				{
					DB::insert('REPLACE INTO wormhole_signatures (`wormhole_hash`, `chainmap_id`,`signature_id`)
								VALUES(:hash, :chainMapID, :id)',
								[
									'hash' => $sigData['chainmap_wormhole']['hash'],
									'chainMapID' => $sigData['chainmap_wormhole']['chainmap_id'],
									'id' => $id
								]);
				}
			}

			Auth::$session->group->incrementStat('updates', Auth::$session->accessData);

			$this->response->body(json_encode('1'));
		}
	}

	public function action_create_wormholes()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$request = json_decode($this->request->body(),true);

		if( isset($request['system_id']) )
		{
			$chainmapID = Auth::$session->accessData['active_chain_map'];
			$chainmap = Chainmap::find($chainmapID,Auth::$session->group->id);
			foreach($request['sigs'] as $sig)
			{
				if(empty($sig['wh_destination']))
					continue;


				$toSysID = $chainmap->find_system_by_name($sig['wh_destination']);
				if( !$toSysID )
					continue;
				
				//permission check
				$sigEntry = Signature::findWithGroup(Auth::$session->group->id, $sig['id']);

				if( $sigEntry == null )
					continue;

				$sigEntry->siteID = $sig['site_id'];
				$sigEntry->save();

				try {
					$chainmap->add_system_to_map($request['system_id'], $toSysID, 0, 0, $sig['site_id']);
				} catch (Exception $e) {

				}

				$whHash = mapUtils::whHashByID($request['system_id'], $toSysID);
				DB::insert('REPLACE INTO wormhole_signatures (`wormhole_hash`, `chainmap_id`,`signature_id`)
							VALUES(:hash, :chainMapID, :id)',
							[
								'hash' => $whHash,
								'chainMapID' => $chainmapID,
								'id' => $sig['id']
							]);

				$this->chainmap->update_system($toSysID, array('lastUpdate' => time() ));
			}

			$this->chainmap->update_system($request['system_id'], array('lastUpdate' => time() ));

			$this->chainmap->rebuild_map_data_cache();
		}

		$this->response->body(json_encode('1'));
	}

	public function action_remove()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		if( isset($_POST['id']) )
		{
			$id = intval($_POST['id']);
			$sigData = DB::selectOne('SELECT *,ss.name as systemName FROM	 systemsigs s
													INNER JOIN solarsystems ss ON ss.id = s.systemID
													WHERE s.id=:id AND s.groupID=:groupID',
													[
														'groupID' => Auth::$session->group->id,
														'id' => $id
													]);
			if($sigData == null)
			{
				$this->response->body(json_encode('0'));
				return;
			}

			DB::table('systemsigs')
				->where('groupID', '=', Auth::$session->group->id)
				->where('id', '=', $id)
				->delete();

			$this->chainmap->update_system($_POST['systemID'], array('lastUpdate' => time() ));

			// delete linked wormholes
			$whlinks = DB::select("SELECT s.*
									FROM wormhole_signatures s
									WHERE s.signature_id=?",[$id]);
			$wormholeHashes = [];
			foreach($whlinks as $link)
			{
				$wormholeHashes[] = $link->wormhole_hash;
			}
			$wormholeHashes = array_unique($wormholeHashes);

			groupUtils::deleteLinkedSigWormholes(Auth::$session->group->id, $wormholeHashes);

			$message = Auth::$session->character_name.' deleted sig "'.$sigData->sig.'" from system '.$sigData->systemName;
			if( $sigData->type != 'none' )
			{
				$message .= '" which was of type '.strtoupper($sigData->type);
			}

			Auth::$session->group->logAction('delsig', $message);
			$this->response->body(json_encode('1'));
		}
	}

	public function action_scanned_systems()
	{
		$this->profiler = NULL;
		$this->auto_render = FALSE;
		$this->response->headers('Content-Type','application/json');
		$this->response->headers('Cache-Control','no-cache, must-revalidate');

		$data = DB::select("SELECT ss.name as system_name, ss.id,
													r.regionName as region_name,
													r.regionID as region_id,
													c.constellationID as constellation_id,
													c.constellationName as constellation_name,
													(SELECT created_at FROM systemsigs
													WHERE systemID = ss.id AND groupID=?
													ORDER BY created_at DESC
													LIMIT 1)
													as last_scan
												FROM solarsystems ss
												INNER JOIN regions r ON(r.regionID=ss.region)
												INNER JOIN constellations c ON(c.constellationID=ss.constellation)
												WHERE ss.id IN (
													SELECT s.systemID FROM	 systemsigs s
													WHERE s.sig !='POS' AND s.groupID=?
													GROUP BY s.systemID
												)",
												[
													Auth::$session->group->id,
													Auth::$session->group->id
												]);

		$this->response->body(json_encode($data));
	}
}
