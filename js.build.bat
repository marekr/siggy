"C:\Program Files (x86)\Java\jre1.8.0_45\bin\java.exe" -jar compiler.jar ^
--js=misc.js ^
--js=siggy.js ^
--js=siggy.helpers.js ^
--js=siggy.static.js ^
--js=siggy.intel.dscan.js ^
--js=siggy.intel.poses.js ^
--js=siggy.charactersettings.js ^
--js=siggy.notifications.js ^
--js=siggy.timer.js ^
--js=siggy.sigtable.js ^
--js=siggy.globalnotes.js ^
--js=siggy.map.connection.js ^
--js=siggy.hotkeyhelper.js ^
--js=siggy.map.js ^
--js=siggy.activity.siggy.js ^
--js=siggy.activity.scannedsystems.js ^
--js=siggy.activity.search.js ^
--js=siggy.activity.thera.js ^
--js=siggy.activity.notifications.js ^
--source_map_format=V3 ^
--create_source_map siggy.compiled.js.map ^
--js_output_file=siggy.compiled.js


echo //# sourceMappingURL=siggy.compiled.js.map >> siggy.compiled.js

@pause
