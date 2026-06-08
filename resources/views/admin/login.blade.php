@extends('admin.layout')
@section('content')

<main class="d-flex w-100">
	<div class="login-wrapper position-relative d-flex flex-column justify-content-center">
		<div class="container d-flex flex-column">
			<div class="row align-items-center justify-content-center sec-wrapper g-5">
				<div class="col-sm-10 col-md-8 col-lg-4 ">
					<div class="card-wrapper">
						<div class="text-center mt-4">
							<img src="{{ asset('assets/img/images/urz-logo.png') }}">
							<h1 class="h2">Welcome back!</h1>
						</div>
						@if(session('status'))
							<div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
								<span>
									<i class="fa-solid fa-circle-check me-2"></i> {{ session('status') }}
								</span>
								<button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
							</div>
						@endif
						<div class="card-body">
							<form method="POST" action="{{ url('/admin/login') }}" >
								@csrf
								<div class="label-input">
									<!--<label class="form-label">Email</label>-->
									<input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" />
								</div>
								<div class="label-input">
									<!--<label class="form-label">Password</label>-->
									<input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" />
								</div>
								<!--<div>
									<div class="form-check align-items-center">
										<input id="customControlInline" type="checkbox" class="form-check-input" value="remember-me" name="remember-me" checked>
										<label class="form-check-label text-small" for="customControlInline">Remember me</label>
									</div>
								</div>-->
								<div class="d-grid gap-2 mt-3">
									<button type="submit" class="btn btn-lg main-btn">Sign In</button>
									<a href="{{route('password.request')}}" class="back-btn">Reset Password</a>
								</div>
								@if ($errors->any())
									<p style="color:red;">{{ $errors->first() }}</p>
								@endif
							</form>
						</div>
					</div>
				</div>
				<div class="col-sm-10 col-md-8 col-lg-5 d-none d-lg-block">
					<div class="right-wrapper">
					<img src="{{ asset('assets/img/images/urz-login-right.png') }}">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 d-flex justify-content-end align-items-end">
					<p class="login-footer-text">Urban Rez Solutions App © {{date('Y')}}</p>
				</div>
			</div>
		</div>
		<img src="{{ asset('assets/img/images/login-bg.png') }}" class="position-absolute bg-first">
		<img src="{{ asset('assets/img/images/login-bg.png') }}" class="position-absolute bg-second">
		<img src="{{ asset('assets/img/images/bg-third.png') }}" class="position-absolute bg-third">
		<div class="login-logo-wrapper d-flex flex-column align-items-start justify-content-end">
            <label>Funder Recognition</label>
            <div class="img-wrapper">
                <img src="{{asset('assets/img/images/funder-logo1.png')}}">
                <img src="{{asset('assets/img/images/funder-logo2.png')}}">
            </div>
        </div>
	</div>
	</main>

@endsection