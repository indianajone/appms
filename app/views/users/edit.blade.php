@extends('layouts.master')

@section('content')


@if(isset($message))

<h3>{{{ $message }}}</h3>

@else

<h1>Edit {{ $user->username }}</h1>
{{ Form::open(array('url' => route('api.v1.users.update', $user->id), 'method' => 'PUT', 'role'=>'form')) }}
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				{{ Form::label('first_name', 'First name') }}
				{{ Form::text('first_name', $user->first_name, array('class'=>'form-control', 'placeholder'=>'First name')) }}
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
	{{ Form::button('Save', array('class'=>'btn btn-success', 'type'=>'button')) }}
{{ Form::close() }}

@endif

@stop

@section('javascript')

<script type="text/javascript">

		$('.btn-success').on('click', function(e){
			var $this = $('form').eq(0);
			// e.preventDefault();

			console.log($this.serialize());

			$.ajax({
				url: $this.attr('action'),
				type: 'put',
				dataType: 'json',
				data: $this.serialize(),
				success: function(result)
				{
					$alert = $('.alert').fadeIn('fast');
					console.log(result);
					switch(result.header.code)
					{
						case 204:
							$alert.addClass('alert-warning')
								.delay(3000).fadeOut('slow', function() {
									$(this).removeClass('alert-warning');
								});

						break;

						default:
							$alert.addClass('alert-success')
								.delay(3000).fadeOut('slow', function() {
									$(this).removeClass('alert-success');
								});
					}

					$alert.text(result.header.message);
				},
				error: function(e)
				{
					console.log(e);
				}
			});

			
		});
</script>

@stop