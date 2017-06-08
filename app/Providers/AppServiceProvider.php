<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		\Siggy\BladeHelpers::register();

		
		\Form::component('bsSelect', 'components.form.select', ['key', 'title', 'options' => [], 'description' => '', 'value' => null, 'attributes' => []]);
		\Form::component('bsText', 'components.form.text', ['key', 'title', 'description' => '', 'value' => null, 'attributes' => []]);
		\Form::component('bsPassword', 'components.form.password', ['key', 'title', 'description' => '', 'attributes' => []]);
		\Form::component('bsTextarea', 'components.form.textarea', ['key', 'title', 'description' => '', 'value' => null, 'attributes' => []]);
		\Form::component('yesNo', 'components.form.yesno', ['key', 'title', 'description' => '', 'value' => null, 'attributes' => []]);
		\Form::component('actionButtons', 'components.form.actionButtons', []);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
