@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Register User</h1>
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="Administrator">Administrator</option>
                                <option value="Registrar">Registrar</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 mt-4">Create Account</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="{{ route('login') }}">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
