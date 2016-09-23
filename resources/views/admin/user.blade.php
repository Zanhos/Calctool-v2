@extends('layout.master')

@section('content')

@section('title', 'Actieve gebruikers')

<?php
function userStatus($user)
{
	if (!$user->confirmed_mail)
		return "Emailactivatie";
	if ($user->banned)
		return "Geblokkeerd";
	if ($user->active)
		return "Actief";
	return "Inactief";
}
$all = false;
if (Input::get('all') == 1) {
	$all = true;
}

$group = null;
if (Input::has('group')) {
	$group = Input::get('group');
}

?>
<div id="wrapper">

	<section class="container">
		<div class="col-md-12">

			<div>
			<ol class="breadcrumb">
			  <li><a href="/">Home</a></li>
			  <li><a href="/admin">Admin CP</a></li>
			  <li class="active">Gebruikers</li>
			</ol>
			<div>
			<br />

			<div class="pull-right">
				@if ($all)
					<a class="btn btn-primary" href="/admin/user">Actieve gebruikers</a>
				@else
					<a class="btn btn-primary" href="?all=1">Alle gebruikers</a>
				@endif
			</div>

			<h2><strong>{{ ($all ? 'Alle' : ($group ? 'Groep' : 'Actieve')) }} gebruikers</strong></h2>

			<div class="white-row">
			<table class="table table-striped">
				<thead>
					<tr>
						<th class="col-md-1 hidden-xs">ID</th>
						<th class="col-md-2">Gebruikersnaam</th>
						<th class="col-md-2">Actief</th>
						<th class="col-md-3 hidden-xs">Email</th>
						<th class="col-md-1 hidden-sm hidden-xs">Status</th>
						<th class="col-md-1 hidden-sm hidden-xs">Type</th>
						<th class="col-md-2 hidden-sm hidden-xs">Group</th>
					</tr>
				</thead>

				<tbody>
				<?php
				if ($all) {
					$selection = \Calctool\Models\User::orderBy('updated_at','desc')->get();
				} else if (!empty($group)) {
					$selection = \Calctool\Models\User::where('user_group',$group)->get();
				} else {
					$selection = \Calctool\Models\User::where('active','=','true')->orderBy('updated_at','desc')->get();
				}
				?>
				@foreach ($selection as $users)
					<tr>
						<td class="col-md-1 hidden-xs"><a href="{{ '/admin/user-'.$users->id.'/edit' }}">{{ $users->id }}</a></td>
						<td class="col-md-2"><a href="{{ '/admin/user-'.$users->id.'/edit' }}"><?php
							echo $users->username;
							if ($users->firstname != $users->username) {
								echo ' (' . $users->firstname . ($users->lastname ? (', ' . $users->lastname) : '') . ')';
							}
						?></a></td>
						<td class="col-md-2">{{ $users->currentStatus() }}</td>
						<td class="col-md-3 hidden-xs">{{ $users->email }}</td>
						<td class="col-md-1 hidden-sm hidden-xs">{{ userStatus($users) }}</td>
						<td class="col-md-1 hidden-sm hidden-xs">{{ ucfirst(\Calctool\Models\UserType::find($users->user_type)->user_type) }}</td>
						<td class="col-md-1 hidden-sm hidden-xs">{{ ucfirst(\Calctool\Models\UserGroup::find($users->user_group)->name) }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<div class="row">
				<div class="col-md-12">
					<a href="/admin/user/new" class="btn btn-primary"><i class="fa fa-pencil"></i> Nieuwe gebruiker</a>
				</div>
			</div>
			</div>
		</div>

	</section>

</div>
@stop
