<?php

namespace Siggy;

use Siggy\ESI\ITokenManager;
use Siggy\ESI\OAuthTokenResponse;

use \miscUtils;

class BackendESITokenManager implements ITokenManager {
	
	public function getAppClientId(): string {
		return config('backend.esi.client_id');
	}

	public function getAppSecretKey(): string {
		return config('backend.esi.secret_key');
	}

	public function getAccessToken(): ?string {
		return (string)miscUtils::getDBCacheItem( 'backendEsiAccessToken' );
	}

	public function getRefreshToken(): ?string {
		return (string)miscUtils::getDBCacheItem( 'backendEsiRefreshToken' );
	}

	public function storeToken(OAuthTokenResponse $token): void {
		miscUtils::storeDBCacheItem( 'backendEsiAccessToken', $token->getAccessToken() );
		miscUtils::storeDBCacheItem( 'backendEsiAccessTokenExpiration', $token->getExpiresAt()->timestamp );
		miscUtils::storeDBCacheItem( 'backendEsiRefreshToken', $token->getRefreshToken() );
	}

	public function getAccessTokenExpirationTimestamp(): int {
		return (int)miscUtils::getDBCacheItem( 'backendEsiAccessTokenExpiration' );
	}
	
	public function shouldAutoRefreshTokens(): bool {
		return true;
	}
}
