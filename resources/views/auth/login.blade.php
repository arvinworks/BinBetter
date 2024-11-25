@extends('layouts.back.app')

@section('content')
<div class="row align-items-center justify-content-center g-0 min-vh-100">
    <div class="col-12 col-md-8 col-lg-6 col-xxl-4 py-8 py-xl-0">

        @include('components.toggle-theme')

        <!-- Card -->
        <div class="card smooth-shadow-md">
            <!-- Card body -->
            <div class="card-body p-6">
                <div class="mb-4">
                    <a href="{{ url('/') }}"><img src="{{ asset('assets/back/images/brand/logo/logo.png') }}" class="mb-2 text-inverse" alt="Image" /></a>
                    <p class="mb-6">Please enter your user information.</p>
                </div>
                <!-- Form -->
                <form>
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="identifier" class="form-label">{{ __('Username or Email') }}</label>
                        <input type="text" class="form-control" name="identifier" id="identifier" value="{{ old('identifier') }}" placeholder="Enter you're email or username" autofocus />
                        <span class="invalid-feedback d-block" role="alert" id="identifier_error"></span>
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="**************" />
                        <span class="invalid-feedback d-block" role="alert" id="password_error"></span>
                    </div>
                    <!-- Checkbox -->
                    <div class="d-lg-flex justify-content-between align-items-center mb-4">
                        <div class="form-check custom-checkbox">
                            <input type="checkbox" class="form-check-input" name="remember" id="rememberme" {{ old('remember') ? 'checked' : '' }} />
                            <label class="form-check-label" for="rememberme">{{ __('Remember me') }}</label>
                        </div>
                    </div>
                    <div>
                        <!-- Button -->


                        <div class="d-flex flex-column  justify-content-center">

                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" id="loginAccount">{{ __('Sign in') }}</button>
                            </div>

                            <h4 class="mt-2 mb-2 fw-bold text-center d-none">OR</h4>

                            <div class="d-grid d-none">
                                <a href="{{ route('google.redirect') }}" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-google mx-2" viewBox="0 0 16 16">
                                        <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z" />
                                    </svg>Login with Google</a>
                            </div>
                        </div>



                        <div class="d-md-flex justify-content-between mt-4">
                            <div class="mb-2 mb-md-0">
                                Don't have an Account?
                                <a href="{{ route('register') }}" class="fs-5"> create</a>
                            </div>


                            <div>

                                @if (Route::has('password.request'))
                                <a class="text-inherit fs-5" href="{{ route('password.request') }}">
                                    {{ __("Forgot your password?") }}
                                </a>
                                @endif


                            </div>
                        </div>


                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'login']) }}"></script>
@endpush