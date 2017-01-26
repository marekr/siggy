# ESI\CorporationApi

All URIs are relative to *https://esi.tech.ccp.is/dev*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getCorporationsCorporationId**](CorporationApi.md#getCorporationsCorporationId) | **GET** /corporations/{corporation_id}/ | Get corporation information
[**getCorporationsCorporationIdAlliancehistory**](CorporationApi.md#getCorporationsCorporationIdAlliancehistory) | **GET** /corporations/{corporation_id}/alliancehistory/ | Get alliance history
[**getCorporationsCorporationIdIcons**](CorporationApi.md#getCorporationsCorporationIdIcons) | **GET** /corporations/{corporation_id}/icons/ | Get corporation icon
[**getCorporationsCorporationIdMembers**](CorporationApi.md#getCorporationsCorporationIdMembers) | **GET** /corporations/{corporation_id}/members/ | Get corporation members
[**getCorporationsCorporationIdRoles**](CorporationApi.md#getCorporationsCorporationIdRoles) | **GET** /corporations/{corporation_id}/roles/ | Get corporation member roles
[**getCorporationsNames**](CorporationApi.md#getCorporationsNames) | **GET** /corporations/names/ | Get corporation names


# **getCorporationsCorporationId**
> \ESI\Model\GetCorporationsCorporationIdOk getCorporationsCorporationId($corporationId, $datasource)

Get corporation information

Public information about a corporation  ---  Alternate route: `/v3/corporations/{corporation_id}/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\CorporationApi();
$corporationId = 56; // int | An Eve corporation ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCorporationsCorporationId($corporationId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CorporationApi->getCorporationsCorporationId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **corporationId** | **int**| An Eve corporation ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCorporationsCorporationIdOk**](../Model/GetCorporationsCorporationIdOk.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCorporationsCorporationIdAlliancehistory**
> \ESI\Model\GetCorporationsCorporationIdAlliancehistory200Ok[] getCorporationsCorporationIdAlliancehistory($corporationId, $datasource)

Get alliance history

Get a list of all the alliances a corporation has been a member of  ---  Alternate route: `/v1/corporations/{corporation_id}/alliancehistory/`  Alternate route: `/legacy/corporations/{corporation_id}/alliancehistory/`  Alternate route: `/latest/corporations/{corporation_id}/alliancehistory/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\CorporationApi();
$corporationId = 56; // int | An EVE corporation ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCorporationsCorporationIdAlliancehistory($corporationId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CorporationApi->getCorporationsCorporationIdAlliancehistory: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **corporationId** | **int**| An EVE corporation ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCorporationsCorporationIdAlliancehistory200Ok[]**](../Model/GetCorporationsCorporationIdAlliancehistory200Ok.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCorporationsCorporationIdIcons**
> \ESI\Model\GetCorporationsCorporationIdIconsOk getCorporationsCorporationIdIcons($corporationId, $datasource)

Get corporation icon

Get the icon urls for a corporation  ---  Alternate route: `/v1/corporations/{corporation_id}/icons/`  Alternate route: `/legacy/corporations/{corporation_id}/icons/`  Alternate route: `/latest/corporations/{corporation_id}/icons/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\CorporationApi();
$corporationId = 56; // int | An EVE corporation ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCorporationsCorporationIdIcons($corporationId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CorporationApi->getCorporationsCorporationIdIcons: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **corporationId** | **int**| An EVE corporation ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCorporationsCorporationIdIconsOk**](../Model/GetCorporationsCorporationIdIconsOk.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCorporationsCorporationIdMembers**
> \ESI\Model\GetCorporationsCorporationIdMembers200Ok[] getCorporationsCorporationIdMembers($corporationId, $datasource)

Get corporation members

Read the current list of members if the calling character is a member.  ---  Alternate route: `/v2/corporations/{corporation_id}/members/`  Alternate route: `/legacy/corporations/{corporation_id}/members/`  Alternate route: `/latest/corporations/{corporation_id}/members/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\CorporationApi();
$corporationId = 56; // int | A corporation ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCorporationsCorporationIdMembers($corporationId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CorporationApi->getCorporationsCorporationIdMembers: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **corporationId** | **int**| A corporation ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCorporationsCorporationIdMembers200Ok[]**](../Model/GetCorporationsCorporationIdMembers200Ok.md)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCorporationsCorporationIdRoles**
> \ESI\Model\GetCorporationsCorporationIdRoles200Ok[] getCorporationsCorporationIdRoles($corporationId, $datasource)

Get corporation member roles

Return the roles of all members if the character has the personnel manager role or any grantable role.  ---  Alternate route: `/v1/corporations/{corporation_id}/roles/`  Alternate route: `/legacy/corporations/{corporation_id}/roles/`  Alternate route: `/latest/corporations/{corporation_id}/roles/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\CorporationApi();
$corporationId = 56; // int | A corporation ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCorporationsCorporationIdRoles($corporationId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CorporationApi->getCorporationsCorporationIdRoles: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **corporationId** | **int**| A corporation ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCorporationsCorporationIdRoles200Ok[]**](../Model/GetCorporationsCorporationIdRoles200Ok.md)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getCorporationsNames**
> \ESI\Model\GetCorporationsNames200Ok[] getCorporationsNames($corporationIds, $datasource)

Get corporation names

Resolve a set of corporation IDs to corporation names  ---  Alternate route: `/v1/corporations/names/`  Alternate route: `/legacy/corporations/names/`  Alternate route: `/latest/corporations/names/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\CorporationApi();
$corporationIds = array(56); // int[] | A comma separated list of corporation IDs
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getCorporationsNames($corporationIds, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CorporationApi->getCorporationsNames: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **corporationIds** | [**int[]**](../Model/int.md)| A comma separated list of corporation IDs |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetCorporationsNames200Ok[]**](../Model/GetCorporationsNames200Ok.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

