@extends('frontend.layouts.app')
@section('content')

<div class="login-wrapper position-relative">
	<img src="{{asset('frontend/images/ab-red.png')}}" alt="" class="position-absolute red-ab-left">
	<div class="container position-relative">
		<div class="row align-items-center justify-content-between vh-100">
			<div class="col-md-3">
				<div class="form-area">
					<a href="https://wacadsgroup.com/ai-lab"><img src="{{asset('frontend/images/login-logo.png')}}" alt=""></a>
					<h2>Welcome back!</h2>
					@if(session('status'))
						<div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
							<span>
								<i class="fa-solid fa-circle-check me-2"></i> {{ session('status') }}
							</span>
							<button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
						</div>
					@endif
					<form  method="POST" action="{{ url('/admin/login') }}" class="d-flex flex-column">
						@csrf
						<input type="text" name="email" placeholder="Email Address">
						<div class="pass-field position-relative w-100">
								<input type="password" name="password" placeholder="Password" class="w-100">
								<img src="{{asset('frontend/images/eye.png')}}" alt="" class="position-absolute">
						</div>
						<div class="link-area d-flex align-items-center justify-content-between">
							<div class="label-input d-flex align-items-center">
								 <input id="remember_me" type="checkbox" class="form-check-input" name="remember" value="1">
								<label for="">Remember me</label>
							</div>
							<a href="{{route('password.request')}}">Forgot Password ?</a>
						</div>
						<button type="submit">Sign in</button>
					</form>
					{{-- <div class="bottom-links d-flex align-items-center">
						<p>Don't have an account?</p>
						<a href="#">Sign up</a>
					</div> --}}
				</div>
			</div>
			<div class="col-md-6">
				<div class="right-wrap">
					<div class="img-area position-relative">
						<img src="{{asset('frontend/images/ai-login.png')}}" alt="" class="w-100 main-img">
						<video class="screen-video" autoplay muted loop playsinline>
							<source src="{{asset('frontend/images/ai-login-video.mp4')}}" type="video/mp4">
						</video>
						<img src="{{asset('frontend/images/cloud.png')}}" alt="" class="cloud cloud-1">
						<img src="{{asset('frontend/images/cloud.png')}}" alt="" class="cloud cloud-2">
					</div>
					<p>© Copyright AI Lab. {{date('Y')}}.</p>
				</div>
			</div>
		</div>
	</div>
	<img src="{{asset('frontend/images/ab-red-right.png')}}" alt="" class="position-absolute red-ab-right">
</div>

@endsection