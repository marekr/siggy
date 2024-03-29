# siggy Changelog

## 5.7.0 - 2021-11-09
## Changes
- Update to CCP's deck chair rearrangement for oauth

## 5.6.0 - 2021-09-09
## Fixes 
- Add recaptcha to register form
- Update dependencies

## 5.5.3 - 2021-09-07
## Fixes 
- Update to latest SDE
- Fix ESI route for corporation which finally changed to v5

## 5.5.2 - 2019-03-09
## Fixes
- Update copy of evedb ships and systems
- Update dependencies

## 5.5.1 - 2018-08-11
## Fixes
- Fix asset compilation that broke with the backend change. How I wish I was using docker sometimes.

## 5.5.0 - 2018-08-11
## Changes
- Passwords are now hashed in a more modern way, with all old ones wrapped into the new hash
- Update backend framework

## 5.4.3 - 2018-08-06
## Fixes
- More aggressive HTTP headers to prevent page caching
- Surpressed a JS error when map was updated at the same time a map state refresh was triggered

## 5.4.2 - 2018-08-05
## Fixes
- Made change to improve initial ui loading that has become wonkier in newer browsers due to more async behavior on their part

## 5.4.1 - 2018-06-02
- Stop description box for sigs from moving when a type is selected
- Add "Delete nonexistent" sigs checkbox next to the simple sig paste reader input
- Move the "Mass Sig Reader" to a pop-out style button next to the signature adder text

## 5.4.0 - 2018-06-02
## Changes
- Add fancy display of triglavian systems based on actual algorithim (dont ban me ccp plz)
## Fixes
- Stop auto mapping triglavian systems

## 5.3.1 - 2018-05-31
## Changes
- Add Triglavian systems

## 5.3.0 - 2018-03-04
## Changes
- Updated some esi routes used to latest
- Removed last vestiges of CREST and XML api usage in anticipation of their removal by CCP
- Billing payments and charges changed in the backend a bit

## Fixes
- Fixed effects for systems not showing

## 5.2.2 - 2018-02-03
## Fixes
- Fixed some backend errors dealing with chainmaps
- Fixed autocomplete of systems not working

## 5.2.1 - 2018-01-30
## Fixes
- Fixed various issues with siggy being able to fetch corporations from ESI. Turns out CCP doesn't do any pretty written deprecation notices and relies on noticing headers.......not even a HTTP 410 after the fact to more easily exception and alert
- Backend cleanup of a table's usage

## 5.2.0 - 2018-01-28
## Additions
- Added swagger api definition to http://siggy.borkedlabs.com/api, nothing concrete or guranteed yet

## Fixes
- Fixed form validation on register page or else it would exception due to compatibility with backend framework
- Fixed saving system names, broken due to previous change affecting solar system handling

## 5.1.1 - 2018-01-26
## Changes
- Handling of system data in the frontend client streamlined and deduplicated
- Updated backend framework

## 5.1.0 - 2018-01-23
## Changes
- Current location is now in the navigation bar, effect is also shown if available (and hoverable!)

## Fixes
- Effect tooltip now displays again when mouse hovers over the name

## 5.0.3 - 2018-01-23
## Fixes
- Wormhole drag selection on map is no longer broken


## 5.0.2 - 2018-01-23
## Fixes
- DScan parsing was broken...if anything was off-grid
- Wormhole deletion now works again
- Fixed a duplicated XHR call

## 5.0.1 - 2018-01-22
## Fixes
- POSes were breaking systems from loading

## 5.0.0 - 2018-01-22
## Major Changes
- Now 100% more leftpad dependency. Ported over frontend javascript to fancy pancy Typescript + webpack system from legacy old school javascript world
- New router handling changes between activities (i.e. Scan vs. Thera page) which adds the pages to the browser history and provides links to the pages
- DScan reimplemented and part of the main siggy application rather than a previous side page

## Changes
- Dscan intel section moved to fancy new page

## Fixes
- Fix double data fetch on load of siggy page. Wooo yay Typescript
- Fix welcome page not working

## 4.1.4 - 2017-07-23
## Fixes
- Catch case where session data for eve sso was being stored in the wrong place causing errors when authenticating eve sso responses half the time

## 4.1.3 - 2017-07-22
## Changes
- Add login attempt throttling

## Fixes
- Try and fix the remember function
- Fix case where CSRF tokens will expire awkwardly while using siggy

## 4.1.2 - 2017-07-22
## Changes
- Add user statistics to prometheus metrics
- Fix up some middleware mapping to routes
- Try and catch an oauth exception better

## 4.1.1 - 2017-07-17
## Fixes
- Unescape html entities where possible for editing
- Prometheus data export route was accidentally put behind auth middleware

## 4.1.0 - 2017-07-17
## Changes
- Priortize ESI over CREST for waypoint/destination sets

## Fixes
- Add back the focusing of the input area on the mass sig adder
- Add padding to compensate for the focus outline on input boxes. This makes the cursor visible now when its at the beginning of a line

## 4.0.0 - 2017-07-16
## Changes
- Full restructure of auth system, separate of auth from session, this removes one of the remaining relics of siggy's past where it was an intertwined mess of hacks made to support IGB in weird ways over its evolution
- Shift all form tags fully over to templating to avoid forgetting the csrf field....
- Adjust group password box to look a tiny bit better

## 3.4.1 - 2017-07-07
## Fixes
- Added missing csrf tokens to remove chainmap and member pages, the pages should work now

## 3.4.0 - 2017-06-20
## Changes
- Characters on maps now tracked via Redis

## Fixes
- Fixed invalid password login not giving some kind of visible error message

## 3.3.0 - 2017-06-20
## Changes
- Sessions centralized in one system rather than two
- Sessions now stored in redis
- User logins reworked to use a new cookie scheme
- Manage page that used to show active sessions replaced with page that shows characters that were active in the last 24 hours, due to new session storage scheme

## 3.2.0 - 2017-06-14
## Changes
- Character locations now tracked in redis

## 3.1.0 - 2017-06-14
## Changes
- Reimplemented system statistics

## 3.0.4 - 2017-06-11
## Changes
- Add request for ESI "online" status scope
- Add display of ship types from ESI

## 3.0.3 - 2017-06-10
## Fixes
- Added error handling on setting EVE client waypoints
- Fixed switching groups from the dropdown

## 3.0.2 - 2017-06-09
## Fixes
- Fixed wormholes not mapping

## 3.0.1 - 2017-06-09
## Fixes
- Fixed dscan recorder not working
- Fixed systems not mapping when the chainmap got big

## 3.0.0 - 2017-06-08
## Changes
- Migrated to new framework
	- RIP Kohana 2009 - 2017
	- 90% of code is migrated, other 10% is in a transitional state
	- Database now maintained via migrations, they weren't a common framework thing when siggy started...death to sql files!
- Full CSRF token deployment
- Several URLs were rewritten in structure
- Forgot password now asks you to enter a new password at the end instead of emailing you a random password
- Ability to disable wormhole to signature link has been added by request
- New API system allowing export of siggy data, work in progress however and will be fleshed out

## Fixes
- Notifier form for system and resident found types no longer shows a default "undefined" text

## 2.45.1 - 2017-04-29
## Fixes
- Group creation broke due to previous changes, fixed now
- Password reset completion broke

## 2.45.0 - 2017-04-23
## Changes
- Reworked all forms
	- New templating system
	- New yes/no checkboxes
	- Form validation added to multiple places it was previously lacking

## 2.44.2 - 2017-04-20
### Fixes
- Handle NS_ERROR_FILE_CORRUPTED due to corrupted local storage in browsers instead of throwing the exception
- Fixed situation where group payment code wasn't generated

## Changes
- ESI success/failure statistic tracking added

## 2.44.1 - 2017-04-10
### Changes
- Add structure summary to system table ala POS summary

## 2.44.0 - 2017-03-12
### Changes
- Added asset management system to deal with js bundling and loading. This will resolve browser cache issues that happen during upgrades.
- Added maintenance pages triggered during upgrade

### Fixes
- Fixed legacy IGB show info calls for system info and character info in certain areas

## 2.43.2 - 2017-03-02
### Fixes
- Fixed system positions getting reset

## 2.43.1 - 2017-03-01
### Fixes
- Revert library upgrade to fix map jumping system issues

## 2.43.0 - 2017-03-01
### Changes
- Add loading screen to rectify race conditions with static data loading

## 2.42.0 - 2017-02-28
### Changes
- Updated several JS libraries to newer versions

### Fixes
- Reconnected characters should get their location polled properly again
- Changed supporting map HTML, it should scroll more properly now along with the deletion box working properly
- Delete selection drags should release now if the mouse leaves the chain map area

## 2.41.3 - 2017-02-26
### Fixes
- Fixed mass signature adder dialog not closing
- Fixed the quick mass signature box not showing its placeholder help text when used once

## 2.41.2 - 2017-02-26
### Fixes
- Fixed POS deletion

## 2.41.1 - 2017-02-26
### Fixes
- Fixed POS and structure edit boxes closing early on invalid entries

## 2.41.0 - 2017-02-26
### New
- Added citadel/structure tracking to systems, complete with vulnerability editor

### Changes
- Moved POS add/edit/remove dialogs to the new dialog system, now with form validation and feedback
- Moved POS table to templated system
- Removed placeholder.js compatibility wrapper as IGB is no longer supported and its not needed with other browsers

### Fixes
- Fixed some users being recorded with the wrong IP in the sessions viewer, one of siggy's proxies wasn't whitelisted by the application
- Fixed some character/corporation queries taking long amounts of time becasue the strict parameter to ESI wasn't being passed

## 2.40.0 - 2017-02-07
### Changes
- Moved a large and legacy cookie to localStorage as the server doesn't need to know UI display states
- Various url tweaks to match the new stricter Content-Security-Policy implemented

## 2.39.0 - 2017-02-07
### Changes
- Negative balances will get you harassed with a recurring popup

### Fixes
- Fixed stats page showing the wrong titles on the overview page


## 2.38.0 - 2017-02-07
### Changes
- All browser blocking alert messages have been moved to page html based alert messages
- Mass sig reader has been moved to the new dialog class
- Adjustments made to input and output of mass sig reader and will now report server errors

### Fixes
- Fixed initial button focus on confirm dialogs

## 2.37.0 - 2017-02-06
### Changes
- Reimplemented the following dialogs: Fatal Error, Session Timeout, POS Delete, and Dscan Delete in a new central class
- Included a new style for updated dialogs

## 2.36.0 - 2017-02-05
### Changes
- Migrated management pages to the new templating system
- Replaced swagger generated library for ESI with a simpler hand written implementation

### Fixes
- Fixed slight issue with billing not applying properly to some groups

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