@extends('layouts.manage',[
							'title' => 'siggy.manage: esi backend'
						])

@section('content')

<h3>Backend ESI</h3>
<div class="row">
	<div class="text-center">
		<a href="{{url('backend/esi')}}"><img src='{{asset('images/eve/EVE_SSO_Login_Buttons_Large_White.png')}}' /></a>
	</div>
</div>

@endsection