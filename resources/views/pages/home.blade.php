@extends('layouts.public',[
							'title' => 'home',
							'selectedTab' => 'home',
							'layoutMode' => 'blank'
						])

@section('content')

@if(env('SIGGY_EOL'))
<div class="alert alert-success" role="alert">
  <h4 class="alert-heading">siggy is closing up shop!</h4>
    <p>
        It's been 12 years of service and I am sad to announce siggy will be shutdown.
        Due to my lack of personal interest in EVE and having moved on over the years, I no longer have the time
        to spend on siggy.
    </p>
    <p>
        siggy has been open sourced at <a href="https://github.com/marekr/siggy">https://github.com/marekr/siggy</a>.
        However, it requires some setup, development knowledge and fiddling to get it to work and it's not for novices.
    </p>
    <p>
        As the developer, I thank all of the users that have used siggy and given me the isk.
    </p>
  </p>
</div>
@endif

<div class="jumbotron">
	<div class="container">
		<h1><img alt="Brand" width="150" src="{{asset('images/siggy-blue.png')}}">siggy</h1>
		<p>One of EVE's oldest WH scanning tool now <?php echo (date('Y')-2011);?> years old and counting! It has aided countless explorers of WH Space.</p>
	</div>
</div>
<div class="container marketing">

	<div class="row featurette">
		<div class="col-md-5">
			<img class="featurette-image img-responsive" width="500" src="{{asset('images/intro/intro-igboog.png')}}">
		</div>
		<div class="col-md-7">
			<h2 class="featurette-heading">OOG Support via CREST/ESI. <span class="text-muted">Works out of game!</span></h2>
			<p class="lead">siggy also works in all major modern browsers and is able to track your movement using CREST/ESI!</p>
		</div>
	</div>

	<hr class="featurette-divider">
	
	<div class="row featurette">
		<div class="col-md-7">
			<h2 class="featurette-heading">Chainmaps. <span class="text-muted">Navigate wormholes with ease!</span></h2>
			<p class="lead">Wormholes automatically get mapped as you jump systems with siggy open. The map allows editing of system and wormhole states and positions on the map</p>
		</div>
		<div class="col-md-5">
			<img class="featurette-image img-responsive" width="500" src="{{asset('images/intro/intro-chainmap.png')}}">
		</div>
	</div>

	<hr class="featurette-divider">

	<div class="row featurette">
		<div class="col-md-5">
			<img class="featurette-image img-responsive" width="500" src="{{asset('images/intro/intro-sigs.png')}}">
		</div>
		<div class="col-md-7">
			<h2 class="featurette-heading">Signature Log.<span class="text-muted">Track signatures in systems.</span></h2>
			<p class="lead">Record signatureas as you can. You are allowed to categorize signatures down to the site type and sort the table! Signatures are preserved for weeks allowing you to quickly skip old signatures in 	wormholes you have already visited.</p>
		</div>
	</div>

	<hr class="featurette-divider">

	<div class="row featurette">
		<div class="col-md-7">
			<h2 class="featurette-heading">Non-English EVE Client Support. <span class="text-muted">Play in your language!</span></h2>
			<p class="lead">Non-english text is supported by default in siggy. Additionally, Deutsch language users are able to paste the EVE client's signatures into siggy and the Deutsch names of sites are handled properly.</p>
		</div>
		<div class="col-md-5">
			<img class="featurette-image img-responsive" width="500" src="{{asset('images/intro/intro-language.png')}}">
		</div>
	</div>

	<hr class="featurette-divider">

	<div class="row featurette" style="text-align:center">
		<div class="col-md-12">
			<h2 class="featurette-heading">Getting Access</h2>
			<p class="lead">Looking to gain access to your corp or alliance siggy?&nbsp;<a href="{{secure_url('account/register')}}" class="btn btn-default btn-primary btn-sm" />Register here</a></p>
			<p class="lead">Looking to start using siggy for the first time?&nbsp;<a href="http://wiki.siggy.borkedlabs.com/getting-siggy/" class="btn btn-default btn-sm" />Click here for info</a></p>
		</div>
	</div>


</div>
@endsection