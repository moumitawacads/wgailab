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
										<h1 class="h2">Verification Code</h1>
										<p>We have just sent a 4-digit verification code to <span>devid........@gmail.com</span></p>
									</div>
									<div class="card-body">
											<form method="POST" action="{{ url('/admin/mfa-verify') }}" >
												@csrf
												<div class="input-wrapper d-flex align-items-center">
													<input type="text" maxlength="1" class="password-mask" name="code[]">
													<input type="text" maxlength="1" class="password-mask" name="code[]">
													<input type="text" maxlength="1" class="password-mask" name="code[]">
													<input type="text" maxlength="1" class="password-mask" name="code[]">
												</div>

												<h4>Time left: <span id="timer">03:59</span></h4>
												
												<div class="d-grid gap-2 mt-3">
													<button type="submit" class="btn btn-lg main-btn">Verify Code</button>
												</div>
												<h3>Didn't receive the code? <span id="resendBtn" style="cursor:pointer; color:#007bff;">Resend</span></h3>
												@if ($errors->any())
													<p style="color:red;">{{ $errors->first() }}</p>
												@endif
											</form>
									</div>
							</div>
					</div>
				</div>
				<div class="col-sm-10 col-md-8 col-lg-5 d-none d-lg-block">
					<div class="right-wrapper position-relative">
						<img src="{{ asset('assets/img/images/fp-right1.png') }}">
						<img src="{{ asset('assets/img/images/ab-icon1.png') }}" class="position-absolute ab-right-icon">
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

<script>
	// Combine inputs into one code
	document.querySelectorAll('.password-mask').forEach(input => {
    let realValue = '';

    input.addEventListener('keydown', function(e) {
        if (e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
            realValue += e.key;
            this.value = '*'.repeat(realValue.length);
            e.preventDefault();
            
            // Auto-focus next input
            let next = this.nextElementSibling;
            if(next && next.classList.contains('password-mask')) {
                next.focus();
            }
        } else if (e.key === 'Backspace') {
            realValue = realValue.slice(0, -1);
            this.value = '*'.repeat(realValue.length);
            e.preventDefault();
            
            // Auto-focus previous input
            let prev = this.previousElementSibling;
            if(prev && prev.classList.contains('password-mask')) {
                prev.focus();
            }
        }

        this.dataset.realValue = realValue;
    });
});

	// Timer functionality
	let timeLeft = 239; // 3 minutes 59 seconds in seconds
	const timerElement = document.getElementById('timer');
	
	function updateTimer() {
	    let minutes = Math.floor(timeLeft / 60);
	    let seconds = timeLeft % 60;
	    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
	    
	    if(timeLeft > 0) {
	        timeLeft--;
	        setTimeout(updateTimer, 1000);
	    }
	}
	
	updateTimer();
	
	// Resend code functionality
	document.getElementById('resendBtn').addEventListener('click', function() {
	    // AJAX call to resend code
	    fetch('{{ url("/admin/mfa-resend") }}', {
	        method: 'POST',
	        headers: {
	            'X-CSRF-TOKEN': '{{ csrf_token() }}',
	            'Content-Type': 'application/json'
	        }
	    }).then(response => response.json())
	      .then(data => {
	          if(data.success) {
	              timeLeft = 239;
	              updateTimer();
	              alert('Verification code resent successfully!');
	          }
	      });
	});
</script>

@endsection