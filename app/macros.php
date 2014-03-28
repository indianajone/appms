<?php

$errors = Session::get('errors', new Illuminate\Support\MessageBag);

Form::macro('bootwrapped', function($name, $label, $callback) use ($errors)
{
  return sprintf(
    '<div class="form-group %s">
      <label class="control-label">%s</label>
      %s
      %s
    </div>', 
    $errors->has($name) ? 'has-error' : '', 
    $label, 
    $callback($name), 
    $errors->first($name, '<span class="help-block">:message</span>')
  );
});