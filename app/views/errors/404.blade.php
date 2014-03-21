<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>App Management : 404 page not found</title>
        
        {{ HTML::style('css/bootstrap/bootstrap.min.css') }}
        {{ HTML::style('css/font-awesome/font-awesome.min.css') }}
    </head>
    <body>
       	<div class="container">
			<div class="page-header">
				<div class="panel panel-warning text-center ">
					<div class="panel-heading">
						<h1>
							404 Page not Found<br>
							<small>Are you lost?</small>
						</h1>
						<a class="btn btn-lg btn-primary" href="{{ URL::to('/') }}">Take me home</a>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>