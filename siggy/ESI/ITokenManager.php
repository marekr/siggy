<?php

namespace Siggy\ESI;

interface ITokenManager
{
	public function storeToken(OAuthTokenResponse $token): void;
	public function getAccessToken(): ?string;
	public function getRefreshToken(): ?string;
	public function getAccessTokenExpirationTimestamp(): int;
	
	public function shouldAutoRefreshTokens(): bool;
	
	public function getAppClientId(): string;
	public function getAppSecretKey(): string;
}