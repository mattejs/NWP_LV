@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($tasks as $task)
            <div class="card mb-3">
                <div class="card-header">
                    {{ $task[1]->title_hr }} ({{ $task[1]->title_en }}) - <b>{{ ucfirst(trans($task[1]->study_type)) }}</b>
                    @if (Auth::user()->role == "student")
                        <a href="{{ route('sign', array_merge(['studentId' => $task[0]->id], ['task' => $task[1]->id])) }}" class="float-right">{{ __('Sign') }}</a><br>
                    @else
                        <div class="float-right">Prijavio: <b>{{ $task[0]->name }}</b></div>
                    @endif
                </div>
                <div class="card-body">
                    {{ $task[1]->task }}

                    @if (Auth::user()->role == "nastavnik")
                        <a href="{{ route('accept', array_merge(['studentId' => $task[0]->id], ['task' => $task[1]->id])) }}" class="float-right">{{ __('Prihvati') }}</a><br>
                    @endif
                </div>                
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection