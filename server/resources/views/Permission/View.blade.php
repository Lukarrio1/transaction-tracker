@extends('Layouts.app')
@section('content')
<div class="row">
    @can('can view permissions edit or create form', auth()->user())
    @include('Permission.Create')
    @endcan
    @can('can view permissions data table', auth()->user())
    @include('Permission.Table')
    @endcan
</div>
@endsection
