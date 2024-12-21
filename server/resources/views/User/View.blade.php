@extends('Layouts.app')
@section('content')
<div class="row">
@include('User.Search')
@include('User.Table')
</div>
@endsection