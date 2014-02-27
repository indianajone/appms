<div class="sidebar-collapse">
    <ul class="nav nav-sidebar">
        <li><a href="#"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
        <li {{ Request::is('v1/users') || Request::is('v1/users/*') ? 'class="active"' : '' }}><a href="{{ URL::to('v1/users/') }}"><i class="fa fa-users fa-fw"></i> User Management</a></li>
        <li>
        	<a href="#"><i class="fa fa-rocket fa-fw"></i> Application Management <span class="fa arrow pull-right"></span></a>
        	<ul class="nav">
        		<li><a href="#"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
        		<li><a href="#"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
        	</ul>
        </li>
    </ul>
</div>