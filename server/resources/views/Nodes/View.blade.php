@extends('Layouts.app')
@section('content')
<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

<div class="row">
    @can('can view nodes edit or create form', auth()->user())
    @include('Nodes.Create')
    @endcan
    @can('can view nodes data table', auth()->user())
    @include('Nodes.Table')
    @endcan

</div>

@endsection
