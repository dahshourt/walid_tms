
<!DOCTYPE html>
<!--
Template Name: Metronic - Bootstrap 4 HTML, React, Angular 11 & VueJS Admin Dashboard Theme
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: https://1.envato.market/EA4JP
Renew Support: https://1.envato.market/EA4JP
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head><base href="../../../">
		<meta charset="utf-8" />
		<title>TMS</title>

		<meta name="description" content="Scrollable datatables examples" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="canonical" href="https://keenthemes.com/metronic" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<!--begin::Fonts-->
		<link rel="stylesheet" href="{{asset('public/new_theme/font.css')}}" />
		<!--end::Fonts-->
        <link href="{{asset('public/new_theme/assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Fonts-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="{{asset('public/new_theme/assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('public/new_theme/assets/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('public/new_theme/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('public/new_theme/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
		<link src="{{asset('public/new_theme/assets/css/charts.css')}}" rel="stylesheet" type="text/css"></link>

		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<!--end::Layout Themes-->


		<link rel="shortcut icon" href="{{asset('public/logo-icon.png')}}" />
		<style>
			.swal2-icon-show
			{
				left:40%;
			}
			.form-control-feedback{
				color:red;
			}
			._change_active
			{
				cursor: pointer;
			}
			.local_password_div
			{
				display:none;
			}
			.same_class{
				display : none;
			}
			.select2-container
			{
				width:100% !important;
			}
			.header-menu .menu-nav > .menu-item > .menu-link .menu-text
			{
				font-size: 0.9rem !important;
			}
			.hint
			{
				color:blue;
			}
			.change-request-form-field
			{
				margin-bottom: 1.75rem;
			}


/* === Notification Wrapper (position control) === */
.notification-wrapper {
    position: absolute;
    top: -5px;
    right: -12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* === Red bubble === */
.notification-badge {
    background-color: #ff3b30;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 7px;
    border-radius: 50%;
    box-shadow: 0 0 8px rgba(255, 59, 48, 0.4);
    z-index: 2;
    transition: transform 0.2s ease, box-shadow 0.3s ease;
}

/* === Subtle hover bounce === */
.menu-link:hover .notification-badge {
    transform: scale(1.15);
    box-shadow: 0 0 12px rgba(255, 59, 48, 0.6);
}

/* === Soft animated pulse ring (like Facebook) === */
.notification-pulse {
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(255, 59, 48, 0.5);
    animation: pulseRing 1.5s infinite;
    z-index: 1;
}

@keyframes pulseRing {
    0% {
        transform: scale(0.8);
        opacity: 0.8;
    }
    70% {
        transform: scale(1.6);
        opacity: 0;
    }
    100% {
        transform: scale(0.8);
        opacity: 0;
    }
}

		</style>

        @stack('css')

	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" style="background-image: url({{asset('public/new_theme/assets/media/bg/bg-6.jpg')}})"  class="quick-panel-right demo-panel-right offcanvas-right header-fixed subheader-enabled page-loading">
