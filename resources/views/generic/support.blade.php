<?php

$user = null;
if (Auth::check()) {
	$user = Auth::user();
}
?>

@extends('layout.master')

@section('title', 'Support')

@push('scripts')
<script src="/plugins/summernote/summernote.min.js"></script>
@endpush

@section('content')

<script type="text/javascript">
$(document).ready(function() {

	 $('.summernote').summernote({
	        height: $(this).attr("data-height") || 200,
	        toolbar: [
	            ["style", ["bold", "italic", "underline", "strikethrough", "clear"]],
	            ["para", ["ul", "ol", "paragraph"]],
	            ["table", ["table"]],
	            ["media", ["link", "picture", "video"]],
	            ["misc", ["codeview"]]
	        ]
	    })
});
</script>

<div id="wrapper">

	<section id="contact" class="container">

		<div class="row">

			<div class="col-md-12">

				<div class="white-row">
				<h2>Helpdesk</h2>
				Telefoon: <a href="tel:+643587470">06 435 87470</a> of <a href="tel:+643587430">06 435 87430</a><br />
				Email: <a href="mailto:info@calculatietool.com">info@calculatietool.com</a><br />
				Stuur SMS, WhatsApp, Telegram naar bovenstaande telefoonnummers
				</div>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				@if(Session::get('success'))
				<div class="alert alert-success">
					<i class="fa fa-check-circle"></i>
					<strong>{{ Session::get('success') }}</strong>
				</div>
				@endif

				@if (count($errors) > 0)
				<div class="alert alert-danger">
					<i class="fa fa-frown-o"></i>
					<strong>Fouten in de invoer</strong>
					<ul>
						@foreach ($errors->all() as $error)
						<li><h5 class="nomargin">{{ $error }}</h5></li>
						@endforeach
					</ul>
				</div>
				@endif

				<h2>Stuur ons een <strong>bericht</strong>, stel een <strong>vraag</strong> of geef ons een <strong>belletje</strong></h2>

				<form class="white-row" action="/support" method="post">
				{!! csrf_field() !!}

					<div class="row">
						<div class="col-md-12">
						<h2>Bericht</h2>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<div class="col-md-6">
								<label>Naam *</label>
								<input type="text" value="{{ $user ? $user->username : '' }}" data-msg-required="Please enter your name." maxlength="100" class="form-control" name="name" id="name">
							</div>
							<div class="col-md-6">
								<label>E-mailadres *</label>
								<input type="email" value="{{ $user ? $user->email : '' }}" data-msg-required="Please enter your email address." data-msg-email="Please enter a valid email address." maxlength="100" class="form-control" name="email" id="email">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-md-12">
								<label>Onderwerp</label>
								<input type="text" value="" data-msg-required="Please enter the subject." maxlength="100" class="form-control" name="subject" id="subject">
							</div>
						</div>
					</div>
					<div class="row">

						<div class="form-group">
							<div class="col-md-12">
								<textarea name="message" id="message" rows="10" class="summernote form-control"></textarea>
							</div>
						</div>

					</div>

					<br />

					<div class="row">
						<div class="col-md-12">
							<input type="submit" value="Verstuur" class="btn btn-primary btn-lg">
						</div>
					</div>

					<br>

				</form>

			</div>
		</div>

	</section>

</div>
<?# -- /WRAPPER -- ?>
@stop
