<?php
$project = Project::find(Route::Input('project_id'));
?>

@extends('layout.master')

@section('content')
<?# -- WRAPPER -- ?>labor-amount

<script type="text/javascript">
	$(document).ready(function() {
		$(".radio-activity").change(function(){
			$.post("/calculation/updatepart", {value: this.value, activity: $(this).attr("data-id")}).fail(function(e) { console.log(e); });
		});
		$(".check-activity").change(function(){
			$.post("/calculation/updateparttype", {value: this.value, activity: $(this).attr("data-id")}).fail(function(e) { console.log(e); });
		});
		$(".labor-amount").change(function(){
			$.post("/calculation/updateamount", {amount: this.value, activity: $(this).attr("data-id")}).fail(function(e) { console.log(e); });
		});
	});


</script>

<div id="wrapper">

	<section class="container">

		<div class="col-md-12">

			<div class="fuelux">
				<div id="calculation-wizard" class="wizard">
					<ul class="steps">Debiteurennummer nieuwe relatie
						<li data-target="#step0">Home<span class="chevron"></span></li>
						<li data-target="#step1" class="complete">Projectgegevens<span class="chevron"></span></li>
						<li data-target="#step2" class="active">Calculatie<span class="chevron"></span></li>
						<li data-target="#step3">Offerte<span class="chevron"></span></li>
						<li data-target="#step4">Stelpost<span class="chevron"></span></li>
						<li data-target="#step5">Minderwerk<span class="chevron"></span></li>
						<li data-target="#step6">Meerwerk<span class="chevron"></span></li>
						<li data-target="#step7">Factuur<span class="chevron"></span></li>
						<li data-target="#step8">Winst/Verlies<span class="chevron"></span></li>
					</ul>
				</div>
			</div>

			<hr />

			<h2><strong>Calculeren</strong></h2>

			<div class="tabs nomargin">

				<!-- taDebiteurennummer nieuwe relatiebs -->
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#calculate" data-toggle="tab">
							<i class="fa fa-list-ol"></i> Calculeren
						</a>
					</li>
					<li>
						<a href="#summary" data-toggle="tab">
							<i class="fa fa-sort-amount-desc"></i> Uittrekstaat
						</a>
					</li>
					<li>
						<a href="#endresult" data-toggle="tab">
							<i class="fa fa-check-circle-o"></i> Eindresultaat
						</a>
					</li>
				</ul>

				<!-- tabs content -->
				<div class="tab-content">
					<div id="calculate" class="tab-pane active">
						<div class="toogle">

							@foreach (Chapter::where('project_id','=', $project->id)->get() as $chapter)
							<div class="toggle">
								<label>{{ $chapter->chapter_name }}</label>
								<div class="toggle-content">

									<div class="toogle">

										@foreach (Activity::where('chapter_id','=', $chapter->id)->get() as $activity)
										<div class="toggle">
											<label>{{ $activity->activity_name }}</label>
											<div class="toggle-content">

												<div class="col-md-12">

													<div class="col-md-10">
														<span class="pull-right">
															<div class="form-group">
																<label class="radio-inline"><input data-id="{{ $activity->id }}" class="radio-activity" name="soort{{ $activity->id }}" value="{{ Part::where('part_name','=','contracting')->first()->id }}" type="radio" {{ ( Part::find($activity->part_id)->part_name=='contracting' ? 'checked' : '') }}/>Aanneming</label>
	    														<label class="radio-inline"><input data-id="{{ $activity->id }}" class="radio-activity" name="soort{{ $activity->id }}" value="{{ Part::where('part_name','=','subcontracting')->first()->id }}" type="radio" {{ ( Part::find($activity->part_id)->part_name=='subcontracting' ? 'checked' : '') }}/>Onderaanneming</label>
															</div>
														</span>
													</div>
													<div class="col-md-2">
														<span class="pull-right">
															<label class="checkbox-inline"><input data-id="{{ $activity->id }}" class="check-activity" type="checkbox" name="estimate" {{ ( PartType::find($activity->part_type_id)->type_name == 'estimate' ? 'checked' : '') }} value="{{ PartType::where('type_name','=','estimate')->first()->id }}" class="form-control">Stelpost</label>
														</span>
													</div>
												</div>

												<h4>Arbeid</h4>
												<table class="table table-striped">
													<?# -- table head -- ?>
													<thead>
														<tr>
															<th class="col-md-2">Eenheid</th>
															<th class="col-md-1">Tarief</th>
															<th class="col-md-1">Arbeidsuren</th>
															<th class="col-md-1">Arbeidskosten</th>
															<th class="col-md-2">&nbsp;</th>
															<th class="col-md-1">&nbsp;</th>
															<th class="col-md-2">&nbsp;</th>
															<th class="col-md-1">BTW</th>
															<th class="col-md-1">&nbsp;</th>
														</tr>
													</thead>

													<!-- table items -->
													<tbody>
														<tr><!-- item -->
															<td class="col-md-2">Per Uur</td>
															<td class="col-md-1">{{ $project->hour_rate }}</td>
															<td class="col-md-1"><input data-id="{{ $activity->id }}" name="name" type="text" value="" class="form-control-number control-sm labor-amount" /></td>
															<td class="col-md-1">$1</td>
															<td class="col-md-2">&nbsp;</td>
															<td class="col-md-1">&nbsp;</td>
															<td class="col-md-2">&nbsp;</td>
															<td class="col-md-1">
																<select name="type" id="type" class="form-control-number pointer control-sm labor-amount">
																@foreach (Tax::all() as $tax)
																	<option value="{{ $tax->id }}" selected="selected">{{ $tax->tax_rate }}%</option>
																@endforeach
																</select>
															</td>
															<td class="col-md-1"><button class="btn btn-danger btn-xs fa fa-times"></button></td>
														</tr>
													</tbody>
												</table>

												<h4>Materiaal</h4>
												<table class="table table-striped">
													<?# -- table head -- ?>
													<thead>
														<tr>
															<th class="col-md-3">Materiaalsoort</th>
															<th class="col-md-2">Eenheid</th>
															<th class="col-md-1">Prijs/eenheid</th>
															<th class="col-md-1">Hoeveelheid</th>
															<th class="col-md-1">Totaalprijs</th>
															<th class="col-md-2">Incl. Winst</th>
															<th class="col-md-1">BTW</th>
															<th class="col-md-1">&nbsp;</th>
														</tr>
													</thead>

													<!-- table items -->
													<tbody>
														<tr>
															<td class="col-md-3"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-2"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="number" min="0" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1 centering">$20.000,00</td>
															<td class="col-md-2">$40</td>
															<td class="col-md-1">
																<select name="type" id="type" class="form-control-number pointer control-sm">
																@foreach (Tax::all() as $tax)
																	<option value="{{ $tax->id }}" selected="selected">{{ $tax->tax_rate }}%</option>
																@endforeach
																</select>
															</td>
															<td class="col-md-1"><button class="btn btn-danger btn-xs fa fa-times"></button></td>
														</tr>
														<tr>
															<td class="col-md-3"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-2"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="number" min="0" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1">$20.000,00</td>
															<td class="col-md-2">$40</td>
															<td class="col-md-1">
																<select name="type" id="type" class="form-control-number pointer control-sm">
																@foreach (Tax::all() as $tax)
																	<option value="{{ $tax->id }}" selected="selected">{{ $tax->tax_rate }}%</option>
																@endforeach
																</select>
															</td>
															<td class="col-md-1"><button class="btn btn-danger btn-xs fa fa-times"></button></td>
														</tr>
													</tbody>
												</table>

												<h4>Materieel</h4>
												<table class="table table-striped">
													<?# -- table head -- ?>
													<thead>
														<tr>
															<th class="col-md-3">Materiaalsoort</th>
															<th class="col-md-2">Eenheid</th>
															<th class="col-md-1">Prijs/eenheid</th>
															<th class="col-md-1">Hoeveelheid</th>
															<th class="col-md-1">Totaalprijs</th>
															<th class="col-md-2">Incl. Winst</th>
															<th class="col-md-1">BTW</th>
															<th class="col-md-1">&nbsp;</th>
														</tr>
													</thead>

													<!-- table items -->
													<tbody>
														<tr>
															<td class="col-md-3"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-2"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="number" min="0" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1">$20.000,00</td>
															<td class="col-md-2">$40</td>
															<td class="col-md-1">
																<select name="type" id="type" class="form-control-number pointer control-sm">
																@foreach (Tax::all() as $tax)
																	<option value="{{ $tax->id }}" selected="selected">{{ $tax->tax_rate }}%</option>
																@endforeach
																</select>
															</td>
															<td class="col-md-1"><button class="btn btn-danger btn-xs fa fa-times"></button></td>
														</tr>
														<tr>
															<td class="col-md-3"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-2"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="text" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1"><input name="name" id="name" type="number" min="0" value="" class="form-control-number control-sm" /></td>
															<td class="col-md-1">$20.000,00</td>
															<td class="col-md-2">$40</td>
															<td class="col-md-1">
																<select name="type" id="type" class="form-control-number pointer control-sm">
																@foreach (Tax::all() as $tax)
																	<option value="{{ $tax->id }}" selected="selected">{{ $tax->tax_rate }}%</option>
																@endforeach
																</select>
															</td>
															<td class="col-md-1"><button class="btn btn-danger btn-xs fa fa-times"></button></td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
										@endforeach
									</div>

									{{ Form::open(array('url' => '/calculation/newactivity/' . $chapter->id)) }}
									<div class="row">
										<div class="col-md-6">

											<div class="input-group">
												<input type="text" class="form-control" name="activity" id="activity" value="" placeholder="Nieuwe Werkzaamheid">
												<span class="input-group-btn">
													<button class="btn btn-primary">Voeg toe</button>
												</span>
											</div>
										</div>
									</div>
									{{ Form::close() }}
								</div>
							</div>
							@endforeach
						</div>

						{{ Form::open(array('url' => '/calculation/newchapter/'.$project->id)) }}
						<div class="row">
							<div class="col-md-6">
								<div class="input-group">
									<input type="text" class="form-control" name="chapter" id="chapter" value="" placeholder="Nieuw Hoofdstuk">
									<span class="input-group-btn">
										<button class="btn btn-primary">Voeg toe</button>
									</span>
								</div>
							</div>
						</div>
						{{ Form::close() }}
					</div>

					<div id="summary" class="tab-pane">
						<div class="toogle">

							<div class="toggle active">
								<label>Aanneming</label>
								<div class="toggle-content">

									<table class="table table-striped">
										<?# -- table head -- ?>
										<thead>
											<tr>
												<th class="col-md-2">&nbsp;</th>
												<th class="col-md-2">&nbsp;</th>
												<th class="col-md-1">Arbeidsuren</th>
												<th class="col-md-1">Arbeidskosten</th>
												<th class="col-md-1">Materiaalkosten</th>
												<th class="col-md-1">Materieelkosten</th>
												<th class="col-md-3">Totaal (excl. BTW)</th>
												<th class="col-md-1">Stelpost</th>
											</tr>
										</thead>

										<!-- table items -->
										<tbody>
											<tr><!-- item -->
												<td class="col-md-2"><strong>Hoofdstuk 1</strong></td>
												<td class="col-md-2">Werkzaamheid 1</td>
												<td class="col-md-1">6</td>
												<td class="col-md-1">$42</td>
												<td class="col-md-1">$83</td>
												<td class="col-md-1">$742</td>
												<td class="col-md-3">$742,28</td>
												<td class="col-md-1">&nbsp;</td>
											</tr>
											<tr><!-- item -->
												<td class="col-md-2">&nbsp;</td>
												<td class="col-md-2">Werkzaamheid 2</td>
												<td class="col-md-1">6</td>
												<td class="col-md-1">$42</td>
												<td class="col-md-1">$83</td>
												<td class="col-md-1">$742</td>
												<td class="col-md-3">$742,28</td>
												<td class="col-md-1  fa fa-check">&nbsp;</td>
											</tr>
											<tr><!-- item -->
												<td class="col-md-2">&nbsp;</td>
												<td class="col-md-2">Werkzaamheid 3</td>
												<td class="col-md-1">6</td>
												<td class="col-md-1">$42</td>
												<td class="col-md-1">$83</td>
												<td class="col-md-1">$742</td>
												<td class="col-md-3">$742,28</td>
												<td class="col-md-1 fa fa-check">&nbsp;</td>
											</tr>
										</tbody>
									</table>

								</div>
							</div>

							<div class="toggle active">
								<label>Onderaanneming</label>
								<div class="toggle-content">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas metus nulla, commodo a sodales sed, dignissim pretium nunc. Nam et lacus neque. Ut enim massa, sodales tempor convallis et, iaculis ac massa. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas metus nulla, commodo a sodales sed, dignissim pretium nunc. Nam et lacus neque. Ut enim massa, sodales tempor convallis et, iaculis ac massa.</p>
								</div>
							</div>

							<div class="toggle active">
								<label>Totalen project</label>
								<div class="toggle-content">
									<table class="table table-striped">
										<?# -- table head -- ?>
										<thead>
											<tr>
												<th class="col-md-4"><span class="pull-right">Arbeidsuren</span></th>
												<th class="col-md-2"><span class="pull-right">Arbeidskosten</span></th>
												<th class="col-md-2"><span class="pull-right">Materiaalkosten</span></th>
												<th class="col-md-2"><span class="pull-right">Materieelkosten</span></th>
												<th class="col-md-2"><span class="pull-right">Totaal (excl. BTW)</span></th>
											</tr>
										</thead>

										<!-- table items -->
										<tbody>
											<tr><!-- item -->
												<td class="col-md-4"><span class="pull-right">6</span></td>
												<td class="col-md-2"><span class="pull-right">$42</span></td>
												<td class="col-md-2"><span class="pull-right">$83</span></td>
												<td class="col-md-2"><span class="pull-right">$742</span></td>
												<td class="col-md-2"><span class="pull-right">$742,28</span></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

						</div>
					</div>

					<div id="endresult" class="tab-pane">

						<h4>Aanneming</h4>
						<table class="table table-striped">
							<?# -- table head -- ?>
							<thead>
								<tr>
									<th class="col-md-4">&nbsp;</th>
									<th class="col-md-1">Manuren</th>
									<th class="col-md-2">Bedrag (excl. BTW)</th>
									<th class="col-md-1">&nbsp;</th>
									<th class="col-md-1">BTW</th>
									<th class="col-md-2">BTW bedrag</th>
									<th class="col-md-1">&nbsp;</th>
								</tr>
							</thead>

							<!-- table items -->
							<tbody>
								<tr><!-- item -->
									<td class="col-md-4">Arbeidskosten</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4">Materiaalkosten</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4">Materieelkosten</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4"><strong>Totaal Aanneming </strong></td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1"><strong>$6.362,71</strong></td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1"><strong>$6.362,71</strong></td>
								</tr>
							</tbody>
						</table>

						<h4>Onderaanneming</h4>
						<table class="table table-striped">
							<?# -- table head -- ?>
							<thead>
								<tr>
									<th class="col-md-4">&nbsp;</th>
									<th class="col-md-1">Manuren</th>
									<th class="col-md-2">Bedrag (excl. BTW)</th>
									<th class="col-md-1">&nbsp;</th>
									<th class="col-md-1">BTW</th>
									<th class="col-md-2">BTW bedrag</th>
									<th class="col-md-1">&nbsp;</th>
								</tr>
							</thead>

							<!-- table items -->
							<tbody>
								<tr><!-- item -->
									<td class="col-md-4">Arbeidskosten</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4">Materiaalkosten</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4">Materieelkosten</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4"><strong>Totaal Aanneming </strong></td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1"><strong>$6.362,71</strong></td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1"><strong>$6.362,71</strong></td>
								</tr>
							</tbody>
						</table>

						<h4>Stelposten</h4>
						<table class="table table-striped">
							<?# -- table head -- ?>
							<thead>
								<tr>
									<th class="col-md-4">&nbsp;</th>
									<th class="col-md-1">Manuren</th>
									<th class="col-md-2">Bedrag (excl. BTW)</th>
									<th class="col-md-1">&nbsp;</th>
									<th class="col-md-1">BTW</th>
									<th class="col-md-2">BTW bedrag</th>
									<th class="col-md-1">&nbsp;</th>
								</tr>
							</thead>

							<!-- table items -->
							<tbody>
								<tr><!-- item -->
									<td class="col-md-4">Arbeidskosten</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">6</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4">Materiaalkosten</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4">Materieelkosten</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">21%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">6%</td>
									<td class="col-md-2">$742,28</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-4">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-1">0%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1">&nbsp;</td>
								</tr>

								<tr><!-- item -->
									<td class="col-md-4"><strong>Totaal Aanneming </strong></td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1"><strong>$6.362,71</strong></td>
									<td class="col-md-1">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-1"><strong>$6.362,71</strong></td>
								</tr>
							</tbody>
						</table>

						<h4>Cumulatieven Offerte</h4>
						<table class="table table-striped">
							<?# -- table head -- ?>
							<thead>
								<tr>
									<th class="col-md-6">&nbsp;</th>
									<th class="col-md-2">Bedrag (excl. BTW)</th>
									<th class="col-md-2">BTW bedrag</th>
									<th class="col-md-2">&nbsp;</th>
								</tr>
							</thead>

							<!-- table items -->
							<tbody>
								<tr><!-- item -->
									<td class="col-md-6">Calculatief te offereren (excl. BTW)</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6">BTW bedrag aanneming belast met 21%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-2">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6">BTW bedrag aanneming belast met 6%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-2">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6">BTW bedrag onderaanneming belast met 21%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-2">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6">BTW bedrag onderaanneming belast met 6%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-2">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6">BTW bedrag stelposten belast met 21%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-2">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6">BTW bedrag stelposten belast met 6%</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">$42</td>
									<td class="col-md-2">&nbsp;</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6">Te offereren BTW bedrag</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">$3.826,38</td>
								</tr>
								<tr><!-- item -->
									<td class="col-md-6"><strong>Calculatief te offereren (Incl. BTW)</strong></td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2">&nbsp;</td>
									<td class="col-md-2"><strong>$3.826,38</strong></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

			</div>


		</div>

	</section>

</div>
<!-- /WRAPPER -->
@stop
