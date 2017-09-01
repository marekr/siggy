@if( App::environment('local') )
<script src="http://dev.siggy.borkedlabs.com:8083/vendor.js" type="text/javascript" ></script>
<script src="http://dev.siggy.borkedlabs.com:8083/siggy.js" type="text/javascript" ></script>
@else
@siggy_asset_js('thirdparty.js', SIGGY_VERSION)
@siggy_asset_js('siggy.js', SIGGY_VERSION)
<script src="https://cdn.ravenjs.com/3.14.0/raven.min.js"></script>
@endif