<!DOCTYPE html>
<!--[if IE 8]><html class="ie ie8"><![endif]-->
<!--[if IE 9]><html class="ie ie9"><![endif]-->
<html>
	<head>
		<meta charset="utf-8" />
		<title>Calculatie Tool - Online calculeren & offreren</title>
		<meta name="keywords" content="Calculeren" />
		<meta name="description" content="" />
		<meta name="Author" content="CalcTool.nl" />

		<?# -- favicon -- ?>
		<link rel="apple-touch-icon" sizes="57x57" href="/images/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/images/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/images/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/images/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/images/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/images/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/images/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/images/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="/images/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/images/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="/images/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="/images/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/images/mstile-144x144.png">
		<meta name="theme-color" content="#4e4e4e">

		<?# -- mobile settings -- ?>
		<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />

		<?# -- WEB FONTS -- ?>
		<link media="all" type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800">

		<?# -- CORE CSS -- ?>
		<link media="all" type="text/css" rel="stylesheet" href="/plugins/bootstrap/css/bootstrap.min.css">
		<link media="all" type="text/css" rel="stylesheet" href="/css/font-awesome.css">
		<link media="all" type="text/css" rel="stylesheet" href="/plugins/owl-carousel/owl.carousel.css">
		<link media="all" type="text/css" rel="stylesheet" href="/plugins/owl-carousel/owl.theme.css">
		<link media="all" type="text/css" rel="stylesheet" href="/plugins/owl-carousel/owl.transitions.css">
		<link media="all" type="text/css" rel="stylesheet" href="/plugins/x-editable/css/bootstrap-editable.css">
		<link media="all" type="text/css" rel="stylesheet" href="/plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css">

		<?# -- SHOP CSS -- ?>
		<link media="all" type="text/css" rel="stylesheet" href="/css/shop.css">

		<?#-- THEME CSS -- ?>
		<link media="all" type="text/css" rel="stylesheet" href="/css/essentials.css">
		<link media="all" type="text/css" rel="stylesheet" href="/css/layout.css">
		<link media="all" type="text/css" rel="stylesheet" href="/css/layout-responsive.css">
		<link media="all" type="text/css" rel="stylesheet" href="/css/darkgreen.css">

		<?# -- CUSTOM CSS -- ?>
		<link media="all" type="text/css" rel="stylesheet" href="/css/custom.css">

		<?# -- Morenizr -- ?>
		<script src="/plugins/modernizr.min.js"></script>

		<?# -- JQuery -- ?>
		<script src="/plugins/jquery-2.1.4.min.js"></script>
	</head>
	<body>
		<?# -- ONLY DEV -- ?>
		@if(App::environment('dev'))
		<div style="background-color:red;z-index:200;position:fixed;top:0px;left:45%;width: 100px;text-align: center;"><a href="https://bitbucket.org/calctool/calctool-v2/commits/{{ File::get('../.revision') }}" style="color: black;">{{ 'REV: ' . substr(File::get('../.revision'), 0, 7) }}</a></div>
		@elseif(App::environment('local'))
		<div style="background-color:green;z-index:200;position:fixed;top:0px;left:45%;width: 100px;text-align: center;">local</div>
		@endif

		<?# -- HEADER -- ?>
		@section('header')
			@include('layout.header')
		@show

		<?# -- MAIN CONTENT -- ?>
		@yield('content')


		<?# -- FOOTER -- ?>
		@section('footer')
			@include('layout.footer')
		@show

		<?# -- JAVASCRIPT FILES -- ?>
		<script src="/plugins/jquery.easing.1.3.js"></script>
		<script src="/plugins/jquery.cookie.js"></script>
		<script src="/plugins/jquery.appear.js"></script>
		<script src="/plugins/jquery.isotope.js"></script>
		<script src="/plugins/jquery.number.min.js"></script>
		<script src="/plugins/masonry.js"></script>

		<script src="/plugins/bootstrap/js/bootstrap.min.js"></script>
		<script src="/plugins/owl-carousel/owl.carousel.min.js"></script>
		<script src="/plugins/x-editable/js/bootstrap-editable.min.js"></script>
		<script src="/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

		<script src="/js/scripts.js"></script>
	</body>
</html>
