@extends('layouts.app')

@section('content')
<h1>Edit Profile: {{ $profile->name }}</h1>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('profiles.update', $profile) }}" method="POST">
    @csrf
    @method('PUT')

    <p>
        <label>Router:</label><br>
        <select name="router_id" required>
            <option value="">-- Select router --</option>
            @foreach($routers as $id => $name)
                <option value="{{ $id }}" {{ old('router_id', $profile->router_id) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </p>

    <p>
        <label>Name:</label><br>
        <input type="text" name="name" value="{{ old('name', $profile->name) }}" required>
    </p>

    <p>
        <label>Code (e.g. KUMI, MBAO):</label><br>
        <input type="text" name="code" value="{{ old('code', $profile->code) }}" required>
    </p>

    <p>
        <label>Rate limit (e.g. 2M/2M):</label><br>
        <input type="text" name="rate_limit" value="{{ old('rate_limit', $profile->rate_limit) }}">
    </p>

    <p>
        <label>Time limit (minutes):</label><br>
        <input type="number" name="time_limit_minutes"
               value="{{ old('time_limit_minutes', $profile->time_limit_minutes) }}" min="0">
    </p>

    <p>
        <label>Data limit (MB):</label><br>
        <input type="number" name="data_limit_mb"
               value="{{ old('data_limit_mb', $profile->data_limit_mb) }}" min="0">
    </p>

    <p>
        <label>Price:</label><br>
        <input type="number" step="0.01" name="price"
               value="{{ old('price', $profile->price) }}" required min="0">
    </p>

    <p>
        <label>
            <input type="checkbox" name="is_default" value="1"
                {{ old('is_default', $profile->is_default) ? 'checked' : '' }}>
            Default profile
        </label>
    </p>

    <p>
        <button type="submit">Update</button>
        <a href="{{ route('profiles.index') }}">Cancel</a>
    </p>
</form>
@endsection
