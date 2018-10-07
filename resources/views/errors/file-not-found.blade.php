@extends('errors.layout')

    @section('content')
        <div class="title m-b-md">
            {{trans('general.file_not_found')}}
        </div>
        <a href="{{ url()->previous() }}">Back</a>
    @endsection