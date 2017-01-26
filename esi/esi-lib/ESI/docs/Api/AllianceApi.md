# ESI\AllianceApi

All URIs are relative to *https://esi.tech.ccp.is/dev*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getAlliances**](AllianceApi.md#getAlliances) | **GET** /alliances/ | List all alliances
[**getAlliancesAllianceId**](AllianceApi.md#getAlliancesAllianceId) | **GET** /alliances/{alliance_id}/ | Get alliance information
[**getAlliancesAllianceIdCorporations**](AllianceApi.md#getAlliancesAllianceIdCorporations) | **GET** /alliances/{alliance_id}/corporations/ | List alliance&#39;s corporations
[**getAlliancesAllianceIdIcons**](AllianceApi.md#getAlliancesAllianceIdIcons) | **GET** /alliances/{alliance_id}/icons/ | Get alliance icon
[**getAlliancesNames**](AllianceApi.md#getAlliancesNames) | **GET** /alliances/names/ | Get alliance names


# **getAlliances**
> int[] getAlliances($datasource)

List all alliances

List all active player alliances  ---  Alternate route: `/v1/alliances/`  Alternate route: `/latest/alliances/`  Alternate route: `/legacy/alliances/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\AllianceApi();
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getAlliances($datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AllianceApi->getAlliances: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

**int[]**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAlliancesAllianceId**
> \ESI\Model\GetAlliancesAllianceIdOk getAlliancesAllianceId($allianceId, $datasource)

Get alliance information

Public information about an alliance  ---  Alternate route: `/v1/alliances/{alliance_id}/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\AllianceApi();
$allianceId = 56; // int | An Eve alliance ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getAlliancesAllianceId($allianceId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AllianceApi->getAlliancesAllianceId: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **allianceId** | **int**| An Eve alliance ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetAlliancesAllianceIdOk**](../Model/GetAlliancesAllianceIdOk.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAlliancesAllianceIdCorporations**
> int[] getAlliancesAllianceIdCorporations($allianceId, $datasource)

List alliance's corporations

List all current member corporations of an alliance  ---  Alternate route: `/v1/alliances/{alliance_id}/corporations/`  Alternate route: `/latest/alliances/{alliance_id}/corporations/`  Alternate route: `/legacy/alliances/{alliance_id}/corporations/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\AllianceApi();
$allianceId = 56; // int | An EVE alliance ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getAlliancesAllianceIdCorporations($allianceId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AllianceApi->getAlliancesAllianceIdCorporations: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **allianceId** | **int**| An EVE alliance ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

**int[]**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAlliancesAllianceIdIcons**
> \ESI\Model\GetAlliancesAllianceIdIconsOk getAlliancesAllianceIdIcons($allianceId, $datasource)

Get alliance icon

Get the icon urls for a alliance  ---  Alternate route: `/v1/alliances/{alliance_id}/icons/`  Alternate route: `/legacy/alliances/{alliance_id}/icons/`  Alternate route: `/latest/alliances/{alliance_id}/icons/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\AllianceApi();
$allianceId = 56; // int | An EVE alliance ID
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getAlliancesAllianceIdIcons($allianceId, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AllianceApi->getAlliancesAllianceIdIcons: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **allianceId** | **int**| An EVE alliance ID |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetAlliancesAllianceIdIconsOk**](../Model/GetAlliancesAllianceIdIconsOk.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **getAlliancesNames**
> \ESI\Model\GetAlliancesNames200Ok[] getAlliancesNames($allianceIds, $datasource)

Get alliance names

Resolve a set of alliance IDs to alliance names  ---  Alternate route: `/v1/alliances/names/`  Alternate route: `/legacy/alliances/names/`  Alternate route: `/latest/alliances/names/`   ---  This route is cached for up to 3600 seconds

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new ESI\Api\AllianceApi();
$allianceIds = array(56); // int[] | A comma separated list of alliance IDs
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $result = $api_instance->getAlliancesNames($allianceIds, $datasource);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AllianceApi->getAlliancesNames: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **allianceIds** | [**int[]**](../Model/int.md)| A comma separated list of alliance IDs |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

[**\ESI\Model\GetAlliancesNames200Ok[]**](../Model/GetAlliancesNames200Ok.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

