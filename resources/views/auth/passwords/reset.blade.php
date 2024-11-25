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
                    <p class="mb-6">Enter you're new password.</p>
                </div>
                <!-- Form -->
                <form id="resetPasswordForm" action="{{ route('password.update') }}">
                     

                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email"  class="form-control" name="email" id="email"  value="{{ $email ?? old('email') }}" placeholder="Enter your email" autocomplete="email" autofocus />
                        <span class="invalid-feedback d-block" role="alert" id="email_error"></span>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="**************" />
                        <span class="invalid-feedback d-block" role="alert" id="password_error"></span>
                    </div>

                     <!-- Confirm Password -->
                     <div class="mb-3">
                        <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                        <input type="password"  class="form-control @error('password') is-invalid @enderror" name="password_confirmation" id="password-confirm" placeholder="**************" />
                    </div>
                   
                    <div>
                        <!-- Button -->
                        <div class="d-grid">
                            <button type="button" class="btn btn-primary" id="resetAccount">{{ __('Reset Password') }}</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ route('secure.js', ['filename' => 'reset']) }}"></script>
@endpush