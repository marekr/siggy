@extends('layouts.public',['layoutMode' => 'leftMenu', 'title' => 'siggy: overview', 'selectedTab'=>'account'])

@section('content')
<h3>Overview</h3>

<dl class="dl-horizontal">
  <dt>Email Address</dt>
  <dd><?php echo $user->email; ?></dd>
</dl>
@endsection

@section('left_menu')
@include('account.menu')
@endsection