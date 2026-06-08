@extends('admin.layout')
@section('content')
	<div class="mb-4 dash-banner-wrapper">
		<span>{{\Carbon\Carbon::now()->format("l M d, Y")}}</span>
		<h1 class="h3 mb-2"><strong>Welcome back, {{auth()->user()->name}}</strong></h1>
		<p>Your dashboard is synced, refreshed, and ready to help you lead with confidence — everything you need to plan, create, and move forward is right here.</p>
	</div>

	<div class="dash-video-wrapper mb-4">
		<div class="row">
			<div class="col-md-8">
				<div class="left-area h-100">
					@if(!empty(admin_settings('media_url')))
						@php
							$url = admin_settings('media_url');
							$embedUrl = '';
							$isIframe = false;

							// Handle YouTube
							if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
								$isIframe = true;
								$videoId = null;
								if (strpos($url, 'youtube.com/watch') !== false) {
									parse_str(parse_url($url, PHP_URL_QUERY), $params);
									$videoId = $params['v'] ?? null;
								} elseif (strpos($url, 'youtu.be') !== false) {
									$videoId = trim(parse_url($url, PHP_URL_PATH), '/');
								}
								if ($videoId) {
									$embedUrl = "https://www.youtube.com/embed/{$videoId}";
								}
							} 
							// Handle Vimeo
							elseif (strpos($url, 'vimeo.com') !== false) {
								$isIframe = true;
								$vimeoId = preg_replace('/[^0-9]/', '', parse_url($url, PHP_URL_PATH));
								if ($vimeoId) {
									$embedUrl = "https://player.vimeo.com/video/{$vimeoId}";
								}
							}
						@endphp

						<div class="video-container w-100 dom-video-wrap">
							@if($isIframe && !empty($embedUrl))
								<div class="ratio ratio-16x9 dom-video-wrap">
									<iframe src="{{ $embedUrl }}" title="Video" allowfullscreen style="border-radius: 15px"></iframe>
								</div>
							@else
								<video controls class="w-100 h-100" style="border-radius: 17px;">
									<source src="{{ $url }}" type="video/mp4">
									Your browser does not support the video tag.
								</video>
							@endif
						</div>
					@endif
				</div>
			</div>
			<div class="col-md-4">
				@if(count($checklists) > 0)
					<div class="right-area h-100">
						<h2>Things You May Have Missed</h2>
						<div class="check-label-wrap" id="participant-checklist">						
							@foreach($checklists as $checklist)
								@php
									$userChecklist = \Illuminate\Support\Facades\DB::table('checklist_user')
										->where('checklist_id', $checklist->id)
										->where('user_id', auth()->user()->id)
										->first();
									$isCompleted = $userChecklist && $userChecklist->is_completed;
								@endphp
								<div class="checklist-item" data-id="{{ $checklist->id }}" style="opacity: {{$isCompleted ? '0.5' : '1'}}">
									@if($checklist->link)
										<div class="check-label">
											<input type="checkbox" 
												class="checklist-checkbox" 
												data-id="{{ $checklist->id }}"
												data-url="{{ route('checklist.complete', $checklist) }}"
												data-incomplete-url="{{ route('checklist.incomplete', $checklist) }}"
												{{ $isCompleted ? 'checked' : '' }}>

											<a href="{{ $checklist->link }}" target="_blank">
												<label>{{ $checklist->title }}</label>
											</a>
										</div>
									@endif
								</div>
							
							@endforeach
						</div>
					
						<div class="action-area d-flex justify-content-end">
							<a href="#" onclick="location.href='{{ route('se.checklists') }}'">Show All</a>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>

	<h1 class="h3 mb-3"><strong>Dashboard</strong> Analytics</h1>

	
	<div class="row">
		@foreach($cards as $card)
			<div class="col-xl-4 col-sm-4 mb-3">
				<div class="card h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between">
							<h5 class="card-title mb-0">{{ $card['title'] }}</h5>
							<div class="stat text-primary">
								<i class="align-middle" data-feather="{{ $card['icon'] }}"></i>
							</div>
						</div>

						@if($card['title'] == 'Upcoming Session' && $card['value'])
							<div class="mb-3">
								<span><strong>Class Name:</strong> {{$card['value']->mainclass->name}}</span><br>
								<span><strong>Date:</strong> {{$card['value']->schedule_date}}</span><br>
								<span><strong>Time:</strong> {{\Carbon\Carbon::parse($card['value']->schedule_time)->format('h:i A')}}</span><br>
							</div>
							@if($card['value']->attendances->isNotEmpty())
								<a href="{{ $card['value']->session->getZoomMeetingLinkForSession($card['value']->session->id) ?? '-' }}" target="_blank"><i class="align-middle me-1" data-feather="external-link"></i>Join Zoom Meeting</a>
							@else
								<button 
									class="dom-primary-btn clock-in-btn"
									data-id="{{ $card['value']->id }}"
									data-date="{{ $card['value']->schedule_date }}"
									data-time="{{ $card['value']->schedule_time }}"
									data-url="{{ route('attendance.clockin') }}"
								><i class="align-middle me-1" data-feather="clock"></i> Clock In
								</button>
							@endif 
						@else
							<h1 class="mt-3 mb-0">{{ $card['value'] }}</h1>
						@endif
					</div>
				</div>
			</div>
		@endforeach
	</div>

	<div class="row">
		<div class="col-xl-6">
			<div class="card flex-fill w-100">
				<div class="card-header">

					<h5 class="card-title mb-0">Last 6 weeks Attendance</h5>
				</div>
				<div class="card-body d-flex w-100">
					<div class="align-self-center chart chart-lg">
						<canvas id="chartjs-dashboard-line"></canvas>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-6">
			<div class="card flex-fill w-100">
				<div class="card-header">

					<h5 class="card-title mb-0">Last 3 Months of Weekly Stipend</h5>
				</div>
				<div class="card-body d-flex w-100">
					<div class="align-self-center chart chart-lg">
						<canvas id="chartjs-dashboard-bar"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>



		<script>
		document.addEventListener("DOMContentLoaded", function() {
			var labels = @json($labels);
    		var data   = @json($data);
			var ctx = document.getElementById("chartjs-dashboard-line").getContext("2d");
			var gradient = ctx.createLinearGradient(0, 0, 0, 225);
			gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
			gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
			// Line chart
			new Chart(document.getElementById("chartjs-dashboard-line"), {
				type: "line",
				data: {
					labels: labels,
					datasets: [{
						label: "Last 6 Weeks Attendance",
						fill: true,
						backgroundColor: gradient,
						borderColor: window.theme.primary,
						data: data
					}]
				},
				options: {
					maintainAspectRatio: false,
					legend: {
						display: false
					},
					tooltips: {
						intersect: false
					},
					hover: {
						intersect: true
					},
					plugins: {
						filler: {
							propagate: false
						}
					},
					scales: {
						xAxes: [{
							reverse: true,
							gridLines: {
								color: "rgba(0,0,0,0.0)"
							}
						}],
						yAxes: [{
							ticks: {
								stepSize: 1000
							},
							display: true,
							borderDash: [3, 3],
							gridLines: {
								color: "rgba(0,0,0,0.0)"
							}
						}]
					}
				}
			});
		});
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var labels = @json($stipendlabels);
    		var data   = @json($stipenddata);
			// Bar chart
			new Chart(document.getElementById("chartjs-dashboard-bar"), {
				type: "bar",
				data: {
					labels: labels,
					datasets: [{
						label: "This year",
						backgroundColor: window.theme.primary,
						borderColor: window.theme.primary,
						hoverBackgroundColor: window.theme.primary,
						hoverBorderColor: window.theme.primary,
						data: data,
						barPercentage: .75,
						categoryPercentage: .5
					}]
				},
				options: {
					maintainAspectRatio: false,
					legend: {
						display: false
					},
					scales: {
						yAxes: [{
							gridLines: {
								display: false
							},
							stacked: false,
							ticks: {
								stepSize: 20
							}
						}],
						xAxes: [{
							stacked: false,
							gridLines: {
								color: "transparent"
							}
						}]
					}
				}
			});
		});
	</script>

	{{-- <script>
		document.addEventListener('DOMContentLoaded', function() {
			const checkboxes = document.querySelectorAll('.checklist-checkbox');
			
			checkboxes.forEach(checkbox => {
				checkbox.addEventListener('change', function(e) {
					const checklistId = this.dataset.id;
					const isChecked = this.checked;
					const url = isChecked ? this.dataset.url : this.dataset.incompleteUrl;
					
					fetch(url, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': '{{ csrf_token() }}'
						},
						body: JSON.stringify({})
					})
					.then(response => response.json())
					.then(data => {
						if (!data.success) {
							checkbox.checked = !isChecked;
							alert(data.message || 'An error occurred');
						} else {
							// Show success feedback
							const item = checkbox.closest('.checklist-item');
							if (isChecked) {
								item.style.opacity = '0.5';
							} else {
								item.style.opacity = '1';
							}
						}
					})
					.catch(error => {
						console.error('Error:', error);
						checkbox.checked = !isChecked;
						alert('An error occurred. Please try again.');
					});
				});
			});
		});
	</script> --}}
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.checklist-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            const checklistItem = this.closest('.checklist-item');
            const checklistId = this.dataset.id;
            const isChecked = this.checked;
            const url = isChecked ? this.dataset.url : this.dataset.incompleteUrl;
            const taskTitle = this.closest('.check-label')?.querySelector('label')?.innerText || 'Task';
            
            // Show loading state
            Swal.fire({
                title: isChecked ? 'Completing task...' : 'Updating task...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isChecked) {
                        // Success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Task Completed! 🎉',
                            text: `${taskTitle} - Great job! Keep up the good work.`,
                            timer: 2000,
                            showConfirmButton: false,
                            willClose: () => {
                                // Remove the item with animation
                                checklistItem.style.transition = 'all 0.3s ease';
                                checklistItem.style.opacity = '0';
                                checklistItem.style.transform = 'translateX(20px)';
                                
                                setTimeout(() => {
                                    checklistItem.remove();
                                    
                                    // Check if no more items
                                    const remainingItems = document.querySelectorAll('.checklist-item');
                                    if (remainingItems.length === 0) {
                                        const checkLabelWrap = document.querySelector('.check-label-wrap');
                                        if (checkLabelWrap) {
                                            checkLabelWrap.innerHTML = `
                                                <div class="text-center py-5">
                                                    <i class="align-middle mb-3" data-feather="check-circle" style="width: 64px; height: 64px; color: #28a745;"></i>
                                                    <h5>All caught up! 🎉</h5>
                                                    <p class="text-muted">You have completed all your tasks.</p>
                                                </div>
                                            `;
                                            if (typeof feather !== 'undefined') feather.replace();
                                        }
                                    }
                                }, 300);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Task Reopened',
                            text: `${taskTitle} - You can complete it again when ready.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    this.checked = !isChecked;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !isChecked;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network error. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    });
});
</script>

@endsection
