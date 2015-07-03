@extends('layout.master')

@section('content')
<?# -- WRAPPER -- ?>
<div id="wrapper">

	<div id="shop">

		<section class="container">

			<div class="row">

				<?# -- LOGIN -- ?>
				<div class="col-md-6">

					<h2><strong>Login</strong></h2>

					{{ Form::open(array('url' => 'login', 'class' => 'white-row')) }}

						<?# -- alert failed -- ?>
						@if($errors->any())
						<div class="alert alert-danger">
							<i class="fa fa-frown-o"></i>
							@foreach ($errors->all() as $error)
								{{ $error }}
							@endforeach
						</div>
						@endif

						<div class="row">
							<div class="form-group">
								<div class="col-md-12">
									{{ Form::label('username', 'Gebruikersnaam') }}
									{{ Form::text('username', Input::old('username'), array('class' => 'form-control')) }}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<div class="col-md-12">
									{{ Form::label('secret', 'Wachtwoord') }}
									{{ Form::password('secret', array('class' => 'form-control')) }}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<span class="remember-box checkbox">
									{{ Form::label('rememberme', 'Onthoud gegevens') }}
									{{ Form::checkbox('rememberme') }}
								</span>
							</div>
							<div class="col-md-6">
								{{ Form::submit('Login', array('class' => 'btn btn-primary pull-right', 'data-loading-text' => 'Laden...')) }}
							</div>
						</div>

					{{ Form::close() }}

				</div>
				<?#-- /LOGIN -- ?>

				<?#-- PASSWORD --?>
				<div class="col-md-6">

					<h2>Wachtwoord <strong>Vergeten</strong>?</h2>

					<div class="white-row">

						<p>
							Heb je een account maar ben je het wachtwoord vergeten? Vraag dan hieronder een nieuwe wachtwoord aan. Mocht het niet lukken neem dan contact op met de <a href="#">helpdesk</a>.
						</p>

						<?# -- alert success -- ?>
						@if(Session::get('success'))
						<div class="alert alert-success">
							<i class="fa fa-check-circle"></i>
							<strong>Instructies verzonden.</strong> Check je e-mail!
						</div>
						@endif

						<?# -- password form -- ?>
						<label>Vul uw e-mailadres hier in</label>
						{{-- Form::open(array('url' => 'password/reset', 'class' => 'input-group')) --}}
						<form class="input-group" method="post" action="password/reset">
							<input type="text" class="form-control" name="email" id="email" value="" placeholder="E-mail adres" />
							<span class="input-group-btn">
								<button class="btn btn-primary">Verzenden</button>
							</span>
						{{ Form::close() }}

					</div>

				</div>
				<?# -- /PASSWORD -- ?>

			</div>


			<p class="white-row">
				Nog geen account? <a href="/register">Maak er een aan</a>, het is gratis!
			</p>

		</section>

	</div>
</div>
<?# -- /WRAPPER -- ?>
@stop
