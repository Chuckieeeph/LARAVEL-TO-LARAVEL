@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Login to Enrollment Management</h1>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button class="btn btn-primary w-100">Login</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="{{ route('register.form') }}">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
