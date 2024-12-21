@extends('Layouts.app')
@section('content')
<div class="row">
    @can('can create or update references', auth()->user())
    @include('Reference.Create')
    @endcan
    @can('can view references', auth()->user())
    @include('Reference.Table')
    @endcan

</div>
@include('Reference.Script')
@endsection

