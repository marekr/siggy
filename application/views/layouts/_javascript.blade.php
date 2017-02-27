@if( Kohana::$environment == Kohana::DEVELOPMENT )
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/vendor/handlebars-v4.0.5.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/vendor/handlebars.form-helpers.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/vendor/handlebars.helpers.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/translate.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery-1.11.2.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery-ui.1.11.4.min.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.tablesorter.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.blockUI.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.color.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.flot.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.ui.position.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.contextMenu.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.qtip.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.jsPlumb-1.6.4.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.hotkeys.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.simplePagination.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/jquery/jquery.idle.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/dropdown.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/tab.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/misc.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/vendor/validate.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/typeahead.bundle.js'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.helpers.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.static.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.timer.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.eve.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.socket.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.sigtable.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.intel.poses.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.intel.dscan.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.intel.structures.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.globalnotes.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.charactersettings.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.hotkeyhelper.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.dialog.sigcreatewormhole.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.map.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.map.connection.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.notifications.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.thera.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.search.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.homestead.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.siggy.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.scannedsystems.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.notifications.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.astrolabe.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.activity.chainmap.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.vulnerabilitytable.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.dialogs.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.dialog.structurevulnerability.js?{{ time() }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/vendor/moment.js?{{ time() }}'></script>
@else
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/thirdparty.compiled.js?{{ SIGGY_VERSION }}'></script>
<script type='text/javascript' src='{{ URL::base(TRUE, TRUE) }}js/siggy.compiled.js?{{ SIGGY_VERSION }}'></script>
<script src="https://cdn.ravenjs.com/3.10.0/raven.min.js"></script>
@endif