<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExample">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}">Students</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('subjects.index') }}">Subjects</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('enrollments.index') }}">Enrollments</a></li>
            </ul>
            <div class="d-flex gap-2 align-items-center text-white">
                <span>{{ auth()->user()->name ?? 'Guest' }} ({{ auth()->user()->role ?? 'n/a' }})</span>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
