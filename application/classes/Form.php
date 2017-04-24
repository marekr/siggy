<?php

class Form {
	
	public static $formBuilder = null;

	public static $htmlBuilder = null;

	public static function getInstance()
	{
		$app = ['url' => new \Siggy\LaravelCompat\UrlGenerator(),
				'view' => \Siggy\View::getViewFactory(),
				'token' => Auth::$session->csrf_token ];

		if(self::$htmlBuilder == null)
		{
			self::$htmlBuilder = new \Siggy\LaravelCompat\HtmlBuilder($app['url'], $app['view']);
		}

		if(self::$formBuilder == null)
		{
			self::$formBuilder = new \Siggy\LaravelCompat\FormBuilder(self::$htmlBuilder, $app['url'], $app['view'], $app['token']);

			Form::component('bsSelect', 'components.form.select', ['key', 'title', 'options' => [], 'description' => '', 'value' => null, 'attributes' => []]);
			Form::component('bsText', 'components.form.text', ['key', 'title', 'description' => '', 'value' => null, 'attributes' => []]);
			Form::component('bsPassword', 'components.form.password', ['key', 'title', 'description' => '', 'attributes' => []]);
			Form::component('bsTextarea', 'components.form.textarea', ['key', 'title', 'description' => '', 'value' => null, 'attributes' => []]);
			Form::component('yesNo', 'components.form.yesno', ['key', 'title', 'description' => '', 'value' => null, 'attributes' => []]);
			Form::component('actionButtons', 'components.form.actionButtons', []);
		}

		return self::$formBuilder;
	}

	public static function __callStatic($method, $params)
	{
		return forward_static_call_array([self::getInstance(), $method], $params);
	}
}