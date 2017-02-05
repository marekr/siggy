# siggy Changelog

## 2.35.0 - 2017-02-04
### Changes
The methodology behind billing has changed. Charges are now based on actual usage of siggy on mostly a character basis per day. This is
a replacement for the original system that simply summed up the total number of characters in corps and standalone that were added to siggy.
i.e. If you access a group with a character selected, it counts agaisnt the total for the day.
Alts that may be tracked and shown on the map but not selected in the siggy interface will not count towards the total.

This also means for those that may barely use siggy at all, you will not be charged for days with zero usage.
The charge per character has been adjusted to compensate and may yet still change but overall it won't cost more than before.

For a fresh start, all balances under 0 have been reset to 1,000,000 ISK.

## 2.34.0 - 2017-02-03
### Changes
- Reimplemented cron jobs in a more stable/sane structure

## 2.33.0 - 2017-01-30
### Changes
- Backend change to how pages and responses are generated, some cpu load reduction on long term sessions as a result
- Added some padding to context menus to make them a little less cramped

### Fixes
- Fixed case where blacklisting a character may not have taken effect

## 2.32.5 - 2017-01-30
### Fixes
- Handle ESI/Crest being slow better. Stop being overaggressive at syncing with them for character and corporation info
- Make /account redirect to /account/overview
- Handle oddities where characters aren't returned for the /account/connected page

## 2.32.4 - 2017-01-29
### Changes
- Make the map resize handle use a resize cursor rather than a pointer (old IGB kludge)

### Fixes
- Fix situation where clicking on map systems would eventually cause multiple refresh events to be queued and keep being queued rather than just one

## 2.32.3 - 2017-01-29
### Fixes
- Fixed situation where multiple ajax calls can end up stacked up because of a timeout on one
- Changed idle timeout to 1.5 hours to ease some pressure on the server

## 2.32.2 - 2017-01-29
### Fixes
- Allow SSO refresh tokens to be null. SSO is mysteriously returning null refresh tokens for random users

## 2.32.1 - 2017-01-29
### Fixes
- Add some apple touch icons to suppress 404 exceptions in siggy's log file <.<
- Fixed error on Connected Characters page when CCP/ESI would not return proper character information at the time of page load

## 2.32.0 - 2017-01-29
### Fixes
- Settings which were previously character level (legacy IGB reasons) are now user account level

## 2.31.2 - 2017-01-28
### Fixes
- Fixed signature descriptions not displaying html entities unencoded

## 2.31.1 - 2017-01-28
### Fixes
- Fixed character location tracking hammering database with severely more updates than required
- Removed legacy IGB header handling code
- Fixed case where wormhole jumps may be suddenly mapped out of nowhere, this was due to characters playing the game without siggy 
and then opening siggy at just the right time

## 2.31.0 - 2017-01-28
### Changes
- Four new scopes for ESI are requested. siggy now tracks what scopes were granted
- Reimplement the jump log partially in preparation of restoring ship type
- Add new changelog
- Remove mention of IGB from home page

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