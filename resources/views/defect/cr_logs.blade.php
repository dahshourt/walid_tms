
<div class="modal" id = "modal">
										<!--begin::Content-->
										<div class="content d-flex flex-column flex-column-fluid  " id="kt_content ">
											<!--begin::Subheader-->
											<div class="subheader py-2 py-lg-12 subheader-transparent " id="kt_subheader">
												<div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
													<!--begin::Info-->
													<div class="d-flex align-items-center flex-wrap mr-1">
														<!--begin::Heading-->
														<div class="d-flex flex-column">
															<!--begin::Title-->
															<h2 class="text-white font-weight-bold my-2 mr-5">Log Ticket </h2>
															<!--end::Title-->
														</div>
														<!--end::Heading-->
													</div>
													<!--end::Info-->
												</div>
											</div>
											<!--end::Subheader-->
											<!--begin::Entry-->
											<div class="d-flex flex-column-fluid">
												<!--begin::Container-->
												<div class="container">
													<div class="row">
														<div class="col-md-12">
															<div class="card card-custom gutter-b ">
																<div class="card-body ">
																	<span id="close_logs" class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo8/dist/../src/media/svg/icons/Navigation/Close.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																		    <title>Stockholm-icons / Navigation / Close</title>
																		    <desc>Created with Sketch.</desc>
																		    <defs/>
																		    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		        <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
																		            <rect x="0" y="7" width="16" height="2" rx="1"/>
																		            <rect opacity="0.3" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000) " x="0" y="7" width="16" height="2" rx="1"/>
																		        </g>
																		    </g>
																		</svg><!--end::Svg Icon--></span>
																																			<!--begin::Example-->
																	<div class="example example-basic">
																		<div class="example-preview ">
																			<!--begin::Timeline-->
																			<div class="timeline timeline-5">
																				<div class="timeline-items">
																					<!--begin::Item-->
																					@foreach($logs as $key => $log)
																					<div class="timeline-item">
																						<!--begin::Icon-->
																						@if($key % 2 == 0)
																						<div class="timeline-media bg-light-primary">
																							<span class="svg-icon svg-icon-primary svg-icon-md">
																								<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group-chat.svg-->
																								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																										<rect x="0" y="0" width="24" height="24" />
																										<path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000" />
																										<path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3" />
																									</g>
																								</svg>
																								<!--end::Svg Icon-->
																							</span>
																						</div>
																						<!--end::Icon-->
																						@else
																							<!--begin::Icon-->
																						<div class="timeline-media bg-light-danger">
																							<span class="svg-icon svg-icon-danger svg-icon-md">
																								<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Add-user.svg-->
																								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																										<polygon points="0 0 24 0 24 24 0 24" />
																										<path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																										<path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
																									</g>
																								</svg>
																								<!--end::Svg Icon-->
																							</span>
																						</div>
																						<!--end::Icon-->
																						@endif	
																						<!--begin::Info-->
																						<div class="timeline-desc timeline-desc-light-primary">
																							<span class="font-weight-bolder text-primary">{{$log->user->user_name}}({{$log->user->defualt_group->title}}) <br />
																							{{$log->created_at->format('d-m-y ,g:i A')}}</span>
																							<p class="font-weight-normal text-dark-50 pb-2">{{$log->log_text}}</p>
																						</div>
																						<!--end::Info-->
																					</div>
																					<!--end::Item-->
																					
																					@endforeach
																				</div>
																			</div>
																	<!--end::Example-->
																	
																	<!--end::Code example-->
																		</div>
																	</div>
					                                        
																</div>
															</div>
														</div>
												<!--end::Container-->
													</div>
											<!--end::Entry-->
												</div>
										<!--end::Logs Content-->
											</div>
									</div>