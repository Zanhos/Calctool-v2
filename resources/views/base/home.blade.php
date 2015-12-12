<?php
use \Calctool\Models\Relation;
use \Calctool\Models\RelationKind;
use \Calctool\Models\RelationType;
use \Calctool\Models\Province;
use \Calctool\Models\Country;
use \Calctool\Models\Contact;
use \Calctool\Models\ContactFunction;
?>

@extends('layout.master')

<?php
$next_step = Cookie::get('nstep');
if (Input::get('nstep') == 'intro')
	$next_step = 'intro_'.Auth::id();

$relation = Relation::find(Auth::user()->self_id);
?>

@section('content')

@if ($next_step && $next_step=='intro_'.Auth::id())
<script type="text/javascript">
$(function() {
	$('#tutModal').modal('toggle');
	$('button[data-action="hide"]').click(function(){
		$.get("/hidenextstep").fail(function(e) { console.log(e); });
	});
	var zipcode = $('#zipcode').val();
	var number = $('#address_number').val();
	$('.autoappend').blur(function(e){
		if (number == $('#address_number').val() && zipcode == $('#zipcode').val())
			return;
		zipcode = $('#zipcode').val();
		number = $('#address_number').val();
		if (number && zipcode) {

			$.post("mycompany/quickstart/address", {
				zipcode: zipcode,
				number: number,
			}, function(data) {
				if (data) {
					var json = $.parseJSON(data);
					$('#street').val(json.street);
					$('#city').val(json.city);
					$("#province").find('option:selected').removeAttr("selected");
					$('#province option[value=' + json.province_id + ']').attr('selected','selected');
				}
			});
		}
	});
});
</script>
<div class="modal fade" id="tutModal" tabindex="-1" role="dialog" aria-labelledby="tutModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #333">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myModalLabel">Welkom bij de Calculatietool</h4>
			</div>

			<div class="modal-body">
				<p>Na het invullen van deze QuickStart kan je direct starten met de CalculatieTool.

				@if($errors->has())
				<div class="alert alert-danger">
					<i class="fa fa-frown-o"></i>
					<strong>Fout</strong>
					@foreach ($errors->all() as $error)
						{{ $error }}
					@endforeach
				</div>
				@endif

				<form action="/mycompany/quickstart" method="post">
				{!! csrf_field() !!}

				<h4 class="company">Jouw Bedrijfsgegevens</h4>
				<input type="hidden" name="id" id="id" value="{{ $relation ? $relation->id : '' }}"/>
				<div class="row">
					<div class="col-md-7">
						<div class="form-group">
							<label for="company_name">Bedrijfsnaam</label>
							<input name="company_name" id="company_name" type="text" value="{{ Input::old('company_name') ? Input::old('company_name') : ($relation ? $relation->company_name : '') }}" class="form-control" />
						</div>
					</div>
					<div class="col-md-5">
						<div class="form-group">
							<label for="company_type">Bedrijfstype*</label>
							<select name="company_type" id="company_type" class="form-control pointer">
							@foreach (RelationType::all() as $type)
								<option {{ $relation ? ($relation->type_id==$type->id ? 'selected' : '') : '' }} value="{{ $type->id }}">{{ ucwords($type->type_name) }}</option>
							@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label for="address_number">Huis nr.*</label>
							<input name="address_number" id="address_number" type="text" value="{{ Input::old('address_number') ? Input::old('address_number') : ($relation ? $relation->address_number : '') }}" class="form-control autoappend"/>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label for="zipcode">Postcode*</label>
							<input name="zipcode" id="zipcode" maxlength="6" type="text" value="{{ Input::old('zipcode') ? Input::old('zipcode') : ($relation ? $relation->address_postal : '') }}" class="form-control autoappend"/>
						</div>
					</div>

					<div class="col-md-7">
						<div class="form-group">
							<label for="street">Straat*</label>
							<input name="street" id="street" type="text" value="{{ Input::old('street') ? Input::old('street') : ($relation ? $relation->address_street : '') }}" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="city">Plaats*</label>
							<input name="city" id="city" type="text" value="{{ Input::old('city') ? Input::old('city') : ($relation ? $relation->address_city : '') }}" class="form-control"/>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="province">Provincie*</label>
							<select name="province" id="province" class="form-control pointer">
								@foreach (Province::all() as $province)
									<option {{ $relation ? ($relation->province_id==$province->id ? 'selected' : '') : '' }} value="{{ $province->id }}">{{ ucwords($province->province_name) }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="country">Land*</label>
							<select name="country" id="country" class="form-control pointer">
								@foreach (Country::all() as $country)
									<option {{ $relation ? ($relation->country_id==$country->id ? 'selected' : '') : ($country->country_name=='nederland' ? 'selected' : '')}} value="{{ $country->id }}">{{ ucwords($country->country_name) }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>

				<h4>Jouw Contactgegevens</h4>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="contact_firstname">Voornaam*</label>
							<input name="contact_firstname" id="contact_firstname" type="text" value="{{ Input::old('contact_firstname') ? Input::old('contact_firstname') : ($relation ? Contact::where('relation_id', $relation->id)->first()['firstname'] : '') }}" class="form-control"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="contact_name">Achternaam*</label>
							<input name="contact_name" id="contact_name" type="text" value="{{ Input::old('contact_name') ? Input::old('contact_name') : ($relation ? Contact::where('relation_id', $relation->id)->first()['lastname'] : '') }}" class="form-control"/>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="email">Email*</label>
							<input name="email" id="email" type="email" value="{{ Auth::user()->email }}" class="form-control"/>
						</div>
					</div>
					<div class="col-md-3 company">
						<div class="form-group">
							<label for="contactfunction">Functie</label>
							<select name="contactfunction" id="contactfunction" class="form-control pointer">
							@foreach (ContactFunction::all() as $function)
								<option {{ $function->function_name=='directeur' ? 'selected' : '' }} value="{{ $function->id }}">{{ ucwords($function->function_name) }}</option>
							@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="col-md-12">
					<button class="btn btn-primary"><i class="fa fa-check"></i> Opslaan</button>
				</div>
			</div>
			</form>

		</div>
	</div>
</div>
@endif
<div id="wrapper">

	<div id="shop">
		<section class="container">

			@if (Calctool\Models\SysMessage::where('active','=',true)->count()>0)
			@if (Calctool\Models\SysMessage::where('active','=',true)->orderBy('created_at', 'desc')->first()->level==1)
			<div class="alert alert-warning">
				<i class="fa fa-fa fa-info-circle"></i>
				{{ Calctool\Models\SysMessage::where('active','=',true)->orderBy('created_at', 'desc')->first()->content }}
			</div>
			@else
			<div class="alert alert-danger">
				<i class="fa fa-warning"></i>
				<strong>{{ Calctool\Models\SysMessage::where('active','=',true)->orderBy('created_at', 'desc')->first()->content }}</strong>
			</div>
			@endif
			@endif

			@if (!Auth::user()->hasPayed())
			<div class="alert alert-danger">
				<i class="fa fa-danger"></i>
				Account is gedeactiveerd, abonnement is verlopen.
			</div>
			@endif

			<h2><strong>Dash</strong>board</h2>
			<div class="row">

				<div class="col-sm-6 col-md-2">
					<div class="item-box item-box-show fixed-box">
						<figure>
							<a class="item-hover" href="/mycompany">
								<span class="overlay color2"></span>
								<span class="inner">
									<span class="block fa fa-home fsize60"></span>
									<strong>Mijn Bedrijf</strong>
								</span>
							</a>
							<a href="/mycompany" class="btn btn-primary add_to_cart"><i class="fa fa-home"></i> Mijn Bedrijf</a>

						</figure>
					</div>
				</div>

				<div class="col-sm-6 col-md-2">
					<div class="item-box item-box-show fixed-box">
						<figure>
							<a class="item-hover" href="/material">
								<span class="overlay color2"></span>
								<span class="inner">
									<span class="block fa fa-wrench fsize60"></span>
									<strong>Materialen</strong>
								</span>
							</a>
							<a href="/material" class="btn btn-primary add_to_cart"><i class="fa fa-wrench"></i> Materialen</a>
						</figure>
					</div>
				</div>

				<div class="col-sm-6 col-md-2">
					<div class="item-box item-box-show fixed-box">
						<figure>
							<a class="item-hover" href="/timesheet">
								<span class="overlay color2"></span>
								<span class="inner">
									<span class="block fa fa-clock-o fsize60"></span>
									<strong>Urenregistratie</strong>
								</span>
							</a>
							<a href="/timesheet" class="btn btn-primary add_to_cart"><i class="fa fa-clock-o"></i> Urenregistratie</a>
						</figure>
					</div>
				</div>

				<div class="col-sm-6 col-md-2">
					<div class="item-box item-box-show fixed-box">
						<figure>
							<a class="item-hover" href="/purchase">
								<span class="overlay color2"></span>
								<span class="inner">
									<span class="block fa fa-shopping-cart fsize60"></span>
									<strong>Inkoopfacturen</strong>
								</span>
							</a>
							<a href="/purchase" class="btn btn-primary add_to_cart"><i class="fa fa-shopping-cart"></i> Inkoopfacturen</a>
						</figure>
					</div>
				</div>

				<div class="col-sm-6 col-md-2">
					<div class="item-box item-box-show fixed-box">
						<figure>
							<a class="item-hover" href="/wholesale">
								<span class="overlay color2"></span>
								<span class="inner">
									<span class="block fa fa-truck fsize60"></span>
									<strong>Leveranciers</strong>
								</span>
							</a>
							<a href="/wholesale" class="btn btn-primary add_to_cart"><i class="fa fa-truck"></i> Leveranciers</a>
						</figure>
					</div>
				</div>

				<div class="col-sm-6 col-md-2">
					<div class="item-box item-box-show fixed-box">
						<figure>
							<a class="item-hover" href="/">
								<span class="overlay color2"></span>
								<span class="inner">
									<span class="block fa fa-bar-chart-o fsize60"></span>
									<strong>Financieel</strong>
									<br>
									<span>Comming Soon</span>
								</span>
							</a>
							<a href="/" class="btn btn-primary add_to_cart"><i class="fa fa-bar-chart-o"></i> Financieel</a>
						</figure>
					</div>
				</div>

			</div>

			<div class="row">

				<div class="col-md-6">
					<div class="white-row" style="min-height: 280px;">
						<div class="pull-right">


					<div class="btn-group">
							  <a href="/project/new" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Nieuw project</a>
							  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    <span class="caret"></span>
							    <span class="sr-only">Toggle Dropdown</span>
							  </button>
							  <ul class="dropdown-menu">
							    <li><a href="/project">Alle Projecten</a></li>
							  </ul>
							</div>

						</div>
						<h2><strong>Actieve</strong> Projecten</h2>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Project</th>
										<th>Opdrachtgever</th>
										<th>Type</th>
									</tr>
								</thead>
								<tbody>
									@foreach (Calctool\Models\Project::where('user_id','=', Auth::id())->whereNull('project_close')->orderBy('created_at', 'desc')->limit(5)->get() as $project)
									<?php $relation = \Calctool\Models\Relation::find($project->client_id); ?>
									<tr>
										<td><a href="{{ '/project-'.$project->id.'/edit' }}">{{ $project->project_name }}</td>
										<td>{!! \Calctool\Models\RelationKind::find($relation->kind_id)->kind_name == 'zakelijk' ? ucwords($relation->company_name) : (\Calctool\Models\Contact::where('relation_id','=',$relation->id)->first()['firstname'].' '.\Calctool\Models\Contact::where('relation_id','=',$relation->id)->first()['lastname']); !!}</td>
										<td>{!! ucfirst($project->type->type_name) !!}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="white-row" style="min-height: 280px;">
						<div class="pull-right">

						<div class="btn-group">
						  <a href="relation/new" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Nieuwe Relatie</a>
						  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						    <li><a href="/relation">Alle Relaties</a></li>
						  </ul>
						</div>

						</div>
						<h2><strong>Laatste</strong> Relaties</h2>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Naam</th>
										<th>Email</th>
										<th>Type</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$userid = Auth::user()->self_id;
									if(Auth::user()->self_id)
										$userid = Auth::user()->self_id;
									else
										$userid = -1;
									?>
									@foreach (Calctool\Models\Relation::where('user_id','=', Auth::id())->where('id','!=',$userid)->where('active',true)->orderBy('created_at', 'desc')->limit(5)->get() as $relation)
									<?php $contact = Calctool\Models\Contact::where('relation_id','=',$relation->id)->where('active',true)->first(); ?>
									<tr>
										<td><a href="{{ 'relation-'.$relation->id.'/edit' }}">{{ $relation->company_name ? $relation->company_name : $contact->firstname .' '. $contact->lastname }}</a></td>
										<td>{{ $relation->company_name ? $relation->email : $contact->email }}</td>
										<td>{{ ucfirst(RelationKind::find($relation->kind_id)->kind_name) }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>

		</section>
	</div>
</div>
@stop
