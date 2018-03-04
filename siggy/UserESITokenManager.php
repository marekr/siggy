<?php

namespace Siggy;

use Siggy\ESI\OAuthTokenResponse;
use Siggy\ESI\ITokenManager;

use \miscUtils;
use App\Facades\Auth;
use Carbon\Carbon;

class UserESITokenManager implements ITokenManager {
	private $sso = null;
	
	public function __construct() {
		$this->sso = Auth::user()->getActiveSSOCharacter();
	}

	public function getAppClientId(): string {
		return config('sso.credentials.key');
	}

	public function getAppSecretKey(): string {
		return config('sso.credentials.secre');
	}

	public function getAccessToken(): ?string {

		if($this->sso == null){
			return null;
		}

		return $this->sso->access_token;
	}
	
	public function getAccessTokenExpirationTimestamp(): int {
		if($this->sso == null){
			return null;
		}
		
		return Carbon::parse($this->sso->access_token_expiration)->timestamp;
	}

	public function getRefreshToken(): ?string {
		return null;
	}

	public function shouldAutoRefreshTokens(): bool {
		return false;
	}
	
	public function storeToken(OAuthTokenResponse $token): void {
		
	}
}

