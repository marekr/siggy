
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'siggy: password reset complete', 'selectedTab'=>'login'])

@section('content')
<div class="container">
    <div class="row">
		<h2>Password Reset Complete</h2>
		<p>Your account's password has been reset. You may now go and <a href="{{url('account/login')}}">login.</a></p>
    </div>
</div>
@endsection