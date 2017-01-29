# siggy Changelog

## 2.31.0 - 2017-01-28
### Changes
- Four new scopes for ESI are requested. siggy now tracks what scopes were granted
- Reimplement the jump log partially in preparation of restoring ship type
- Add new changelog

## 2.30.0 - 2017-01-25
### Changes
- Switched to ESI for corporation and character data fetching

### Fixes
- Fixed homesystems for chainmaps
- Fixed wormhole signature deletio

## 2.29.3 - 2017-01-14
### Changes
- Idle timeout added. After 1 hour, open siggy clients will stop querying the server. 
This is to prevent users from having tens of tabs open to siggy upon hundreds of other tabs...
- Backend data Changes

## 2.29.2 - 2017-01-13
### Changes
- Added a notification system that allows the siggy web application to notify the siggy location worker process to faster
start retrival of character location on open of siggy

### Fixes
- Fixed exit finder

## 2.29.1 - 2017-01-12
### Fixes
- Fixed mass sig adder
- Fixed pathfinder
- Fixed pos deletion
- Fixed notifications
- Fixed mapping systems
- Fixed broken automatic logins
- Fixed chainmap creation

## 2.29.0 - 2017-01-10
### Changes
- More backend data model changes

## 2.28.0 - 2017-01-08
### Changes
- More backend data model changes

## 2.27.0 - 2017-01-04
### Changes
- Begin of major data handling changes that will undoubtly cause a mess temporarily

## 2.26.4 - 2016-10-22
- Fix html entities not being evaluated in custom system names

## 2.26.3 - 2016-10-20
### Fixes
- Location reported back to siggy browser clients

## 2.26.2 - 2016-10-13
### Changes
- Change how location is tracked by external crest query worker to fix siggy mapping issues
- Characters on the same siggy account but not currently selected are tracked as if they were selected. They will map wormholes and the like normally.

## 2.26.1b - 2016-10-13

### Changes
- Increased polling rate to 5 seconds

### Fixes
- Fixed case where characters were selected that shouldn't be selected resulting in errors
- Ensure location fetched for map usage is "fresh" within a 20 second cutoff
- Fixed cases where adding a group member would result in error

## 2.26.1a - 2016-10-10

### Fixes
- Fixed context menus for show info, set destination and set waypoint not always doing the right thing

## 2.26.1 - 2016-10-10

### Fixes
- Fixed session log

## 2.26 - 2016-10-09

### Changes
- IGB and API key support has been dropped for CREST only auth. SSO is now required for all logins with a siggy account.
- Several legacy pieces of code are now centralized

## 2.25.6 - 2016-05-09

### Changes
- Add system hazard setting because drifters