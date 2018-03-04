<?php

namespace Siggy\ESI;

use Carbon\Carbon;

class OAuthTokenResponse {

	private $accessToken = '';
	private $expiresAt = null;
	private $tokenType = '';
	private $refreshToken = '';


	public function __construct($accessToken, $expiresInSeconds, $tokenType, $refreshToken = '') {
		$this->accessToken = $accessToken;
		$this->refreshToken = $refreshToken;
		$this->expiresAt = Carbon::now()->addSeconds($expiresInSeconds);
	}

	public function getAccessToken(): string {
		return $this->accessToken;
	}

	public function getExpiresAt(): Carbon {
		return $this->expiresAt;
	}

	public function getRefreshToken(): string {
		return $this->refreshToken;
	}
}