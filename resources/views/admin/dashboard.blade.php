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
	</script>

@endsection