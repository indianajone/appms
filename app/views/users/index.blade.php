@extends('layouts.master')

@section('css')

{{ HTML::style('css/plugins/dataTables/dataTables.bootstrap.css') }}

@stop

@section('content')

<h1>User Management</h1>
<div class="table-responsive">
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
			@foreach ($users as $key => $user)		
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

@stop

@section('js')

{{ HTML::script('js/dataTables/jquery.dataTables.js') }}
{{ HTML::script('js/dataTables/dataTables.bootstrap.js') }}

@stop

@section('javascript')

<script type="text/javascript">
	$(function(){
		$('.table-data').dataTable({
			aoColumnDefs: [
			  {
			     bSortable: false,
			     aTargets: [ -1 ]
			  }
			]
		});
	});
</script>

@stop