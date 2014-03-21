@extends('layouts.master')

@section('css')
	{{ HTML::style('css/datepicker/datepicker3.css') }}
@stop

@section('content')

@if(isset($message))

<h3>{{{ $message }}}</h3>

@else

<h1>New User</h1>
{{ Form::open(array('url' => route('api.v1.users.store'), 'method' => 'POST', 'role'=>'form')) }}
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('username', 'Username') }}
				{{ Form::text('username', null, array('class'=>'form-control', 'placeholder'=>'Username')) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('password', 'Password') }}
				{{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'Password')) }}
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('confirm_password', 'Confirm Password') }}
				{{ Form::password('confirm_password', array('class'=>'form-control', 'placeholder'=>'Confirm Password')) }}
			</div>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('first_name', 'First name') }}
				{{ Form::text('first_name', null, array('class'=>'form-control', 'placeholder'=>'First name')) }}
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('last_name', 'Last name') }}
				{{ Form::text('last_name', null, array('class'=>'form-control', 'placeholder'=>'Last name')) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('email', 'Email') }}
				{{ Form::email('email', null, array('class'=>'form-control', 'placeholder'=>'Email')) }}
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('gender', 'Gender') }}
				{{ Form::text('gender', null, array('class'=>'form-control', 'placeholder'=>'Gender')) }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('birthday', 'Birthday') }}
				{{ Form::text('birthday', null, array('class'=>'form-control datepicker', 'placeholder'=>'dd/mm/yyyy')) }}
			</div>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('parent', 'Parent') }}
				<select name="parent_id" class="form-control">
					<option value="">Please select parent</option>
				@if($user->hasRole('super_admin'))
					<option value="">no parent</option>
				@endif
				@foreach($parents as $parent)
					<option value="{{ $parent->id }}">{{ $parent->username }}</option>
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
					<option value="{{ $role->id }}">{{ $role->name }}</option>
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
		$(this).data('timestamp', Math.floor(e.timeStamp/1e3));
	});

	$('form').on('submit', function()
	{
		return false;
	}).validate({
		submitHandler: function(form){
			$this = $(form);
			$datepicker.val($datepicker.data('timestamp'));
			
			$.ajax({
				url: $this.attr('action'),
				type: 'post',
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
									window.location.href = "{{ route('v1.users.index') }}";
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
			username: {
				required: true
			},
			password: {
				required: true
			},
			confirm_password: {
				required: true,
				equalTo: '#password'
			},
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