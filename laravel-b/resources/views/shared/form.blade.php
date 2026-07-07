@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">{{ $title }}</h1>
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="row g-3">
                @foreach ($fields as $field)
                    <div class="col-md-{{ ($field['type'] ?? 'text') === 'textarea' ? 12 : 6 }}">
                        <label class="form-label">{{ $field['label'] }}</label>
                        @if (($field['type'] ?? 'text') === 'textarea')
                            <textarea name="{{ $field['name'] }}" class="form-control" rows="4">{{ old($field['name'], $field['value'] ?? '') }}</textarea>
                        @elseif (($field['type'] ?? 'text') === 'select')
                            <select name="{{ $field['name'] }}" class="form-select">
                                <option value="">Select...</option>
                                @foreach ($field['options'] ?? [] as $value => $label)
                                    <option value="{{ $value }}" @selected(old($field['name'], $field['value'] ?? '') == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif (($field['type'] ?? 'text') === 'multiselect')
                            <select name="{{ $field['name'] }}[]" class="form-select" multiple size="8">
                                @foreach ($field['options'] ?? [] as $value => $label)
                                    <option value="{{ $value }}" @selected(collect(old($field['name'], $field['value'] ?? []))->contains($value))>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            <input
                                type="{{ $field['type'] ?? 'text' }}"
                                name="{{ $field['name'] }}"
                                value="{{ old($field['name'], $field['value'] ?? '') }}"
                                class="form-control"
                                @if (! empty($field['step'])) step="{{ $field['step'] }}" @endif
                                @if (! empty($field['readonly'])) readonly @endif
                            >
                        @endif
                        @if (! empty($field['help']))
                            <div class="form-text">{{ $field['help'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <button class="btn btn-primary mt-4">{{ $title }}</button>
        </form>
    </div>
</div>
@endsection
