@extends('admin.layout')
@section('content')


	<h1 class="h3 mb-3"><strong>Dashboard</strong> Analytics</h1>

	<div class="dash-video-wrapper mb-4">
		<div class="row">
			<div class="col-md-12">
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
		</div>
	</div>

	
	<div class="row">
		@foreach($cards as $card)
			<div class="col-xl-6 col-sm-6 mb-3">
				<div class="card h-100">
					<div class="card-body dash-card">
						<div class="d-flex justify-content-between">
							<h5 class="card-title mb-0">{{ $card['title'] }}</h5>
							<div class="stat text-primary">
								<i class="align-middle" data-feather="{{ $card['icon'] }}"></i>
							</div>
						</div>

						@php
							$url = null;
							if($card['title'] == 'Current Week Class Count') {
								$url = route('instructor.schedule_log', ['from_date' => \Carbon\Carbon::parse($startOfWeek)->format('Y-m-d'), 'to_date' => \Carbon\Carbon::parse($endOfWeek)->format('Y-m-d')]);
							} else if($card['title'] == 'Upcoming Week Class Count') {
								$url = route('instructor.schedule_log', ['from_date' => \Carbon\Carbon::parse($startOfNextWeek)->format('Y-m-d'), 'to_date' => \Carbon\Carbon::parse($endOfNextWeek)->format('Y-m-d')]);
							}
						@endphp
						<a href="{{$url}}"><h1 class="mt-3 mb-0">{{ $card['value'] }}</h1></a>
					</div>
				</div>
			</div>
		@endforeach
	</div>  


@endsection