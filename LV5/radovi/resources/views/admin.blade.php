@extends('layouts.app')

@section('content')
@if (Auth::user()->role == "admin")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($users as $user)
            <div class="card mb-3">
                <div class="card-header">
                    <b>{{ $user->name }}</b>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('changeRole') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}" />

                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Roles') }}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="role">
                                    <option value="{{ $user->role }}" selected disabled hidden>{{ ucfirst($user->role) }}</option>
                                    <option value="student">Student</option>
                                    <option value="nastavnik">Nastavnik</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Edit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>                
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@if (Auth::user()->role != "admin")
    Unauthorized
@endif
@endsection