<div class="form-group">
	{!! Form::label($key, $title, ['class' => 'control-label']) !!}
	<div class="controls">
		{!! Form::checkbox($key, 1, $value, ['class' => 'form-control yesno']) !!}
		<span class="help-block">{{$description}}</span>
	</div>
</div>