<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \miscUtils;
use \Group;
use Siggy\ESI\Client as ESIClient;
use Siggy\BillingPayment;
use Siggy\BackendESITokenManager;


class BillingPaymentsCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'billing:payments';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Process billing payments';

	const playerDonation = 'player_donation';
	const corpAccountWithdrawal = 'corporation_account_withdrawal';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', 0);
		set_time_limit(0);

		$corpId = config('backend.payment_corp_id');
		$division = config('backend.payment_division');

		$manager = new BackendESITokenManager();
		$client = new ESIClient($manager);

		$continueFetchingTransactions = true;
		$lastProcessedRefId = null;
		do
		{
			$this->info("Fetching some wallet entries, from {$lastProcessedRefId}");
			
			$transactions = $client->getCorporationWalletDivisionJournal($corpId, $division, $lastProcessedRefId);

			if($transactions == null)
			{
				$this->info("no transactions returned");
				break;
			}

			if(!count($transactions))
			{
				$this->info("exhausted wallet entries");
				$continueFetchingTransactions = false;
				break;
			}

			foreach($transactions as $transaction)
			{
				//process and check if we should stop
				if(!$this->processBillingTransaction($transaction))
				{
					$this->info("reached old transactions");
					$continueFetchingTransactions = false;
					break;
				}

				//set this query parameter as we go, ultimately it shiould end up at the "end"
				$lastProcessedRefId = $transaction->ref_id;
			}
		} while($continueFetchingTransactions);
	}
	

	private function superclean($text)
	{
		// Strip HTML Tags
		$clear = strip_tags($text);
		// Clean up things like &amp;
		$clear = html_entity_decode($clear);
		// Strip out any url-encoded stuff
		$clear = urldecode($clear);
		// Replace non-AlNum characters with space
		$clear = preg_replace('/[^-a-z0-9]+/i', ' ', $clear);
		// Replace Multiple spaces with single space
		$clear = preg_replace('/ +/', ' ', $clear);
		// Trim the string of leading/trailing space
		$clear = trim($clear);

		return $clear;
	}


	private function processBillingTransaction( $transaction ): bool
	{
		$this->info("Processing payment {$transaction->ref_id}");
		if( $transaction->ref_type == self::playerDonation || $transaction->ref_type == self::corpAccountWithdrawal)
		{
			if(!property_exists($transaction, 'reason')) {
				$this->info("Missing reason {$transaction->ref_id}");
				return true;
			}

			print_r($transaction);
			$entryCode = trim(str_replace('DESC:','',$transaction->reason));
			$entryCode = strtolower($this->superclean($entryCode));
			if( !empty($entryCode) )
			{
				preg_match('/^siggy-([a-zA-Z0-9]{14,})/', $entryCode, $matches);
				if( count($matches) > 0 && isset($matches[1]) )
				{
					$res = BillingPayment::where('ref_id', $transaction->ref_id)->first();
					
					if( $res == null )
					{
						$paymentCode = strtolower($matches[1]);	//get 14 char "account code"
						$group = Group::findByPaymentCode($paymentCode);

						if( $group != null )
						{
							$insert = [ 'group_id' => $group->id,
												'ref_id' => $transaction->ref_id,
												'paid_at' => Carbon::parse($transaction->date),
												'processed_at' => Carbon::now(),
												'amount' => (float)$transaction->amount
									];

							if($transaction->ref_type == self::corpAccountWithdrawal)
							{
								$insert['payer_type'] = 'corp';
								$insert['payer_corporation_id'] = $transaction->first_party_id;
								$insert['payer_character_id'] = $transaction->extra_info->character_id;
							}
							else
							{
								$insert['payer_type'] = 'char';
								$insert['payer_character_id'] = $transaction->first_party_id;
							}

							BillingPayment::create($insert);

							$group->applyISKPayment((float)$transaction->amount);
							$this->info("Applying payment of {$transaction->amount} ISK to group {$group->id}");
						}
						else
						{
							$this->info("group not found for payment of {$transaction->amount} with code {$transaction->reason}");
						}

						return true;
					}
					else
					{
						//processed already!
						//do nothing
						$this->info("Payment already processed,{$entryCode},{$res->ref_id}");

						//we reached a old payment, stop
						return false;
					}
				}
			}
		}

		//continue processing
		return true;
	}
}
