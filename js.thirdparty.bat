java -jar ./build/closure-compiler-v20170218.jar ^
--js=translate.js ^
--js=jquery/jquery-ui.1.11.4.min.js ^
--js=jquery/jquery.qtip.js ^
--js=jquery/jquery.blockUI.js ^
--js=jquery/jquery.color.js ^
--js=jquery/jquery.tablesorter.js ^
--js=jquery/jquery.flot.js ^
--js=jquery/jquery.ui.position.js ^
--js=jquery/jquery.contextMenu.js ^
--js=jquery/jquery.hotkeys.js ^
--js=jquery/jquery.simplePagination.js ^
--js=jquery/jquery.idle.js ^
--js=jquery/jquery-1.12.4.js ^
--js=vendor/jsPlumb-1.7.10.js ^
--js=vendor/handlebars-v4.0.5.js ^
--js=vendor/handlebars.form-helpers.js ^
--js=vendor/handlebars.helpers.js ^
--js=dropdown.js ^
--js=tab.js ^
--js=typeahead.bundle.js ^
--js=vendor/moment.js ^
--js=vendor/validate.js ^
--create_source_map thirdparty.compiled.js.map ^
--js_output_file=thirdparty.compiled.js

echo //# sourceMappingURL=thirdparty.compiled.js.map >> thirdparty.compiled.js