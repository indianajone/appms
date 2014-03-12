<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            @section('title')
            App Management
            @show
        </title>
        
        {{ HTML::style('css/bootstrap/bootstrap.min.css') }}
        {{ HTML::style('css/font-awesome/font-awesome.min.css') }}
        {{-- HTML::style('css/admin.css') --}}
        <!-- Extra CSS -->
        @yield('css')

        {{ HTML::style('css/style.css') }}

        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <div class="navbar navbar-inverse" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><i class="fa fa-rocket fa-fw"></i> App Manager</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown" >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i> hello {{ Auth::user()->username }} <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="fa fa-user fa-fw"></i> Profile</a></li>
                            <li><a href="#"><i class="fa fa-cog fa-fw"></i> Setting</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ URL::to('v1/users/logout') }}"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
            
        <!-- Begin page content -->
        <div class="container-fluid">
        
            <div class="alert"></div>
        
            <div class="row">
                <!-- Menu -->
                <div class="col-sm-4 col-md-3 navbar-sidebar" role="navigation">
                    @include('layouts.sidebar')
                </div>
                <!-- Content -->
                <div class="col-sm-8 col-sm-offset-4 col-md-9 col-md-offset-3">
                    @yield('content')
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
                <p class="text-muted">App Manager &copy; 2014</p>
            </div>
        </div>
        
        {{ HTML::script('js/jquery/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/bootstrap/bootstrap.min.js') }}
        {{ HTML::script('js/metisMenu/jquery.metisMenu.js') }}
        {{ HTML::script('js/admin.js') }}
        <!-- Extra Javascript -->
        @yield('js', '')

        @yield('javascript', '')

    </body>
</html>