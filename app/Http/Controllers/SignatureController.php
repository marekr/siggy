<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Siggy\StandardResponse;
use App\Facades\Auth;
use App\Facades\SiggySession;
use \Signature;
use Siggy\Chainmap;
use \Notifier;
use \NotificationTypes;
use \WormholeSignature;
use \groupUtils;
use Siggy\Notification;
use \miscUtils;


class SignatureController extends BaseController {

	public $chainmap = null;
	
	public function getChainmap()
	{
		if($this->chainmap == null)
		{
			if( SiggySession::getAccessData()['active_chain_map'] )
			{
				$this->chainmap = Chainmap::find(SiggySession::getAccessData()['active_chain_map'],SiggySession::getGroup()->id);
			}
		}

		return $this->chainmap;
	}


	public function add(Request $request)
	{
		$sigData = json_decode($request->getContent(), true);

		if( !empty($sigData) && isset($sigData['systemID']) )
		{
			$insert = [
				'systemID' => intval($sigData['systemID']),
				'sig' => strtoupper($sigData['sig']),
				'description' => htmlspecialchars($sigData['description']),
				'created_at' => Carbon::now()->toDateTimeString(),
				'siteID' => intval($sigData['siteID']),
				'type' => $sigData['type'],
				'groupID' => SiggySession::getGroup()->id,
				'creator' => SiggySession::getCharacterName(),
				'sigSize' => ( isset($sigData['sigSize']) && is_numeric( $sigData['sigSize'] ) ? $sigData['sigSize'] : '' )
			];

			$sig = Signature::create($insert);

			$this->getChainmap()->update_system($insert['systemID'], array('lastUpdate' => time(),
																'lastActive' => time() )
										);
										
			SiggySession::getGroup()->incrementStat('adds', SiggySession::getAccessData());

			$this->notifierCheck($sig);

			return response()->json([$sig->id => $sig]);
		}
	}

	private function notifierCheck(Signature $sigData)
	{
		foreach( Notifier::allByGroupCharacter(SiggySession::getGroup()->id, SiggySession::getCharacterId()) as $notifier )
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
										'discoverer_name' => SiggySession::getCharacterName(),
										'discoverer_id' => SiggySession::getCharacterId(),
										'signature' => $sigData->sig
										);

					$charID = 0;
					if( $notifier->scope == 'personal' )
					{
						$charID = SiggySession::getCharacterId();
					}

					Notification::createFancy(SiggySession::getGroup()->id, $charID, $notifier->type, $eventData);
				}
			}
		}
	}

	public function mass_add(Request $request)
	{
		$postData = json_decode($request->getContent(), true);

		//load settings to trigger localization
		$this->loadSettings();

		if( isset($postData['system_id']) && isset($postData['blob']) && !empty($postData['blob']) )
		{
			$sigs = miscUtils::parseIngameSigExport( $postData['blob'] );

			$systemID = intval($postData['system_id']);

			$addedSigs = [];

			$deleteNonExisting = isset($postData['delete_nonexistent_sigs']) ? (int)$postData['delete_nonexistent_sigs'] : 0;
			if( $deleteNonExisting )
			{
				$sigList = [];
				foreach( $sigs as $sig )
				{
					$sigList[] = $sig['sig'];
				}

				DB::table('systemsigs')
					->where('groupID', SiggySession::getGroup()->id)
					->where('systemID', $systemID)
					->whereNotIn('sig',$sigList)
					->delete();
			}


			if( count($sigs) > 0 && count($sigs) < 200 )	//200 is safety limit to prevent attacks, no system should have this many sigs
			{
				$doingUpdate = FALSE;
				foreach( $sigs as $sig )
				{
					$sigData = Signature::findByGroupSystemSig(SiggySession::getGroup()->id, $systemID, $sig['sig']);
					if( $sigData != null )
					{
						if(  $sig['type'] != 'none' || $sig['siteID'] != 0 )
						{
							$doingUpdate = TRUE;
							$update = [
										'updated_at' => Carbon::now()->toDateTimeString(),
										'siteID' => ( $sig['siteID'] != 0 ) ? $sig['siteID'] : $sigData->siteID,
										'type' => $sig['type'],
										'lastUpdater' => SiggySession::getCharacterName()
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
								'groupID' => SiggySession::getGroup()->id,
								'sigSize' => "",	//need to return this value for JS to fail gracefully
								'creator' => SiggySession::getCharacterName()
							];

						$sig = Signature::create($insert);

						$addedSigs[ $sig->id ] = $sig;

						if( $insert['type'] != 'none' )
						{
							SiggySession::getGroup()->incrementStat('adds', SiggySession::getAccessData());
						}
					}
				}

				if( $doingUpdate )
				{
					$this->getChainmap()->update_system($systemID, array('lastUpdate' => time(),'lastActive' => time() ) );
				}

				return response()->json(StandardResponse::ok($addedSigs));
				return;
			}
		}

		return response()->json(StandardResponse::ok());
	}

	public function edit(Request $request)
	{
		$sigData = json_decode($request->getContent(), true);

		if( !empty($sigData) && isset($sigData['id']) )
		{
			$update['sig'] = strtoupper($sigData['sig']);
			if(isset($sigData['description']))
			{
				$update['description'] = htmlspecialchars($sigData['description']);
			}
			$update['updated_at'] = Carbon::now()->toDateTimeString();
			$update['siteID'] = isset($sigData['siteID']) ? intval($sigData['siteID']) : 0;
			$update['type'] = $sigData['type'];

			if( SiggySession::getGroup()->show_sig_size_col )
			{
				$update['sigSize'] = ( is_numeric( $sigData['sigSize'] ) ? $sigData['sigSize'] : ''  );
			}

			$update['lastUpdater'] = SiggySession::getCharacterName();

			$id = intval($sigData['id']);

			$sig = Signature::findByGroup(SiggySession::getGroup()->id,$id);
			if($sig == null)
			{
				return response()->json('0');
			}

			$sig->fill($update);
			$sig->save();

			$this->getChainmap()->update_system($sigData['systemID'], array('lastUpdate' => time(), 'lastActive' => time() ) );

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

			SiggySession::getGroup()->incrementStat('updates', SiggySession::getAccessData());

			return response()->json('1');
		}
	}

	public function create_wormholes($request)
	{
		$request = json_decode($request->getContent(),true);

		if( isset($request['system_id']) )
		{
			$chainmapID = SiggySession::getAccessData()['active_chain_map'];
			$chainmap = Chainmap::find($chainmapID,SiggySession::getGroup()->id);
			foreach($request['sigs'] as $sig)
			{
				if(empty($sig['wh_destination']))
					continue;


				$toSysID = $chainmap->find_system_by_name($sig['wh_destination']);
				if( !$toSysID )
					continue;
				
				//permission check
				$sigEntry = Signature::findByGroup(SiggySession::getGroup()->id, $sig['id']);

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

				$this->getChainmap()->update_system($toSysID, array('lastUpdate' => time() ));
			}

			$this->getChainmap()->update_system($request['system_id'], array('lastUpdate' => time() ));

			$this->getChainmap()->rebuild_map_data_cache();
		}

		return response()->json('1');
	}

	public function remove()
	{
		if( isset($_POST['id']) )
		{
			$id = intval($_POST['id']);

			$sigData = Signature::findByGroupWithSystem(SiggySession::getGroup()->id, $id);
			if($sigData == null)
			{
				return response()->json('0');
			}

			$sigData->delete();

			$this->getChainmap()->update_system($_POST['systemID'], array('lastUpdate' => time() ));

			// delete linked wormholes
			$whlinks = WormholeSignature::findAllBySig($id);
			$wormholeHashes = [];
			foreach($whlinks as $link)
			{
				$wormholeHashes[] = $link->wormhole_hash;
			}
			$wormholeHashes = array_unique($wormholeHashes);

			groupUtils::deleteLinkedSigWormholes(SiggySession::getGroup()->id, $wormholeHashes);

			$message = sprintf('%s deleted sig "%s" from system "%s"', SiggySession::getCharacterName(), $sigData->sig, $sigData->system->name);

			if( $sigData->type != 'none' )
			{
				$message .= '" which was of type '.strtoupper($sigData->type);
			}

			SiggySession::getGroup()->logAction('delsig', $message);
			return response()->json('1');
		}
	}

	public function scanned_systems()
	{
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
												INNER JOIN eve_map_regions r ON(r.regionID=ss.region)
												INNER JOIN eve_map_constellations c ON(c.constellationID=ss.constellation)
												WHERE ss.id IN (
													SELECT s.systemID FROM	 systemsigs s
													WHERE s.sig !='POS' AND s.groupID=?
													GROUP BY s.systemID
												)",
												[
													SiggySession::getGroup()->id,
													SiggySession::getGroup()->id
												]);

		return response()->json($data);
	}
}