@extends('Layouts.app')
@section('content')
<div class="row">
    @include('Multi_Tenancy.Create')
    @include('Multi_Tenancy.Table')
</div>

@endsection
