@extends('layouts.app')

@section('content')
<div class="limiter">
		<div class="container-login100" style="background-image: url('../login-styles/images/utrain221.jpg');">
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<form method="POST" class="login100-form validate-form" action="{{ route('login') }}">
                         @csrf
					<span class="login100-form-title p-b-49">    
                             <img height ="100px" width= "150px" src="../login-styles/images/utrain.png"/><br>
						{{ __('Admin') }}
					</span>

					<div class="wrap-input100 validate-input m-b-23" data-validate = "Email is required">
						<span class="label-input100">{{ __('E-Mail Address') }}</span>
<!--						<input class="input100" type="text" name="email" placeholder="Type your email">-->
                           <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror   
						<!--<span class="focus-input100" data-symbol="&#xf206;"></span>-->
					</div>

					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<span class="label-input100">Password</span>
<!--						<input class="input100" type="password" name="pass" placeholder="Type your password">-->
                               <input id="phone" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="password">

                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
						<!--<span class="focus-input100" data-symbol="&#xf190;"></span>-->
					</div>
					
<!--					<div class="text-right p-t-8 p-b-31">
						<a href="#">
							Forgot password?
						</a>
					</div>     -->
<br>
<br>
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button type="submit" class="login100-form-btn">
								 {{ __('Login') }}
							</button>
						</div>
					</div>

<!--					<div class="txt1 text-center p-t-54 p-b-20">
						<span>
							Or Sign Up Using
						</span>
					</div>-->

<!--					<div class="flex-c-m">
						<a href="#" class="login100-social-item bg1">
							<i class="fa fa-facebook"></i>
						</a>

						<a href="#" class="login100-social-item bg2">
							<i class="fa fa-twitter"></i>
						</a>

						<a href="#" class="login100-social-item bg3">
							<i class="fa fa-google"></i>
						</a>
					</div>

					<div class="flex-col-c p-t-155">
						<span class="txt1 p-b-17">
							Or Sign Up Using
						</span>

						<a href="#" class="txt2">
							Sign Up
						</a>
					</div>-->
				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
@endsection
 
