@extends('layout.master')

@section('title', 'Login')

@push('style')
<link media="all" type="text/css" rel="stylesheet" href="/plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css">
@endpush

@push('scripts')
<script src="/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
@endpush

@section('content')

<script type="text/javascript">
$(function() {
    $("[name='rememberme']").bootstrapSwitch({onText: 'Ja',offText: 'Nee'});

	$('#passtoggle').click(function(){
		if ($('#secret').attr('type') == 'password') {
			$('#secret').attr('type', 'text');
			$('#passtoggle i').remove();
			$('<i class="glyphicon icon-eye-close glyphicon-eye-close"></i>').appendTo('#passtoggle');
		} else {
			$('#secret').attr('type', 'password');
			$('#passtoggle i').remove();
			$('<i class="glyphicon icon-eye-open glyphicon-eye-open"></i>').appendTo('#passtoggle');
		}
	});    
});
</script>

<div id="wrapper">

	<div id="shop">

		<section class="container">

			<div class="row">

				<div class="col-md-6">

					<h2><strong>Log</strong>In</h2>

					<form method="POST" action="/login" accept-charset="UTF-8" class="white-row">
					{!! csrf_field() !!}

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
									<label for="username">Gebruikersnaam of e-mailadres</label>
									<input class="form-control" name="username" type="text" id="username" value="{{ old('username') }}">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<div class="col-md-12">
									<label for="secret">Wachtwoord</label>
									<div class="input-append input-group">
										<input id="secret" name="secret" class="form-control" type="password" autocomplete="off">
										<input type="text" class="form-control" placeholder="password" style="display: none;">
										<span tabindex="100" id="passtoggle" title="Klik om wachtwoord te tonen/verbergen" class="add-on input-group-addon" style="cursor: pointer;">
											<i class="icon-eye-open glyphicon glyphicon-eye-open"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<input name="rememberme" class="left-label" type="checkbox">
									<label for="rememberme" style="margin-left:10px;">Onthoud gegevens</label>
								</div>
							</div>
							<div class="col-md-6">
								<input class="btn btn-primary pull-right" data-loading-text="Laden..." type="submit" value="Login">
							</div>
						</div>

					</form>

				</div>

				<div class="col-md-6">

					<h2>Wachtwoord <strong>Vergeten</strong>?</h2>

					<div class="white-row">

						<p>
							Heb je een account maar ben je het wachtwoord vergeten? Vraag dan hieronder een nieuwe wachtwoord aan. Mocht het niet lukken neem dan contact op met de <a href="#">helpdesk</a>.
						</p>

						@if(Session::get('success'))
						<div class="alert alert-success">
							<i class="fa fa-check-circle"></i>
							<strong>Instructies verzonden.</strong> Check je e-mail!
						</div>
						@endif

						<label class="nobold">Vul uw e-mailadres hier in</label>
						<form id="passforgot" method="post" action="password/reset">
							<div class="input-group">
							<input type="text" class="form-control" name="email" id="email" placeholder="E-mail adres" />
							<span class="input-group-btn">
								<button class="btn btn-primary" onclick="$('#passforgot').submit();" type="button">Verzenden</button>
							</span>
							</div>
						{!! csrf_field() !!}
						</form>

					</div>

				</div>

			</div>


			<div class="white-row">
				<h4>Nog Registreren?</h4>			
				<span>Nog geen account? <a href="/register">Maak er één aan</a>, het is <strong>gratis!</strong></span>
			</div>

		</section>

	</div>
</div>
@stop
