@extends('Layouts.app')
@section('content')
<div class="row">
    @can('can create redirects', auth()->user())
    @include('Redirects.Create')
    @endcan
    @can('can view redirects', auth()->user())
    @include('Redirects.Table')
    @endcan
</div>
@endsection
