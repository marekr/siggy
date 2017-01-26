# GetSovereigntyCampaigns200Ok

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**attackersScore** | **float** | Score for all attacking parties, only present in Defense Events. | [optional] 
**campaignId** | **int** | Unique ID for this campaign. | 
**constellationId** | **int** | The constellation in which the campaign will take place. | 
**defenderId** | **int** | Defending alliance, only present in Defense Events | [optional] 
**defenderScore** | **float** | Score for the defending alliance, only present in Defense Events. | [optional] 
**eventType** | **string** | Type of event this campaign is for. tcu_defense, ihub_defense and station_defense are referred to as \&quot;Defense Events\&quot;, station_freeport as \&quot;Freeport Events\&quot;. | 
**participants** | [**\ESI\Model\SovereigntycampaignsParticipants[]**](SovereigntycampaignsParticipants.md) | Alliance participating and their respective scores, only present in Freeport Events. | [optional] 
**solarSystemId** | **int** | The solar system the structure is located in. | 
**startTime** | [**\DateTime**](\DateTime.md) | Time the event is scheduled to start. | 
**structureId** | **int** | The structure item ID that is related to this campaign. | 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


