@extends('layouts.master')

@section('content')

<div class="page-header">
	<h1>User Management</h1>
	<div class="btn-group">
		<a class="btn btn-primary" href="{{ URL::to('v1/users/create') }}" title="new user"><i class="fa fa-plus"></i> <i class="fa fa-user"></i></a>
	</div>
</div>

<div class="table-responsive">
	<table class="table table-striped table-hover table-data">
		<thead>
			<tr>
				<th class="text-center">#</th>
				<th>Username</th>
				<th>Email</th>
				<th>Last Seen</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>

@stop

@section('js')

{{ HTML::script('js/dataTables/jquery.dataTables.js') }}
{{ HTML::script('js/dataTables/dataTables.bootstrap.js') }}
{{ HTML::script('js/moment/moment.min.js') }}

@stop

@section('javascript')

<script type="text/javascript">

	$(function()
	{
		var dt = $('.table-data').DataTable({
			// processing: true,
			serverSide: true,
			ajax: $.fn.dataTable.pipeline( {
	            url: '{{ URL::to("api/v1/users?user_id=").Auth::user()->id }}',
	            pages: 1, // number of pages to cache
	            data: function ( d ) {
	                d.fields = "id,username,email,last_seen";
	                d.q = d.search['value'];
	                d.offset = d.start;
	                d.limit = d.length;
	                d.order_by = d.columns[d.order[0].column].name + ','+ d.order[0].dir;
	          	}
	        } ),
			columns: [
				{
					"data": "id",
					"searchable": false,
					"orderable": false,
					render: function(data)
					{
						return '<input type="checkbox" value="'+ data +'" />'
					}
				},
            	{ 
            		"name": "username",
            		"data": "username"
            	},
            	{ 
            		"name": "email",
            		"data": "email"
            	},
            	{ 
            		"name": "last_seen",
            		"data": "last_seen",
            		"searchable": false,
            		// "orderable": false,
					render: function ( data, type, row ) 
					{
						if(!data) return '<span class="label label-danger">Never</span>';

				        // If display or filter data is requested, format the date
				        if ( type === 'display' || type === 'filter' ) {
				            return moment(data * 1000).fromNow();
				        }
				 
				        // Otherwise the data type requested (`type`) is type detection or
				        // sorting data, for which we want to use the integer, so just return
				        // that, unaltered
				        return data;
				    }
            	},
            	{ 
            		"name": "actions",
            		"data": "id",
            		"searchable": false,
            		"orderable": false,
            		"render": function(data){
            			return 	'<a href="{{ URL::to('v1/users/') }}/' + data + '/edit' + '" data-toggle="tooltip" data-placement="top" title="Edit">' +
									'<span class="fa-stack">' +
										'<i class="fa fa-circle fa-stack-2x text-warning"></i>' +
										'<i class="fa fa-edit fa-stack-1x fa-inverse"></i>' +
									'</span>' +
								'</a>' +
								'<a href="#" data-toggle="tooltip" data-placement="top" title="Delete">' +
									'<span class="fa-stack">' +
										'<i class="fa fa-circle fa-stack-2x text-danger"></i>' +
										'<i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>' +
									'</span>' +
								'</a>';
            		}
            	}
            ]
		});
	});

</script>

@stop