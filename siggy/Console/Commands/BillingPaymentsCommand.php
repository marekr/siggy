<?php

namespace Siggy\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Pheal\Pheal;
use \PhealHelper;
use Illuminate\Support\Facades\DB;
use \miscUtils;
use \Group;

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

	const playerTrading = 1;
	const marketTransaction = 2;
	const playerDonation = 10;
	const bountyPrizes = 17;
	const insurance = 19;
	const CSPA = 35;
	const corpAccountWithdrawal = 37;
	const brokerFee = 46;
	const manufacturing = 56;
	const bountyPrize = 85;

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

		PhealHelper::configure();
		$pheal = new Pheal( "3523432", "iBfWRWpwZ9I5l7Ynt2Y7ZlxiesY6b7vVhmpHlhzLDMdCFQnaTus4DBgOGxIfwu4M", "corp" );

		$stoppingRef = (float)miscUtils::getDBCacheItem( 'lastProcessedJournalRefID' );

		$largestVisitedRef = 0;
		$fromID = $stoppingRef;

		$continueFetchingTransactions = true;
		$i = 0;

		$queryParams = ['rowCount' => 30];
		do
		{
			$this->info("Fetching some wallet entries");
			$transactions = $pheal->WalletJournal( $queryParams )->entries;

			if(!count($transactions))
			{
				$this->info("exhausted wallet entries");
				$continueFetchingTransactions = false;
				break;
			}

			foreach($transactions as $transaction)
			{
				$largestVisitedRef = max($largestVisitedRef, $transaction['refID']);
				
				//did we reach the end?
				if($transaction['refID'] <= $stoppingRef)
				{
					$this->info("reached old transactions");
					$continueFetchingTransactions = false;
					break;
				}

				$this->processBillingTransaction($transaction);

				//set this query parameter as we go, ultimately it shiould end up at the "end"
				$queryParams['fromID'] = $transaction['refID'];
			}
		} while($continueFetchingTransactions);

		miscUtils::storeDBCacheItem( 'lastProcessedJournalRefID', (string)$largestVisitedRef );
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

	private function processBillingTransaction( $transaction )
	{
		$this->info("Processing payment {$transaction['refID']}");
		if( $transaction['refTypeID'] == self::playerDonation || $transaction['refTypeID'] == self::corpAccountWithdrawal)
		{
			$entryCode = trim(str_replace('DESC:','',$transaction['reason']));
			$entryCode = strtolower($this->superclean($entryCode));
			if( !empty($entryCode) )
			{
				preg_match('/^siggy-([a-zA-Z0-9]{14,})/', $entryCode, $matches);
				if( count($matches) > 0 && isset($matches[1]) )
				{
					$res = DB::selectOne('SELECT eveRefID FROM billing_payments WHERE eveRefID=?',[$transaction['refID']]);

					if( $res == null )
					{
						$paymentCode = strtolower($matches[1]);	//get 14 char "account code"
						$group = Group::findByPaymentCode($paymentCode);

						if( $group != null )
						{
							$insert = [ 'groupID' => $group->id,
												'eveRefID' => $transaction['refID'],
												'paymentTime' => strtotime($transaction['date']),
												'paymentProcessedTime' => time(),
												'paymentAmount' => (float)$transaction['amount'],
												'payeeID' => $transaction['ownerID1'],
												'payeeName' => $transaction['ownerName1'],
												'payeeType' => ($transaction['refTypeID'] == self::corpAccountWithdrawal ? 'corp' : 'char'),
												'argID' => $transaction['argID1'],
												'argName' => $transaction['argName1']
									];
							DB::table( 'billing_payments')->insert($insert);

							$group->applyISKPayment((float)$transaction['amount']);
							$this->info("Applying payment of {$transaction['amount']} ISK to group {$group->id}");
						}
						else
						{
							$this->info("group not found for payment of {$transaction['amount']} with code {$transaction['reason']}");
						}
					}
					else
					{
						//processed already!
						//do nothing
						$this->info("Payment already processed,{$entryCode},{$res->eveRefID}");
					}
				}
			}
		}
	}
}
