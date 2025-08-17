<!--begin::Footer-->
<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
						
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::Main-->
		<!-- begin::User Panel-->
		<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
			<!--begin::Header-->
			<div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
				<h3 class="font-weight-bold m-0">Profile</h3>
				<a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
					<i class="ki ki-close icon-xs text-muted"></i>
				</a>
			</div>
			<!--end::Header-->
			<!--begin::Content-->
			<div class="offcanvas-content pr-5 mr-n5">
				<!--begin::Header-->
				<div class="d-flex align-items-center mt-5">
					<div class="symbol symbol-100 mr-5">
						<div class="symbol-label" style="background-image:url('{{asset('public/user.jpg')}}')"></div>
						<i class="symbol-badge bg-success"></i>
					</div>
					<div class="d-flex flex-column">
						@php 
							$user = auth()->user();
						@endphp
						<a class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">{{ $user->name }}</a>
						<div class="text-muted mt-1">Last login : {{ $user->last_login }}</div>
						<div class="navi mt-2">
							<a href="javascript::;" class="navi-item">
								<span class="navi-link p-0 pb-2">
									<span class="navi-icon mr-1">
										<span class="svg-icon svg-icon-lg svg-icon-primary">
											<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
												<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
													<rect x="0" y="0" width="24" height="24" />
													<path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000" />
													<circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5" />
												</g>
											</svg>
											<!--end::Svg Icon-->
										</span>
									</span>
									<span class="navi-text text-muted text-hover-primary">{{ $user->email }}</span>
								</span>
							</a>
							<a href="{{ url('logout') }}" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5">Sign Out</a>
						</div>
					</div>
				</div>
				<!--end::Header-->
				<!--begin::Separator-->
				<div class="separator separator-dashed mt-8 mb-5"></div>
				<!--end::Separator-->
				
				
			</div>
			<!--end::Content-->
		</div>
		<!-- end::User Panel-->
		
		<!--begin::Scrolltop-->
		<div id="kt_scrolltop" class="scrolltop">
			<span class="svg-icon">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Up-2.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1" />
						<path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
		</div>
		<!--end::Scrolltop-->
		
		
		<script>var HOST_URL = "{{ url('/') }}";</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="{{asset('public/new_theme/assets/plugins/global/plugins.bundle.js')}}"></script>
        <script src="{{asset('public/new_theme/assets/plugins/custom/prismjs/prismjs.bundle.js')}}"></script>
        <script src="{{asset('public/new_theme/assets/js/scripts.bundle.js')}}"></script>
		<!--end::Global Theme Bundle-->
		<!--begin::Page Vendors(used by this page)-->
        <script src="{{asset('public/new_theme/assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
		<!--end::Page Vendors-->
		<!--begin::Page Scripts(used by this page)-->
		<script src="{{asset('public/new_theme/assets/js/pages/crud/datatables/basic/scrollable.js')}}"></script>

		<script src="{{asset('public/new_theme/sweetalert2.min.js')}}"></script>

		<script src="{{asset('public/new_theme/assets/js/pages/crud/forms/widgets/select2.js')}}"></script>
		<script src="{{asset('public/new_theme/toastr.min.js')}}"></script>
		<!--end::Page Scripts-->
		<script src="{{asset('public/new_theme/assets/js/chart.js')}}"></script>




		@include('templates.toastr')
		<script>

			

		$('#group_id').select2({
        	placeholder: "Select group/groups",
        });

		$('#view_group_id').select2({
        	placeholder: "Select group/groups",
        });

		$('#set_group_id').select2({
        	placeholder: "Select group/groups",
        });

		$('.multi-select').select2({
        	placeholder: "Select ",
        });

		$('#to_status_id').select2({
        	placeholder: "Select status/statuses",
        });
		$('#from_status_id').select2({
        	placeholder: "Select status/statuses",
        });
		
		
		$('#previous_status_id').select2({
        	placeholder: "Select status/statuses",
        });
		$('#status_id').select2({
        	placeholder: "Select group/groups",
        });
		$('._change_active').on('click', function(){//_approved_active
			var id = $(this).attr('data-id');
			Swal.fire({
				title: "Are you sure?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes"
				}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
                        url: '{{url("/")}}/{{ isset($OtherRoute)? $OtherRoute : "" }}/updateactive',
                        type: 'POST',
                        data: {'_method':'post','_token': $('meta[name="csrf-token"]').attr('content'),'id':id },
                        success: function( msg ) {
                            if ( msg.status === 'success' ) {
								toastr.success("Changed Successfully");
                              	window.location.reload();
                            }
                    },
					error: function(xhr, status, error) {
        toastr.error("Something went wrong: " + error);
        alert("Error: " + error + "\nStatus: " + status + "\nResponse: " + xhr.responseText);
        window.location.reload();
                    },
                  });
				}
			});
    	});

		$('._approved_active').on('click', function() {
    var id = $(this).attr('data-id');
    var token = $(this).attr('data-token');
	let baseUrl = '{{ url("/change_request/approved_active") }}';
let fullUrl = `${baseUrl}?crId=${id}&action=approve&token=${token}`;
    Swal.fire({
        title: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: fullUrl,
                type: 'GET',
                success: function(msg) {
                    if (msg.status === 200 && msg.isSuccess) {
                        toastr.success(msg.message);
                        
                    } else {
                        toastr.error(msg.message || "Action failed.");
                    }
					window.location.reload();
                },
                error: function(xhr, status, error) {
                    toastr.error("Something went wrong: " + error);
                    alert("Error: " + error + "\nStatus: " + status + "\nResponse: " + xhr.responseText);
                    window.location.reload();
                }
            });
        }
    });
});

$('._rejected_active').on('click', function() {
    var id = $(this).attr('data-id');
    var token = $(this).attr('data-token');
	let baseUrl = '{{ url("/change_request/approved_active") }}';
let fullUrl = `${baseUrl}?crId=${id}&action=reject&token=${token}`;
    Swal.fire({
        title: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: fullUrl,
                type: 'GET',
                success: function(msg) {
                    if (msg.status === 200 && !msg.isSuccess) {
                        toastr.success(msg.message);
                       
                    } else {
                        toastr.error(msg.message || "Action failed.");
                    }
					window.location.reload();
                },
                error: function(xhr, status, error) {
                    toastr.error("Something went wrong: " + error);
                    alert("Error: " + error + "\nStatus: " + status + "\nResponse: " + xhr.responseText);
                  //  window.location.reload();
                }
            });
        }
    });
});

		$(document).ready(function() {
			$('#dfUsageTable').DataTable({
				"searching": true,
				"paging":   true,
        		"ordering": false,
        		"info":     false,
				"pageLength": 50
			});
		});

		</script>

		<script>
		function stripTags(input) {
			return input.replace(/<\/?[^>]+(>|$)/g, "");
		}

		$(document).ready(function () {
			// Apply to all input and textarea
			$('input[type="text"], input[type="email"],textarea').on('input', function () {
			const cleanVal = stripTags($(this).val());
			$(this).val(cleanVal);
			});

			// Prevent HTML tags on paste
			$('input[type="text"], input[type="email"], textarea').on('paste', function (e) {
			e.preventDefault();
			const pastedText = (e.originalEvent || e).clipboardData.getData('text');
			const cleanText = stripTags(pastedText);
			const el = $(this);
			const currentVal = el.val();
			const caretPos = this.selectionStart;

			// Insert at cursor position
			const newVal = currentVal.substring(0, caretPos) + cleanText + currentVal.substring(caretPos);
			el.val(newVal);
			});
		});
		</script>

@auth
<script>
  
  const CHECK_INTERVAL = {{ config('app.check_interval') }};

    setInterval(() => {
      
		$.get( "{{ route('check-active') }}", function( data ) {
			console.log("check-active",data.active);
			if (data.active == false || data.active === "0" || data.active === 0){
                    //window.location.href = "{{ route('inactive-logout') }}";
			}
				
		});
	  
        
    }, CHECK_INTERVAL);
</script>
@endauth


		@stack('script')
	</body>
	<!--end::Body-->
</html>