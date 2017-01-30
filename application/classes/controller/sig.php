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
			$insert = [
				'systemID' => intval($sigData['systemID']),
				'sig' => strtoupper($sigData['sig']),
				'description' => htmlspecialchars($sigData['desc']),
				'created_at' => Carbon::now()->toDateTimeString(),
				'siteID' => intval($sigData['siteID']),
				'type' => $sigData['type'],
				'groupID' => Auth::$session->group->id,
				'creator' => Auth::$session->character_name,
				'sigSize' => ( isset($sigData['sigSize']) && is_numeric( $sigData['sigSize'] ) ? $sigData['sigSize'] : '' )
			];

			$sig = Signature::create($insert);

			$this->chainmap->update_system($insert['systemID'], array('lastUpdate' => time(),
																'lastActive' => time() )
										);

			Auth::$session->group->incrementStat('adds', Auth::$session->accessData);

			$this->notifierCheck($sig);

			$this->response->body(json_encode([$sig->id => $sig]));
		}
	}

	private function notifierCheck(Signature $sigData)
	{
		foreach( Notifier::allByGroupCharacter(Auth::$session->group->id, Auth::$session->character_id) as $notifier )
		{
			if( $notifier->type == NotificationTypes::SiteFound )
			{
				$data = $notifier->data;
				if( $sigData->siteID == $data->site_id )
				{
					$eventData = array(
										'system_id' => $sigData->systemID,
										'system_name' => miscUtils::systemNameByID($sigData->systemID),
										'site_id' => $sigData->siteID,
										'discoverer_name' => Auth::$session->character_name,
										'discoverer_id' => Auth::$session->character_id,
										'signature' => $sigData->sig
										);

					$charID = 0;
					if( $notifier->scope == 'personal' )
					{
						$charID = Auth::$session->character_id;
					}

					Notification::createFancy(Auth::$session->group->id, $charID, $notifier->type, $eventData);
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
					$sigData = Signature::findByGroupSystemSig(Auth::$session->group->id, $systemID, $sig['sig']);
					if( $sigData != null )
					{
						if(  $sig['type'] != 'none' || $sig['siteID'] != 0 )
						{
							$doingUpdate = TRUE;
							$update = [
										'updated_at' => Carbon::now()->toDateTimeString(),
										'siteID' => ( $sig['siteID'] != 0 ) ? $sig['siteID'] : $sigData->siteID,
										'type' => $sig['type'],
										'lastUpdater' => Auth::$session->character_name
									];
							$sigData->fill($update);
							$sigData->save();
						}
					}
					else
					{
						$insert = [
								'systemID' => intval($systemID),
								'sig' => strtoupper($sig['sig']),
								'description' => "",
								'created_at' => Carbon::now()->toDateTimeString(),
								'siteID' => intval($sig['siteID']),
								'type' => $sig['type'],
								'groupID' => Auth::$session->group->id,
								'sigSize' => "",	//need to return this value for JS to fail gracefully
								'creator' => Auth::$session->character_name
							];

						$sig = Signature::create($insert);

						$addedSigs[ $sig->id ] = $sig;

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

			$sig = Signature::findByGroup(Auth::$session->group->id,$id);
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
					$whConn = WormholeSignature::findByChainMapSig($sigData['chainmap_wormhole']['chainmap_id'], $id);
					if($whConn != null)
					{
						$whConn->delete();
					}
				}
				else
				{
					$replace = [
									'wormhole_hash' => $sigData['chainmap_wormhole']['hash'],
									'chainmap_id' => $sigData['chainmap_wormhole']['chainmap_id'],
									'signature_id' => $id
								];

					WormholeSignature::replace($replace);
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
				$sigEntry = Signature::findByGroup(Auth::$session->group->id, $sig['id']);

				if( $sigEntry == null )
					continue;

				$sigEntry->siteID = $sig['site_id'];
				$sigEntry->save();

				try {
					$chainmap->add_system_to_map($request['system_id'], $toSysID, 0, 0, $sig['site_id']);
				} catch (Exception $e) {

				}

				$whHash = Chainmap::whHashByID($request['system_id'], $toSysID);
				$replace = [
								'wormhole_hash' => $whHash,
								'chainmap_id' => $chainmapID,
								'signature_id' => $sig['id']
							];
				WormholeSignature::replace($replace);

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

			$sigData = Signature::findByGroupWithSystem(Auth::$session->group->id, $id);
			if($sigData == null)
			{
				$this->response->body(json_encode('0'));
				return;
			}

			$sigData->delete();

			$this->chainmap->update_system($_POST['systemID'], array('lastUpdate' => time() ));

			// delete linked wormholes
			$whlinks = WormholeSignature::findAllBySig($id);
			$wormholeHashes = [];
			foreach($whlinks as $link)
			{
				$wormholeHashes[] = $link->wormhole_hash;
			}
			$wormholeHashes = array_unique($wormholeHashes);

			groupUtils::deleteLinkedSigWormholes(Auth::$session->group->id, $wormholeHashes);

			$message = sprintf('%s deleted sig "%s" from system "%s"', Auth::$session->character_name, $sigData->sig, $sigData->system->name);

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
