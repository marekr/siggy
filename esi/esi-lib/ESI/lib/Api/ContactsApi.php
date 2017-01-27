<?php
/**
 * ContactsApi
 * PHP version 5
 *
 * @category Class
 * @package  ESI
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * EVE Swagger Interface
 *
 * An OpenAPI for EVE Online
 *
 * OpenAPI spec version: 0.3.9
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace ESI\Api;

use \ESI\Configuration;
use \ESI\ApiClient;
use \ESI\ApiException;
use \ESI\ObjectSerializer;

/**
 * ContactsApi Class Doc Comment
 *
 * @category Class
 * @package  ESI
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class ContactsApi
{

    /**
     * API Client
     *
     * @var \ESI\ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param \ESI\ApiClient|null $apiClient The api client to use
     */
    public function __construct(\ESI\ApiClient $apiClient = null)
    {
        if ($apiClient == null) {
            $apiClient = new ApiClient();
            $apiClient->getConfig()->setHost('https://esi.tech.ccp.is/dev');
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return \ESI\ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param \ESI\ApiClient $apiClient set the API client
     *
     * @return ContactsApi
     */
    public function setApiClient(\ESI\ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation deleteCharactersCharacterIdContacts
     *
     * Delete contacts
     *
     * @param int $character_id ID for a character (required)
     * @param int[] $contact_ids A list of contacts to edit (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return void
     * @throws \ESI\ApiException on non-2xx response
     */
    public function deleteCharactersCharacterIdContacts($character_id, $contact_ids, $datasource = null)
    {
        list($response) = $this->deleteCharactersCharacterIdContactsWithHttpInfo($character_id, $contact_ids, $datasource);
        return $response;
    }

    /**
     * Operation deleteCharactersCharacterIdContactsWithHttpInfo
     *
     * Delete contacts
     *
     * @param int $character_id ID for a character (required)
     * @param int[] $contact_ids A list of contacts to edit (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of null, HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function deleteCharactersCharacterIdContactsWithHttpInfo($character_id, $contact_ids, $datasource = null)
    {
        // verify the required parameter 'character_id' is set
        if ($character_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $character_id when calling deleteCharactersCharacterIdContacts');
        }
        // verify the required parameter 'contact_ids' is set
        if ($contact_ids === null) {
            throw new \InvalidArgumentException('Missing the required parameter $contact_ids when calling deleteCharactersCharacterIdContacts');
        }
        // parse inputs
        $resourcePath = "/characters/{character_id}/contacts/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array());

        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // path params
        if ($character_id !== null) {
            $resourcePath = str_replace(
                "{" . "character_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($character_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($contact_ids)) {
            $_tempBody = $contact_ids;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'DELETE',
                $queryParams,
                $httpBody,
                $headerParams,
                null,
                '/characters/{character_id}/contacts/'
            );

            return array(null, $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\DeleteCharactersCharacterIdContactsForbidden', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\DeleteCharactersCharacterIdContactsInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getCharactersCharacterIdContacts
     *
     * Get contacts
     *
     * @param int $character_id ID for a character (required)
     * @param int $page page integer (optional, default to 1)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return \ESI\Model\GetCharactersCharacterIdContacts200Ok[]
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdContacts($character_id, $page = null, $datasource = null)
    {
        list($response) = $this->getCharactersCharacterIdContactsWithHttpInfo($character_id, $page, $datasource);
        return $response;
    }

    /**
     * Operation getCharactersCharacterIdContactsWithHttpInfo
     *
     * Get contacts
     *
     * @param int $character_id ID for a character (required)
     * @param int $page page integer (optional, default to 1)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of \ESI\Model\GetCharactersCharacterIdContacts200Ok[], HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdContactsWithHttpInfo($character_id, $page = null, $datasource = null)
    {
        // verify the required parameter 'character_id' is set
        if ($character_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $character_id when calling getCharactersCharacterIdContacts');
        }
        if (!is_null($page) && ($page < 1.0)) {
            throw new \InvalidArgumentException('invalid value for "$page" when calling ContactsApi.getCharactersCharacterIdContacts, must be bigger than or equal to 1.0.');
        }

        // parse inputs
        $resourcePath = "/characters/{character_id}/contacts/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array());

        // query params
        if ($page !== null) {
            $queryParams['page'] = $this->apiClient->getSerializer()->toQueryValue($page);
        }
        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // path params
        if ($character_id !== null) {
            $resourcePath = str_replace(
                "{" . "character_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($character_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\ESI\Model\GetCharactersCharacterIdContacts200Ok[]',
                '/characters/{character_id}/contacts/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\ESI\Model\GetCharactersCharacterIdContacts200Ok[]', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdContacts200Ok[]', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdContactsForbidden', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdContactsInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getCharactersCharacterIdContactsLabels
     *
     * Get contact labels
     *
     * @param int $character_id ID for a character (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return \ESI\Model\GetCharactersCharacterIdContactsLabels200Ok[]
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdContactsLabels($character_id, $datasource = null)
    {
        list($response) = $this->getCharactersCharacterIdContactsLabelsWithHttpInfo($character_id, $datasource);
        return $response;
    }

    /**
     * Operation getCharactersCharacterIdContactsLabelsWithHttpInfo
     *
     * Get contact labels
     *
     * @param int $character_id ID for a character (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of \ESI\Model\GetCharactersCharacterIdContactsLabels200Ok[], HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdContactsLabelsWithHttpInfo($character_id, $datasource = null)
    {
        // verify the required parameter 'character_id' is set
        if ($character_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $character_id when calling getCharactersCharacterIdContactsLabels');
        }
        // parse inputs
        $resourcePath = "/characters/{character_id}/contacts/labels/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array());

        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // path params
        if ($character_id !== null) {
            $resourcePath = str_replace(
                "{" . "character_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($character_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\ESI\Model\GetCharactersCharacterIdContactsLabels200Ok[]',
                '/characters/{character_id}/contacts/labels/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\ESI\Model\GetCharactersCharacterIdContactsLabels200Ok[]', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdContactsLabels200Ok[]', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdContactsLabelsForbidden', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdContactsLabelsInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation postCharactersCharacterIdContacts
     *
     * Add contacts
     *
     * @param int $character_id ID for a character (required)
     * @param float $standing Standing for the new contact (required)
     * @param int[] $contact_ids A list of contacts to add (required)
     * @param bool $watched Whether the new contact should be watched, note this is only effective on characters (optional, default to false)
     * @param int $label_id Add a custom label to the new contact (optional, default to 0)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return int[]
     * @throws \ESI\ApiException on non-2xx response
     */
    public function postCharactersCharacterIdContacts($character_id, $standing, $contact_ids, $watched = null, $label_id = null, $datasource = null)
    {
        list($response) = $this->postCharactersCharacterIdContactsWithHttpInfo($character_id, $standing, $contact_ids, $watched, $label_id, $datasource);
        return $response;
    }

    /**
     * Operation postCharactersCharacterIdContactsWithHttpInfo
     *
     * Add contacts
     *
     * @param int $character_id ID for a character (required)
     * @param float $standing Standing for the new contact (required)
     * @param int[] $contact_ids A list of contacts to add (required)
     * @param bool $watched Whether the new contact should be watched, note this is only effective on characters (optional, default to false)
     * @param int $label_id Add a custom label to the new contact (optional, default to 0)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of int[], HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function postCharactersCharacterIdContactsWithHttpInfo($character_id, $standing, $contact_ids, $watched = null, $label_id = null, $datasource = null)
    {
        // verify the required parameter 'character_id' is set
        if ($character_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $character_id when calling postCharactersCharacterIdContacts');
        }
        // verify the required parameter 'standing' is set
        if ($standing === null) {
            throw new \InvalidArgumentException('Missing the required parameter $standing when calling postCharactersCharacterIdContacts');
        }
        if (($standing > 10.0)) {
            throw new \InvalidArgumentException('invalid value for "$standing" when calling ContactsApi.postCharactersCharacterIdContacts, must be smaller than or equal to 10.0.');
        }
        if (($standing < -10.0)) {
            throw new \InvalidArgumentException('invalid value for "$standing" when calling ContactsApi.postCharactersCharacterIdContacts, must be bigger than or equal to -10.0.');
        }

        // verify the required parameter 'contact_ids' is set
        if ($contact_ids === null) {
            throw new \InvalidArgumentException('Missing the required parameter $contact_ids when calling postCharactersCharacterIdContacts');
        }
        // parse inputs
        $resourcePath = "/characters/{character_id}/contacts/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array());

        // query params
        if ($standing !== null) {
            $queryParams['standing'] = $this->apiClient->getSerializer()->toQueryValue($standing);
        }
        // query params
        if ($watched !== null) {
            $queryParams['watched'] = $this->apiClient->getSerializer()->toQueryValue($watched);
        }
        // query params
        if ($label_id !== null) {
            $queryParams['label_id'] = $this->apiClient->getSerializer()->toQueryValue($label_id);
        }
        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // path params
        if ($character_id !== null) {
            $resourcePath = str_replace(
                "{" . "character_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($character_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($contact_ids)) {
            $_tempBody = $contact_ids;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'POST',
                $queryParams,
                $httpBody,
                $headerParams,
                'int[]',
                '/characters/{character_id}/contacts/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, 'int[]', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), 'int[]', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\PostCharactersCharacterIdContactsForbidden', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\PostCharactersCharacterIdContactsInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation putCharactersCharacterIdContacts
     *
     * Edit contacts
     *
     * @param int $character_id ID for a character (required)
     * @param float $standing Standing for the contact (required)
     * @param int[] $contact_ids A list of contacts to edit (required)
     * @param bool $watched Whether the contact should be watched, note this is only effective on characters (optional, default to false)
     * @param int $label_id Add a custom label to the contact, use 0 for clearing label (optional, default to 0)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return void
     * @throws \ESI\ApiException on non-2xx response
     */
    public function putCharactersCharacterIdContacts($character_id, $standing, $contact_ids, $watched = null, $label_id = null, $datasource = null)
    {
        list($response) = $this->putCharactersCharacterIdContactsWithHttpInfo($character_id, $standing, $contact_ids, $watched, $label_id, $datasource);
        return $response;
    }

    /**
     * Operation putCharactersCharacterIdContactsWithHttpInfo
     *
     * Edit contacts
     *
     * @param int $character_id ID for a character (required)
     * @param float $standing Standing for the contact (required)
     * @param int[] $contact_ids A list of contacts to edit (required)
     * @param bool $watched Whether the contact should be watched, note this is only effective on characters (optional, default to false)
     * @param int $label_id Add a custom label to the contact, use 0 for clearing label (optional, default to 0)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of null, HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function putCharactersCharacterIdContactsWithHttpInfo($character_id, $standing, $contact_ids, $watched = null, $label_id = null, $datasource = null)
    {
        // verify the required parameter 'character_id' is set
        if ($character_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $character_id when calling putCharactersCharacterIdContacts');
        }
        // verify the required parameter 'standing' is set
        if ($standing === null) {
            throw new \InvalidArgumentException('Missing the required parameter $standing when calling putCharactersCharacterIdContacts');
        }
        if (($standing > 10.0)) {
            throw new \InvalidArgumentException('invalid value for "$standing" when calling ContactsApi.putCharactersCharacterIdContacts, must be smaller than or equal to 10.0.');
        }
        if (($standing < -10.0)) {
            throw new \InvalidArgumentException('invalid value for "$standing" when calling ContactsApi.putCharactersCharacterIdContacts, must be bigger than or equal to -10.0.');
        }

        // verify the required parameter 'contact_ids' is set
        if ($contact_ids === null) {
            throw new \InvalidArgumentException('Missing the required parameter $contact_ids when calling putCharactersCharacterIdContacts');
        }
        // parse inputs
        $resourcePath = "/characters/{character_id}/contacts/";
        $httpBody = '';
        $queryParams = array();
        $headerParams = array();
        $formParams = array();
        $_header_accept = $this->apiClient->selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(array());

        // query params
        if ($standing !== null) {
            $queryParams['standing'] = $this->apiClient->getSerializer()->toQueryValue($standing);
        }
        // query params
        if ($watched !== null) {
            $queryParams['watched'] = $this->apiClient->getSerializer()->toQueryValue($watched);
        }
        // query params
        if ($label_id !== null) {
            $queryParams['label_id'] = $this->apiClient->getSerializer()->toQueryValue($label_id);
        }
        // query params
        if ($datasource !== null) {
            $queryParams['datasource'] = $this->apiClient->getSerializer()->toQueryValue($datasource);
        }
        // path params
        if ($character_id !== null) {
            $resourcePath = str_replace(
                "{" . "character_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($character_id),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($contact_ids)) {
            $_tempBody = $contact_ids;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'PUT',
                $queryParams,
                $httpBody,
                $headerParams,
                null,
                '/characters/{character_id}/contacts/'
            );

            return array(null, $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\PutCharactersCharacterIdContactsForbidden', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\PutCharactersCharacterIdContactsInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

}
