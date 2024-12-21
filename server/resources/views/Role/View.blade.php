@extends('Layouts.app')
@section('content')
<div class="row">
    @can('can view roles edit or create form', auth()->user())
    @include('Role.Create')
    @endcan
    @can('can view roles data table', auth()->user())
    @include('Role.Table')
    @endcan


</div>
@endsection
