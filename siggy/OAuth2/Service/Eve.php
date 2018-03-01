<?php

namespace Siggy\OAuth2\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\OAuth2\Service\AbstractService;

class Eve extends AbstractService
{
    const SCOPE_ESI_UI_WRITE_WAYPOINT                = 'esi-ui.write_waypoint.v1';
    const SCOPE_ESI_LOCATION_READ_LOCATION                = 'esi-location.read_location.v1';
    const SCOPE_ESI_LOCATION_READ_SHIP_TYPE                = 'esi-location.read_ship_type.v1';
    const SCOPE_ESI_LOCATION_READ_ONLINE                = 'esi-location.read_online.v1';
    const SCOPE_ESI_UI_OPEN_WINDOW                = 'esi-ui.open_window.v1';
    
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://login.eveonline.com');
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri($this->baseApiUri . '/oauth/authorize');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri($this->baseApiUri . '/oauth/token');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || ! is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        $token->setLifetime($data['expires_in']);
        $token->setRefreshToken($data['refresh_token']);

        unset($data['access_token']);
		unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return array('Accept' => 'application/json');
    }

    /**
     * Return any additional headers always needed for this service implementation's API calls.
     *
     * @return array
     */
    protected function getExtraApiHeaders()
    {
        return array('Accept' => 'application/json');
    }
}