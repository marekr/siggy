
@extends('layouts.public',['layoutMode' => 'blank', 'title' => 'siggy: forgot password', 'selectedTab'=>'login'])

@section('content')
<div class="container">
    <div class="row">
		<h2>Password Reset</h2>
		<p>An email has been dispatched to <?php echo $email; ?> if the email address you provided is correct and exists in our database. <br /><br />
		Due to security concerns, we will not confirm the existence of an account by email address through this form. You may request additional password resets if nessecary.
		</p>
    </div>
</div>
@endsection