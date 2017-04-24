<div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
	{!! Form::label($key, $title, ['class' => 'control-label']) !!}
	
	<div class="controls">
		{!! Form::password($key, array_merge(['class' => 'form-control'], $attributes)) !!}
		<span class="help-block">{{$description}}</span>

		@if($errors->has($key))
		<span class="help-block with-errors">{{ $errors->first($key, ':message') }}</span>
		@endif
	</div>
</div>