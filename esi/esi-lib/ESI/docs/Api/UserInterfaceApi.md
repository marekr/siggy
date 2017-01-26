# ESI\UserInterfaceApi

All URIs are relative to *https://esi.tech.ccp.is/dev*

Method | HTTP request | Description
------------- | ------------- | -------------
[**postUiAutopilotWaypoint**](UserInterfaceApi.md#postUiAutopilotWaypoint) | **POST** /ui/autopilot/waypoint/ | Set Autopilot Waypoint
[**postUiOpenwindowContract**](UserInterfaceApi.md#postUiOpenwindowContract) | **POST** /ui/openwindow/contract/ | Open Contract Window
[**postUiOpenwindowInformation**](UserInterfaceApi.md#postUiOpenwindowInformation) | **POST** /ui/openwindow/information/ | Open Information Window
[**postUiOpenwindowMarketdetails**](UserInterfaceApi.md#postUiOpenwindowMarketdetails) | **POST** /ui/openwindow/marketdetails/ | Open Market Details
[**postUiOpenwindowNewmail**](UserInterfaceApi.md#postUiOpenwindowNewmail) | **POST** /ui/openwindow/newmail/ | Open New Mail Window


# **postUiAutopilotWaypoint**
> postUiAutopilotWaypoint($destinationId, $clearOtherWaypoints, $addToBeginning, $datasource)

Set Autopilot Waypoint

Set a solar system as autopilot waypoint  ---  Alternate route: `/v2/ui/autopilot/waypoint/`  Alternate route: `/latest/ui/autopilot/waypoint/`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\UserInterfaceApi();
$destinationId = 789; // int | The destination to travel to, can be solar system, station or structure's id
$clearOtherWaypoints = false; // bool | Whether clean other waypoints beforing adding this one
$addToBeginning = false; // bool | Whether this solar system should be added to the beginning of all waypoints
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $api_instance->postUiAutopilotWaypoint($destinationId, $clearOtherWaypoints, $addToBeginning, $datasource);
} catch (Exception $e) {
    echo 'Exception when calling UserInterfaceApi->postUiAutopilotWaypoint: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **destinationId** | **int**| The destination to travel to, can be solar system, station or structure&#39;s id |
 **clearOtherWaypoints** | **bool**| Whether clean other waypoints beforing adding this one | [default to false]
 **addToBeginning** | **bool**| Whether this solar system should be added to the beginning of all waypoints | [default to false]
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

void (empty response body)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **postUiOpenwindowContract**
> postUiOpenwindowContract($contractId, $datasource)

Open Contract Window

Open the contract window inside the client  ---  Alternate route: `/v1/ui/openwindow/contract/`  Alternate route: `/legacy/ui/openwindow/contract/`  Alternate route: `/latest/ui/openwindow/contract/`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\UserInterfaceApi();
$contractId = 56; // int | The contract to open
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $api_instance->postUiOpenwindowContract($contractId, $datasource);
} catch (Exception $e) {
    echo 'Exception when calling UserInterfaceApi->postUiOpenwindowContract: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **contractId** | **int**| The contract to open |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

void (empty response body)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **postUiOpenwindowInformation**
> postUiOpenwindowInformation($targetId, $datasource)

Open Information Window

Open the information window for a character, corporation or alliance inside the client  ---  Alternate route: `/v1/ui/openwindow/information/`  Alternate route: `/legacy/ui/openwindow/information/`  Alternate route: `/latest/ui/openwindow/information/`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\UserInterfaceApi();
$targetId = 56; // int | The target to open
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $api_instance->postUiOpenwindowInformation($targetId, $datasource);
} catch (Exception $e) {
    echo 'Exception when calling UserInterfaceApi->postUiOpenwindowInformation: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **targetId** | **int**| The target to open |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

void (empty response body)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **postUiOpenwindowMarketdetails**
> postUiOpenwindowMarketdetails($typeId, $datasource)

Open Market Details

Open the market details window for a specific typeID inside the client  ---  Alternate route: `/v1/ui/openwindow/marketdetails/`  Alternate route: `/legacy/ui/openwindow/marketdetails/`  Alternate route: `/latest/ui/openwindow/marketdetails/`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\UserInterfaceApi();
$typeId = 56; // int | The item type to open in market window
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $api_instance->postUiOpenwindowMarketdetails($typeId, $datasource);
} catch (Exception $e) {
    echo 'Exception when calling UserInterfaceApi->postUiOpenwindowMarketdetails: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **typeId** | **int**| The item type to open in market window |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

void (empty response body)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **postUiOpenwindowNewmail**
> postUiOpenwindowNewmail($newMail, $datasource)

Open New Mail Window

Open the New Mail window, according to settings from the request if applicable  ---  Alternate route: `/v1/ui/openwindow/newmail/`  Alternate route: `/legacy/ui/openwindow/newmail/`  Alternate route: `/latest/ui/openwindow/newmail/`

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: evesso
ESI\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$api_instance = new ESI\Api\UserInterfaceApi();
$newMail = new \ESI\Model\PostUiOpenwindowNewmailNewMail(); // \ESI\Model\PostUiOpenwindowNewmailNewMail | The details of mail to create
$datasource = "tranquility"; // string | The server name you would like data from

try {
    $api_instance->postUiOpenwindowNewmail($newMail, $datasource);
} catch (Exception $e) {
    echo 'Exception when calling UserInterfaceApi->postUiOpenwindowNewmail: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **newMail** | [**\ESI\Model\PostUiOpenwindowNewmailNewMail**](../Model/\ESI\Model\PostUiOpenwindowNewmailNewMail.md)| The details of mail to create |
 **datasource** | **string**| The server name you would like data from | [optional] [default to tranquility]

### Return type

void (empty response body)

### Authorization

[evesso](../../README.md#evesso)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

