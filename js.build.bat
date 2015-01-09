"C:\Program Files (x86)\Java\jre7\bin\java.exe" -jar compiler.jar ^
--js=misc.js ^
--js=siggy.js ^
--js=siggy.helpers.js ^
--js=siggy.static.js ^
--js=siggy.intel.dscan.js ^
--js=siggy.intel.poses.js ^
--js=siggy.charactersettings.js ^
--js=siggy.timer.js ^
--js=siggy.sigtable.js ^
--js=siggy.globalnotes.js ^
--js=siggy.map.connection.js ^
--js=siggy.hotkeyhelper.js ^
--js=siggy.map.js ^
--js=siggy.activity.thera.js ^
--source_map_format=V3 ^
--create_source_map siggy.compiled.js.map ^
--js_output_file=siggy.compiled.js


echo //# sourceMappingURL=siggy.compiled.js.map >> siggy.compiled.js

@pause
