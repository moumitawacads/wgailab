@extends('admin.layout')

@section('title', 'Dashboard Analytics')

@section('content')



<h1 class="h3 mb-3"><strong>Dashboard</strong> Analytics</h1>

<div class="row">
	@foreach($cards as $card)
		<div class="{{count($cards) == 2 ? 'col-xl-6' : 'col-xl-3'}} col-sm-6 mb-3">
			<div class="card h-100">
				<div class="card-body dash-card">
					<div class="d-flex justify-content-between">
						<h5 class="card-title mb-0">{{ $card['title'] }}</h5>
						<div class="stat bg-green">
							<i class="align-middle" data-feather="{{ $card['icon'] }}"></i>
						</div>
					</div>
					@php
						$url = null;
						if($card['title'] == 'Street Entrepreneurs') {
							$url = route('admin.users', ['role' => 'se']);
						} else if($card['title'] == 'Instructors') {
							$url = route('admin.users', ['role' => 'instructor']);
						} else if($card['title'] == 'Current Week Class Count') {
							$url = route('admin.schedule_log', ['from_date' => \Carbon\Carbon::parse($startOfWeek)->format('Y-m-d'), 'to_date' => \Carbon\Carbon::parse($endOfWeek)->format('Y-m-d')]);
						} else if($card['title'] == 'Upcoming Week Class Count') {
							$url = route('admin.schedule_log', ['from_date' => \Carbon\Carbon::parse($startOfNextWeek)->format('Y-m-d'), 'to_date' => \Carbon\Carbon::parse($endOfNextWeek)->format('Y-m-d')]);
						}
					@endphp
					<a href="{{$url}}"><h1 class="mt-3 mb-0">{{ $card['value'] }}</h1></a>
				</div>
			</div>
		</div>
	@endforeach
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Attendance Report</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="me-2" id="dateRangeDisplay">
                            {{ $dateRange['start_label'] ?? '' }} - {{ $dateRange['end_label'] ?? '' }}
                        </span>
                        <select id="attendancePeriod" class="form-control form-control-sm form-select" style="width: 170px;">
                            <option value="this_week" {{ request('period', 'this_week') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="last_week" {{ request('period') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                            <option value="two_weeks" {{ request('period') == 'two_weeks' ? 'selected' : '' }}>Last 2 Weeks</option>
                            <option value="six_weeks" {{ request('period') == 'six_weeks' ? 'selected' : '' }}>Last 6 Weeks</option>
                            <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                        <a href="{{route('admin.dashboard')}}" class="back-btn p-3">
                            <i class="align-middle" data-feather="refresh-cw"></i> Reset
                        </a>
                    </div>
                </div>
                <!-- Custom date range inputs (hidden by default) -->
                <div id="customDateRange" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }}; margin-top: 10px;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="date" id="customStartDate" class="form-control form-control-sm" 
                                   value="{{ request('start_date') }}" placeholder="Start Date">
                        </div>
                        <div class="col-md-4">
                            <input type="date" id="customEndDate" class="form-control form-control-sm" 
                                   value="{{ request('end_date') }}" placeholder="End Date">
                        </div>
                        <div class="col-md-4">
                            <button class="dom-primary-btn" onclick="updateAttendanceReport()">
                                <i class="align-middle" data-feather="check"></i> Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="attendanceBoxes">
                    @foreach($attendanceBoxes ?? [] as $box)
                        <div class="col-xl-3 col-sm-6 mb-3">
                            <div class="card h-100 border-{{ $box['color'] }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-1 wi-105">{{ $box['title'] }}</h6>
                                            <h2 class="mt-2 mb-0">{{ $box['count'] }}</h2>
                                            <small class="text-muted">SEs</small>
                                        </div>
                                        <div class="stat bg-{{ $box['color'] }} ar-icon">
                                            <i class="align-middle" data-feather="{{ $box['icon'] }}"></i>
                                        </div>
                                    </div>
                                    @if($box['count'] > 0)
                                        <a href="{{ request('period') == 'custom' ? route('admin.attendance.details', ['category' => $box['category'], 'period' => request('period', 'this_week'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) : route('admin.attendance.details', ['category' => $box['category'], 'period' => request('period', 'this_week')]) }}" 
                                           class="btn btn-sm btn-outline-{{ $box['color'] }} mt-2">
                                            View Details <i class="align-middle" data-feather="arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Homework Completion Report</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="me-2" id="domeworkDateRangeDisplay">
                            {{ $domeworkDateRange['start_label'] ?? '' }} - {{ $domeworkDateRange['end_label'] ?? '' }}
                        </span>
                        <select id="domeworkPeriod" class="form-control form-control-sm" style="width: auto;">
                            <option value="this_week" {{ request('period', 'this_week') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="last_week" {{ request('period') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                            <option value="two_weeks" {{ request('period') == 'two_weeks' ? 'selected' : '' }}>Last 2 Weeks</option>
                            <option value="six_weeks" {{ request('period') == 'six_weeks' ? 'selected' : '' }}>Last 6 Weeks</option>
                            <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
						<a href="{{route('admin.dashboard')}}" class="back-btn p-3">
                            <i class="align-middle" data-feather="refresh-cw"></i> Reset
                        </a>
                    </div>
                </div>
                <!-- Custom date range inputs (hidden by default) -->
                <div id="domeworkCustomDateRange" style="display: {{ request('domework_period') == 'custom' ? 'block' : 'none' }}; margin-top: 10px;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="date" id="domeworkCustomStartDate" class="form-control form-control-sm" 
                                   value="{{ request('domework_start_date') }}" placeholder="Start Date">
                        </div>
                        <div class="col-md-4">
                            <input type="date" id="domeworkCustomEndDate" class="form-control form-control-sm" 
                                   value="{{ request('domework_end_date') }}" placeholder="End Date">
                        </div>
                        <div class="col-md-4">
                            <button class="dom-primary-btn" onclick="updateDomeworkReport()">
                                <i class="align-middle" data-feather="check"></i> Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="domeworkBoxes">
                    @foreach($domeworkBoxes ?? [] as $box)
                        <div class="col-xl-3 col-sm-6 mb-3">
                            <div class="card h-100 border-{{ $box['color'] }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title mb-1 wi-105">{{ $box['title'] }}</h6>
                                            <h2 class="mt-2 mb-0">{{ $box['count'] }}</h2>
                                            <small class="text-muted">SEs</small>
                                        </div>
                                        <div class="stat bg-{{ $box['color'] }} ar-icon">
                                            <i class="align-middle" data-feather="{{ $box['icon'] }}"></i>
                                        </div>
                                    </div>
                                    @if($box['count'] > 0)
										<a href="{{ route('admin.domework.details', [
											'category' => $box['category'], 
											'period' => request('domework_period', 'this_week'),
											'start_date' => request('domework_start_date'),
											'end_date' => request('domework_end_date')
										]) }}" 
										class="btn btn-sm btn-outline-{{ $box['color'] }} mt-2">
											View Details <i class="align-middle" data-feather="arrow-right"></i>
										</a>
									@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
	<div class="col-xl-6">
		<div class="card flex-fill w-100">
			<div class="card-header">

				<h5 class="card-title mb-0">Last 6 weeks Attendances</h5>
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

				<h5 class="card-title mb-0">Last 3 Months Weekly Stipend Record</h5>
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


		document.addEventListener("DOMContentLoaded", function() {
			// Handle period dropdown change
			document.getElementById('attendancePeriod').addEventListener('change', function() {
				if (this.value === 'custom') {
					document.getElementById('customDateRange').style.display = 'block';
					// Update URL with custom parameter
					updateUrlParameter('period', 'custom');
				} else {
					document.getElementById('customDateRange').style.display = 'none';
					updateAttendanceReport();
					// Update URL with selected period
					updateUrlParameter('period', this.value);
				}
			});

			// Handle Enter key on custom date inputs
			document.getElementById('customStartDate').addEventListener('keypress', function(e) {
				if (e.key === 'Enter') {
					updateAttendanceReport();
				}
			});
			document.getElementById('customEndDate').addEventListener('keypress', function(e) {
				if (e.key === 'Enter') {
					updateAttendanceReport();
				}
			});

			// Load saved state from URL parameters
			const urlParams = new URLSearchParams(window.location.search);
			const period = urlParams.get('period');
			if (period) {
				document.getElementById('attendancePeriod').value = period;
				if (period === 'custom') {
					document.getElementById('customDateRange').style.display = 'block';
					const startDate = urlParams.get('start_date');
					const endDate = urlParams.get('end_date');
					if (startDate) document.getElementById('customStartDate').value = startDate;
					if (endDate) document.getElementById('customEndDate').value = endDate;
				}
			}
		});

		function updateAttendanceReport() {
			const period = document.getElementById('attendancePeriod').value;
			let url = '{{ route("admin.attendance.stats") }}?period=' + period;
			
			if (period === 'custom') {
				const startDate = document.getElementById('customStartDate').value;
				const endDate = document.getElementById('customEndDate').value;
				if (!startDate || !endDate) {
					alert('Please select both start and end dates');
					return;
				}
				url += '&start_date=' + startDate + '&end_date=' + endDate;
				// Update URL parameters
				updateUrlParameter('start_date', startDate);
				updateUrlParameter('end_date', endDate);
			} else {
				// Remove custom date parameters from URL
				removeUrlParameter('start_date');
				removeUrlParameter('end_date');
			}
			
			// Update URL with period
			updateUrlParameter('period', period);
			
			// Show loading state
			document.getElementById('attendanceBoxes').innerHTML = 
				'<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
			
			fetch(url)
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					// Update date range display
					if (data.date_range) {
						document.getElementById('dateRangeDisplay').textContent = 
							data.date_range.start_label + ' - ' + data.date_range.end_label;
					}
					
					let html = '';
					data.boxes.forEach(box => {
						const detailsUrl = period == 'custom' ? '{{ route("admin.attendance.details") }}?category=' + box.category + '&period=' + period + '&start_date=' + document.getElementById('customStartDate').value + '&end_date=' + document.getElementById('customEndDate').value : '{{ route("admin.attendance.details") }}?category=' + box.category + '&period=' + period;
						html += `
							<div class="col-xl-3 col-sm-6 mb-3">
								<div class="card h-100 border-${box.color}">
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-start">
											<div>
												<h6 class="card-title mb-1">${box.title}</h6>
												<h2 class="mt-2 mb-0">${box.count}</h2>
												<small class="text-muted">SEs</small>
											</div>
											<div class="stat bg-${box.color} ar-icon">
												<i class="align-middle" data-feather="${box.icon}"></i>
											</div>
										</div>
										${box.count > 0 ? `
											<a href="${detailsUrl}" class="btn btn-sm btn-outline-${box.color} mt-2">
												View Details <i class="align-middle" data-feather="arrow-right"></i>
											</a>
										` : ''}
									</div>
								</div>
							</div>
						`;
					});
					document.getElementById('attendanceBoxes').innerHTML = html;
					
					// Reinitialize Feather icons
					if (typeof feather !== 'undefined') {
						feather.replace();
					}
				})
				.catch(error => {
					console.error('Error:', error);
					document.getElementById('attendanceBoxes').innerHTML = 
						'<div class="col-12 text-center text-danger py-5">Failed to load attendance data. Please try again.</div>';
				});
		}

		// Helper function to update URL parameters without page reload
		function updateUrlParameter(key, value) {
			const url = new URL(window.location.href);
			url.searchParams.set(key, value);
			window.history.replaceState({}, '', url.toString());
		}

		// Helper function to remove URL parameters
		function removeUrlParameter(key) {
			const url = new URL(window.location.href);
			url.searchParams.delete(key);
			window.history.replaceState({}, '', url.toString());
		}



		document.addEventListener("DOMContentLoaded", function() {
			// Handle period dropdown change for domework
			const domeworkPeriodSelect = document.getElementById('domeworkPeriod');
			if (domeworkPeriodSelect) {
				domeworkPeriodSelect.addEventListener('change', function() {
					if (this.value === 'custom') {
						document.getElementById('domeworkCustomDateRange').style.display = 'block';
						updateUrlParameter('domework_period', 'custom');
					} else {
						document.getElementById('domeworkCustomDateRange').style.display = 'none';
						updateDomeworkReport();
						updateUrlParameter('domework_period', this.value);
					}
				});
			}

			// Handle Enter key on custom date inputs for domework
			const customStartDate = document.getElementById('domeworkCustomStartDate');
			const customEndDate = document.getElementById('domeworkCustomEndDate');
			if (customStartDate) {
				customStartDate.addEventListener('keypress', function(e) {
					if (e.key === 'Enter') {
						updateDomeworkReport();
					}
				});
			}
			if (customEndDate) {
				customEndDate.addEventListener('keypress', function(e) {
					if (e.key === 'Enter') {
						updateDomeworkReport();
					}
				});
			}

			// Load saved state from URL parameters for domework
			const urlParams = new URLSearchParams(window.location.search);
			const domeworkPeriod = urlParams.get('domework_period');
			if (domeworkPeriod && domeworkPeriodSelect) {
				domeworkPeriodSelect.value = domeworkPeriod;
				if (domeworkPeriod === 'custom') {
					document.getElementById('domeworkCustomDateRange').style.display = 'block';
					const startDate = urlParams.get('domework_start_date');
					const endDate = urlParams.get('domework_end_date');
					if (startDate && customStartDate) customStartDate.value = startDate;
					if (endDate && customEndDate) customEndDate.value = endDate;
				}
			}
		});

		function updateDomeworkReport() {
			const period = document.getElementById('domeworkPeriod').value;
			let url = '{{ route("admin.domework.stats") }}?period=' + period;
			let detailsBaseUrl = '{{ route("admin.domework.details") }}';
			
			if (period === 'custom') {
				const startDate = document.getElementById('domeworkCustomStartDate').value;
				const endDate = document.getElementById('domeworkCustomEndDate').value;
				if (!startDate || !endDate) {
					alert('Please select both start and end dates');
					return;
				}
				url += '&start_date=' + startDate + '&end_date=' + endDate;
				updateUrlParameter('domework_start_date', startDate);
				updateUrlParameter('domework_end_date', endDate);
				detailsBaseUrl += '?period=' + period + '&start_date=' + startDate + '&end_date=' + endDate;
			} else {
				removeUrlParameter('domework_start_date');
				removeUrlParameter('domework_end_date');
				detailsBaseUrl += '?period=' + period;
			}
			
			updateUrlParameter('domework_period', period);
			
			// Show loading state
			document.getElementById('domeworkBoxes').innerHTML = 
				'<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
			
			fetch(url)
				.then(response => {
					if (!response.ok) {
						throw new Error('Network response was not ok');
					}
					return response.json();
				})
				.then(data => {
					// Update date range display
					if (data.date_range) {
						document.getElementById('domeworkDateRangeDisplay').textContent = 
							data.date_range.start_label + ' - ' + data.date_range.end_label;
					}
					
					let html = '';
					data.boxes.forEach(box => {
						// Build the details URL with proper parameters
						let detailsUrl = detailsBaseUrl + '&category=' + box.category;
						
						html += `
							<div class="col-xl-3 col-sm-6 mb-3">
								<div class="card h-100 border-${box.color}">
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-start">
											<div>
												<h6 class="card-title mb-1">${box.title}</h6>
												<h2 class="mt-2 mb-0">${box.count}</h2>
												<small class="text-muted">SEs</small>
											</div>
											<div class="stat bg-${box.color} ar-icon">
												<i class="align-middle" data-feather="${box.icon}"></i>
											</div>
										</div>
										${box.count > 0 ? `
											<a href="${detailsUrl}" class="btn btn-sm btn-outline-${box.color} mt-2">
												View Details <i class="align-middle" data-feather="arrow-right"></i>
											</a>
										` : ''}
									</div>
								</div>
							</div>
						`;
					});
					document.getElementById('domeworkBoxes').innerHTML = html;
					
					// Reinitialize Feather icons
					if (typeof feather !== 'undefined') {
						feather.replace();
					}
				})
				.catch(error => {
					console.error('Error:', error);
					document.getElementById('domeworkBoxes').innerHTML = 
						'<div class="col-12 text-center text-danger py-5">Failed to load homework data. Please try again.</div>';
				});
		}
	</script>

@endsection