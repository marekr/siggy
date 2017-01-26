# ESI\LocationApi

All URIs are relative to *https://esi.tech.ccp.is/dev*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getCharactersCharacterIdLocation**](LocationApi.md#getCharactersCharacterIdLocation) | **GET** /characters/{character_id}/location/ | Get character location
[**getCharactersCharacterIdShip**](LocationApi.md#getCharactersCharacterIdShip) | **GET** /characters/{character_id}/ship/ | Get current ship


# **getCharactersCharacterIdLocation**
> \ESI\Model\GetCharactersCharacterIdLocationOk getCharactersCharacterIdLocation($characterId, $datasource)

Get character location

Information about the characters current location. Returns the current solar system id, and also the current station or structure ID if applicable.  ---  Alternate route: `/v1/characters/{character_id}/location/`  Alternate route: `/legacy/characters/{character_id}/location/`  Alternate route: `/latest/characters/{character_id}/location/`   ---  This route is cached for up to 5 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\LocationApi();
$characterId = 56; // int | An EVE character ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCharactersCharacterIdLocation($characterId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling LocationApi->getCharactersCharacterIdLocation: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **characterId** | **int**| An EVE character ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCharactersCharacterIdLocationOk**](../Model/GetCharactersCharacterIdLocationOk.md)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCharactersCharacterIdShip**
> \ESI\Model\GetCharactersCharacterIdShipOk getCharactersCharacterIdShip($characterId, $datasource)

Get current ship

Get the current ship type, name and id  ---  Alternate route: `/v1/characters/{character_id}/ship/`  Alternate route: `/legacy/characters/{character_id}/ship/`  Alternate route: `/latest/characters/{character_id}/ship/`   ---  This route is cached for up to 5 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\LocationApi();
$characterId = 56; // int | An EVE character ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCharactersCharacterIdShip($characterId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling LocationApi->getCharactersCharacterIdShip: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **characterId** | **int**| An EVE character ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCharactersCharacterIdShipOk**](../Model/GetCharactersCharacterIdShipOk.md)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

