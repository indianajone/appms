@extends('layouts.master')

@section('content')

<h1>Edit {{ $user->username }}</h1>
{{ Form::open(array('url' => URL::to('api/v1/users/'.$user->id), 'method' => 'put', 'role'=>'form')) }}
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
	{{ Form::button('Save', array('class'=>'btn btn-success', 'type'=>'submit')) }}
{{ Form::close() }}

@stop


@section('javascript')

<script type="text/javascript">

		$('form').eq(0).on('submit', function(e){
			var $this = $(this);
			$.ajax({
				url: $this.attr('action'),
				success: function(result)
				{
					alert(result.header.code);
				}
			});

			return false;	
		});
</script>

@stop