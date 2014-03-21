@extends('layouts.master')

@section('css')
	{{ HTML::style('css/datepicker/datepicker3.css') }}
@stop

@section('content')

@if(isset($message))

<h3>{{{ $message }}}</h3>

@else

<h1>Edit {{ $user->username }}</h1>
<small class="text-muted">last updated: {{ \Carbon\Carbon::createFromTimestamp($user->updated_at) }}</small>
<hr>
{{ Form::open(array('url' => route('api.v1.users.update', $user->id), 'method' => 'PUT', 'role'=>'form', 'id'=>'frm')) }}
	<div class="error"></div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('first_name', 'First name') }}
				{{ Form::text('first_name', $user->first_name, array('class'=>'form-control', 'placeholder'=>'First name', 'required')) }}
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('last_name', 'Last name') }}
				{{ Form::text('last_name', $user->last_name, array('class'=>'form-control', 'placeholder'=>'Last name')) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('email', 'Email') }}
				{{ Form::email('email', $user->email, array('class'=>'form-control', 'placeholder'=>'Email')) }}
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('gender', 'Gender') }}
				{{ Form::text('gender', $user->gender, array('class'=>'form-control', 'placeholder'=>'Gender')) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('birthday', 'Birthday') }}
				{{ Form::text('birthday', Carbon\Carbon::createFromTimestamp($user->birthday)->format('d/m/Y'), array('class'=>'form-control datepicker', 'placeholder'=>'dd/mm/yyyy', 'data-timestamp'=>$user->birthday)) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('parent', 'Parent') }}
				<select name="parent_id" class="form-control">
					<option value="">Please select parent</option>
					<option value="" {{ is_null($user->parent_id) ? 'selected' : '' }}>no parent</option>
				@foreach($parents as $parent)
					<option value="{{ $parent->id }}" {{ $user->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->username }}</option>
				@endforeach
				</select>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('role', 'Role') }}
				<select name="role_id" class="form-control">
					<option value="">Please select role</option>
				@foreach($roles as $role)
					<option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
				@endforeach
				</select>
			</div>
		</div>
	</div>
	{{ Form::button('Save', array('class'=>'btn btn-success', 'type'=>'submit')) }}
{{ Form::close() }}

@endif

@stop

@section('js')

{{ HTML::script('js/moment/moment.min.js') }}
{{ HTML::script('js/jquery/jquery.validate.js') }}
{{ HTML::script('js/datepicker/datepicker.js') }}

@stop

@section('javascript')

<script type="text/javascript">

	var date_format = 'dd/mm/yyyy';
	var $datepicker = $('.datepicker');
	$datepicker.datepicker({
		autoclose: true,
		todayHighlight: true,
	    format: date_format
	}).on('changeDate', function(e){
		$(this).data('timestamp', moment(e.date).unix());
	});

	$('#frm').on('submit', function()
	{
		return false;
	}).validate({
		submitHandler: function(form){
			$this = $(form);
			$datepicker.val($datepicker.data('timestamp'));
			
			$.ajax({
				url: $this.attr('action'),
				type: 'put',
				dataType: 'json',
				data: $this.serialize(),
				success: function(result)
				{
					console.log(result);
					
					$alert = $('<div/>').addClass('alert').fadeIn('fast').prependTo('form');
					switch(result.header.code)
					{
						case 204:
							$alert.addClass('alert-warning')
								.delay(3000).fadeOut('slow', function() {
									$(this).remove();
								});
						break;

						default:
							$alert.addClass('alert-success')
								.delay(3000).fadeOut('slow', function() {
									$(this).remove();
								});
					}

					
					$alert.text(result.header.message);
				},
				error: function(e)
				{
					console.log(e);
				}
			});
		},
		rules: {
		    first_name: {
		      required: true,
		      minlength: 3
		    },
		    last_name: {
		      required: true,
		      minlength: 3
		    },
		    email: {
		      required: true,
		      email: true
		    }
	 	}
	});
</script>

@stop