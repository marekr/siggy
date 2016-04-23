java -jar compiler.jar ^
--js=translate.js ^
--js=jquery/jquery-1.11.2.js ^
--js=jquery/jquery-ui.1.11.4.min.js ^
--js=jquery/jquery.qtip.js ^
--js=jquery/jquery.blockUI.js ^
--js=jquery/jquery.color.js ^
--js=jquery/jquery.tablesorter.js ^
--js=jquery/jquery.flot.js ^
--js=jquery/jquery.ui.position.js ^
--js=jquery/jquery.contextMenu.js ^
--js=jquery/jquery.hotkeys.js ^
--js=jquery/jquery.jsPlumb-1.6.4.js ^
--js=jquery/jquery.placeholder.js ^
--js=jquery/jquery.simplePagination.js ^
--js=handlebars-v2.0.0.js ^
--js=handlebars.form-helpers.js ^
--js=handlebars.helpers.js ^
--js=dropdown.js ^
--js=tab.js ^
--js=typeahead.bundle.js ^
--js=vendor/moment.js ^
--source_map_format=V3 ^
--create_source_map thirdparty.compiled.js.map ^
--js_output_file=thirdparty.compiled.js

echo //# sourceMappingURL=thirdparty.compiled.js.map >> thirdparty.compiled.js