# ESI\SkillsApi

All URIs are relative to *https://esi.tech.ccp.is/dev*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getCharactersCharacterIdSkillqueue**](SkillsApi.md#getCharactersCharacterIdSkillqueue) | **GET** /characters/{character_id}/skillqueue/ | Get character&#39;s skill queue
[**getCharactersCharacterIdSkills**](SkillsApi.md#getCharactersCharacterIdSkills) | **GET** /characters/{character_id}/skills/ | Get character skills


# **getCharactersCharacterIdSkillqueue**
> \ESI\Model\GetCharactersCharacterIdSkillqueue200Ok[] getCharactersCharacterIdSkillqueue($characterId, $datasource)

Get character's skill queue

List the configured skill queue for the given character  ---  Alternate route: `/v2/characters/{character_id}/skillqueue/`  Alternate route: `/legacy/characters/{character_id}/skillqueue/`  Alternate route: `/latest/characters/{character_id}/skillqueue/`   ---  This route is cached for up to 120 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\SkillsApi();
$characterId = 56; // int | Character id of the target character
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCharactersCharacterIdSkillqueue($characterId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SkillsApi->getCharactersCharacterIdSkillqueue: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **characterId** | **int**| Character id of the target character |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCharactersCharacterIdSkillqueue200Ok[]**](../Model/GetCharactersCharacterIdSkillqueue200Ok.md)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCharactersCharacterIdSkills**
> \ESI\Model\GetCharactersCharacterIdSkillsOk getCharactersCharacterIdSkills($characterId, $datasource)

Get character skills

List all trained skills for the given character  ---  Alternate route: `/v3/characters/{character_id}/skills/`  Alternate route: `/latest/characters/{character_id}/skills/`   ---  This route is cached for up to 120 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\SkillsApi();
$characterId = 56; // int | An EVE character ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCharactersCharacterIdSkills($characterId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SkillsApi->getCharactersCharacterIdSkills: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **characterId** | **int**| An EVE character ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCharactersCharacterIdSkillsOk**](../Model/GetCharactersCharacterIdSkillsOk.md)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

