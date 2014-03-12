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
        <div class="container">
            {{ Form::open(array('id'=>'frm-login' ,'url' => URL::to('api/v1/users/login'), 'method' => 'POST', 'role'=>'form')) }}
                <h2 class="form-signin-heading">Please sign in</h2>
                <div class="form-group">
                    {{ Form::label('username', 'Username') }}
                    {{ Form::text('username', '', array('class'=>'form-control', 'placeholder'=>'Username', 'required', 'autoFocus'=>1)) }}
                </div>
                <div class="form-group">
                    {{ Form::label('password', 'Password') }}
                    {{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'Password', 'required')) }}
                </div>
                <div class="alert alert-danger hide"></div>
                {{ Form::button('Login', array('class'=>'btn btn-success btn-block', 'type'=>'submit')) }}
            {{ Form::close() }}

        </div>
        
        {{ HTML::script('js/jquery/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/bootstrap/bootstrap.min.js') }}

        <script type="text/javascript">
            $(function(){
                $('#frm-login').on('submit', function(e){
                    e.preventDefault();
                    $this = $(this);
                    $.ajax({
                        url: $this.attr('action'),
                        type: $this.attr('method'),
                        data: $this.serialize(),
                        success: function(result)
                        {
                            if(result.header.code == 200)
                            {
                                window.location.href = "{{ URL::to('v1/users') }}";
                            }
                            else
                            {
                                $('.alert').removeClass('hide').text(result.header.message);
                            }
                        }
                    });
                });
            });
        </script>

    </body>
</html>
