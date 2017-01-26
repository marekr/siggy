<?php
/**
 * PlanetaryInteractionApi
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
 * PlanetaryInteractionApi Class Doc Comment
 *
 * @category Class
 * @package  ESI
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class PlanetaryInteractionApi
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
     * @return PlanetaryInteractionApi
     */
    public function setApiClient(\ESI\ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation getCharactersCharacterIdPlanets
     *
     * Get colonies
     *
     * @param int $characterId Character id of the target character (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return \ESI\Model\GetCharactersCharacterIdPlanets200Ok[]
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdPlanets($characterId, $datasource = null)
    {
        list($response) = $this->getCharactersCharacterIdPlanetsWithHttpInfo($characterId, $datasource);
        return $response;
    }

    /**
     * Operation getCharactersCharacterIdPlanetsWithHttpInfo
     *
     * Get colonies
     *
     * @param int $characterId Character id of the target character (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of \ESI\Model\GetCharactersCharacterIdPlanets200Ok[], HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdPlanetsWithHttpInfo($characterId, $datasource = null)
    {
        // verify the required parameter 'characterId' is set
        if ($characterId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $characterId when calling getCharactersCharacterIdPlanets');
        }
        // parse inputs
        $resourcePath = "/characters/{character_id}/planets/";
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
        if ($characterId !== null) {
            $resourcePath = str_replace(
                "{" . "character_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($characterId),
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
                '\ESI\Model\GetCharactersCharacterIdPlanets200Ok[]',
                '/characters/{character_id}/planets/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\ESI\Model\GetCharactersCharacterIdPlanets200Ok[]', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdPlanets200Ok[]', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdPlanetsForbidden', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdPlanetsInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getCharactersCharacterIdPlanetsPlanetId
     *
     * Get colony layout
     *
     * @param int $characterId Character id of the target character (required)
     * @param int $planetId Planet id of the target planet (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return \ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdOk
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdPlanetsPlanetId($characterId, $planetId, $datasource = null)
    {
        list($response) = $this->getCharactersCharacterIdPlanetsPlanetIdWithHttpInfo($characterId, $planetId, $datasource);
        return $response;
    }

    /**
     * Operation getCharactersCharacterIdPlanetsPlanetIdWithHttpInfo
     *
     * Get colony layout
     *
     * @param int $characterId Character id of the target character (required)
     * @param int $planetId Planet id of the target planet (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of \ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdOk, HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getCharactersCharacterIdPlanetsPlanetIdWithHttpInfo($characterId, $planetId, $datasource = null)
    {
        // verify the required parameter 'characterId' is set
        if ($characterId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $characterId when calling getCharactersCharacterIdPlanetsPlanetId');
        }
        // verify the required parameter 'planetId' is set
        if ($planetId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $planetId when calling getCharactersCharacterIdPlanetsPlanetId');
        }
        // parse inputs
        $resourcePath = "/characters/{character_id}/planets/{planet_id}/";
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
        if ($characterId !== null) {
            $resourcePath = str_replace(
                "{" . "character_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($characterId),
                $resourcePath
            );
        }
        // path params
        if ($planetId !== null) {
            $resourcePath = str_replace(
                "{" . "planet_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($planetId),
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
                '\ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdOk',
                '/characters/{character_id}/planets/{planet_id}/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdOk', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdOk', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdForbidden', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdNotFound', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetCharactersCharacterIdPlanetsPlanetIdInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getUniverseSchematicsSchematicId
     *
     * Get schematic information
     *
     * @param int $schematicId A PI schematic ID (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return \ESI\Model\GetUniverseSchematicsSchematicIdOk
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getUniverseSchematicsSchematicId($schematicId, $datasource = null)
    {
        list($response) = $this->getUniverseSchematicsSchematicIdWithHttpInfo($schematicId, $datasource);
        return $response;
    }

    /**
     * Operation getUniverseSchematicsSchematicIdWithHttpInfo
     *
     * Get schematic information
     *
     * @param int $schematicId A PI schematic ID (required)
     * @param string $datasource The server name you would like data from (optional, default to tranquility)
     * @return Array of \ESI\Model\GetUniverseSchematicsSchematicIdOk, HTTP status code, HTTP response headers (array of strings)
     * @throws \ESI\ApiException on non-2xx response
     */
    public function getUniverseSchematicsSchematicIdWithHttpInfo($schematicId, $datasource = null)
    {
        // verify the required parameter 'schematicId' is set
        if ($schematicId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $schematicId when calling getUniverseSchematicsSchematicId');
        }
        // parse inputs
        $resourcePath = "/universe/schematics/{schematic_id}/";
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
        if ($schematicId !== null) {
            $resourcePath = str_replace(
                "{" . "schematic_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($schematicId),
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
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\ESI\Model\GetUniverseSchematicsSchematicIdOk',
                '/universe/schematics/{schematic_id}/'
            );

            return array($this->apiClient->getSerializer()->deserialize($response, '\ESI\Model\GetUniverseSchematicsSchematicIdOk', $httpHeader), $statusCode, $httpHeader);
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetUniverseSchematicsSchematicIdOk', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetUniverseSchematicsSchematicIdNotFound', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\ESI\Model\GetUniverseSchematicsSchematicIdInternalServerError', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

}
