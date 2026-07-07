@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $title }}</h1>
    <a href="{{ $createUrl }}" class="btn btn-primary">Create {{ $title }}</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    @foreach ($columns as $label)
                        <th>{{ $label }}</th>
                    @endforeach
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @foreach (array_keys($columns) as $key)
                            <td>{{ $row[$key] ?? '-' }}</td>
                        @endforeach
                        <td>
                            @if (! empty($row['show_url']))
                                <a href="{{ $row['show_url'] }}" class="btn btn-sm btn-outline-secondary">View</a>
                            @endif
                            @if (! empty($row['edit_url']))
                                <a href="{{ $row['edit_url'] }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            @endif
                            @if (! empty($row['delete_url']))
                                <form action="{{ $row['delete_url'] }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="text-center text-muted py-4">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
