
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'siggy: password reset complete', 'selectedTab'=>'login'])

@section('content')
<div class="container">
    <div class="row">
		<h2>Password Reset Complete</h2>
		<p>Your account's password has been reset. An email has been dispatched with a temporary password you may use to login and then change it to anything you wish.</p>
    </div>
</div>
@endsection