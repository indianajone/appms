@extends('layouts.master')

@section('content')


@if(isset($message))

<h3>{{{ $message }}}</h3>

@else

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
				{{ Form::text('gender', $user->birthday, array('class'=>'form-control', 'placeholder'=>'Gender')) }}
			</div>
		</div>
	</div>
	{{ Form::button('Save', array('class'=>'btn btn-success', 'type'=>'submit')) }}
{{ Form::close() }}

	{{ var_dump($user->apps->toArray()) }}

	<select name="" multiple>


	</select>

	@if ($user->children->count() > 0)

<div class="row">
	<div class="table-responsive col-md-8">
		<table class="table table-striped table-hover table-data">
			<thead>
				<tr>
					<th>#</th>
					<th>Username</th>
					<th>Children</th>
					<th>Last Seen</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($user->children as $key => $user)		
				<tr>
					<td>{{ ($key+1) }}
					<td>{{ $user->username }}</td>
					<td>{{ $user->children->count() }}</td>
					<td>{{ Carbon\Carbon::createFromTimeStamp($user->last_seen)->format('d-m-y G:i:s') }}</td>
					<td>
						<a href="{{ URL::to('v1/users/'.$user->id.'/edit') }}">
							<span class="fa-stack">
								<i class="fa fa-circle fa-stack-2x"></i>
								<i class="fa fa-edit fa-stack-1x fa-inverse"></i>
							</span>
						</a>
						<a href="#">
							<span class="fa-stack">
								<i class="fa fa-circle fa-stack-2x"></i>
								<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
							</span>
						</a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

	@endif

@endif

@stop


@section('javascript')

<script type="text/javascript">

		$('form').eq(0).on('submit', function(e){
			var $this = $(this);
			// $.ajax({
			// 	url: $this.attr('action'),
			// 	success: function(result)
			// 	{
			// 		alert(result.header.code);
			// 	}
			// });

			console.log( Date.parse($('#birthday').val()) / 1000);

			return false;	
		});
</script>

@stop