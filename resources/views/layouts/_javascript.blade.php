@if( App::environment('local') )
@siggy_asset_js('thirdparty.js', time(), true)
@siggy_asset_js('siggy.js', time(), true)
@else
@siggy_asset_js('thirdparty.js', SIGGY_VERSION)
@siggy_asset_js('siggy.js', SIGGY_VERSION)
<script src="https://cdn.ravenjs.com/3.14.0/raven.min.js"></script>
@endif