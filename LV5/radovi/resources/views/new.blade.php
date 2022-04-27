@extends('layouts.app')

@section('content')
@if (Auth::user()->role == "nastavnik")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('main.new_task') }}</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('create', app()->getLocale()) }}">
                        @csrf

                        <div class="form-group row">
                            <label for="title_hr" class="col-md-4 col-form-label text-md-right">{{ __('main.title_hr') }}</label>

                            <div class="col-md-6">
                                <input id="title_hr" type="text" class="form-control" name="title_hr" value="{{ old('title_hr') }}" required autocomplete="title_hr" autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title_en" class="col-md-4 col-form-label text-md-right">{{ __('main.title_en') }}</label>

                            <div class="col-md-6">
                                <input id="title_en" type="text" class="form-control" name="title_en" value="{{ old('title_en') }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="task" class="col-md-4 col-form-label text-md-right">{{ __('main.task_desc') }}</label>

                            <div class="col-md-6">
                                <input id="task" type="text" class="form-control" name="task" value="{{ old('task') }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="study_type" class="col-md-4 col-form-label text-md-right">{{ __('main.study_type') }}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="study_type">
                                    <option value="strucni">Stručni</option>
                                    <option value="preddiplomski">Preddiplomski</option>
                                    <option value="diplomski">Diplomski</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if (Auth::user()->role != "nastavnik")
    Unauthorized
@endif
@endsection