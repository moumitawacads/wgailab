@extends('admin.layout')
@section('content')

<main class="d-flex w-100">
	<div class="login-wrapper position-relative d-flex flex-column justify-content-center">
		<div class="container d-flex flex-column">
			<div class="row align-items-center justify-content-center sec-wrapper g-5">
				<div class="col-sm-10 col-md-8 col-lg-6 ">
					<div class="d-flex flex-column justify-content-center align-items-center">
						<div class="forget-pass-form-wrap">
								<div class="text-center mt-4">
										<h1 class="h2">Forgot Password</h1>
										<p>Send reset link to registered email address</p>
									</div>
									<div class="card-body">
											<form method="POST" action="{{ url('/admin/forgot-password') }}" >
												@csrf
												<div class="label-input">
													<input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" required />
												</div>
												
												<div class="d-grid gap-2 mt-3">
													<button type="submit" class="btn btn-lg main-btn">Send</button>
													<a href="{{ url('/admin/login') }}" class="btn btn-lg cancel-btn mt-3">Cancel</a>
												</div>
												@if ($errors->any())
													<h4 class="warn-txt-err" style="color:red;">{{ $errors->first() }}</h4>
												@endif
												@if (session('status'))
													<h4 class="warn-txt-success" style="color:green;">{{ session('status') }}</h4>
												@endif
											</form>
									</div>
							</div>
					</div>
				</div>
				<div class="col-sm-10 col-md-8 col-lg-5 d-none d-lg-block">
					<div class="right-wrapper position-relative">
						<img src="{{ asset('assets/img/images/fp-right.png') }}">
						<img src="{{ asset('assets/img/images/an-icon.png') }}" class="position-absolute ab-right-icon">
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
	</div>
</main>

@endsection