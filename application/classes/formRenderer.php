<?php


class formRenderer
{

	/**
	 * Creates a form input. If no type is specified, a "text" type input will
	 * be returned.
	 *
	 *     echo Bootstrap::input('username', $username);
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   errors
	 * @param   array   html attributes
	 * @return  string
	 */
	public static function input($name, $key, $value, $desc = '', $errors = NULL, $useRequestValue = TRUE, array $attributes = NULL)
	{
		if( $useRequestValue )
		{
			$value = Arr::get($_REQUEST, $key, $value);
		}
	
		return self::wrap($name, $key, Form::input($key, $value, $attributes), $desc, $errors);
	}
	
	
	
	public static function yesNo($name, $key, $value,  $desc = '', $errors = NULL, $useRequestValue = TRUE, $attributes = NULL) {
		if( $useRequestValue )
		{
			$value = Arr::get($_REQUEST, $key, $value);
		}
		
		$attributes['class'] = 'radio_buttons';
		
      $result = "<label class='yes radio inline'>";
			$result .= Kohana_Form::radio($key, 1, ($value == 1 ? TRUE : FALSE), $attributes);	
      $result .= "Yes </label><label class='no radio inline'>";
			$result .= Kohana_Form::radio($key, 0, ($value == 0 ? TRUE : FALSE), $attributes);	
      $result .= "No </label>";
      
	  return self::wrap($name, $key, $result, $desc, $errors);
	}

	/**
	 * Creates a password form input.
	 *
	 *     echo Bootstrap::password('password');
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   errors
	 * @param   array   html attributes
	 * @return  string
	 */
	public static function password($name, $key, $value, $desc = '', $errors = NULL, array $attributes = NULL)
	{
		return self::wrap($name, $key, Form::password($key, $value, $attributes), $desc, $errors);
	}

	/**
	 * Creates a textarea form input.
	 *
	 *     echo Bootstrap::textarea('about', $about);
	 *
	 * @param   string   textarea name
	 * @param   string   textarea body
	 * @param   array    errors
	 * @param   array    html attributes
	 * @param   boolean  encode existing HTML characters
	 * @return  string
	 */
	public static function textarea($name, $key, $value, $desc = '', $errors = NULL, array $attributes = NULL, $useRequestValue = TRUE,  $double_encode = TRUE)
	{
		if( $useRequestValue )
		{
			$value = Arr::get($_REQUEST, $key, $value);
		}	
		
		return self::wrap($name, $key, Form::textarea($key, $value, $attributes, $double_encode), $desc, $errors);
	}

	/**
	 * Creates a select form input.
	 *
	 *     echo Bootstrap::select('country', $countries, $country);
	 *
	 * @param   string   input name
	 * @param   array    available options
	 * @param   mixed    selected option string, or an array of selected options
	 * @param   array    errors
	 * @param   array    html attributes
	 * @return  string
	 */
	public static function select($name, $key, array $options, $selected, $desc = '', $errors = NULL, $useRequestValue = TRUE,  array $attributes = NULL)
	{
		if( $useRequestValue )
		{
			$selected = Arr::get($_REQUEST, $key, $selected);
		}	
	
		return self::wrap($name, $key, Form::select($key, $options, $selected, $attributes), $desc, $errors);
	}

	/**
	 * Wraps a form element with Boostrap specific HTML.
	 *
	 *     echo Bootstrap::select('country', $countries, $country);
	 *
	 * @param   string   form item name
	 * @param   string   html form element
	 * @param   array    errors
	 * @return  string
	 */
	 
	public static function wrap($name, $key, $form_element, $desc = "", $errors = NULL)
	{
		$is_error = ($errors != NULL) && (Arr::get($errors, $key) != NULL);
		$error_class = $is_error ? ' error' : '';
		$error_html = $is_error ? '<span class="help-inline">'.Arr::get($errors, $key).'</span>' : '';

		$desc_html = !empty($desc) ? '<p class="help-block">'.$desc.'</p>' : '';

		$i18n_name = __($name);

		$out = <<<OUT
<div class="control-group{$error_class}">
	<label for="{$key}" class="control-label">{$i18n_name}</label>
	<div class="controls">
		{$form_element}{$error_html}
		{$desc_html}
	</div>
</div>
OUT;

		return $out;
	}
}