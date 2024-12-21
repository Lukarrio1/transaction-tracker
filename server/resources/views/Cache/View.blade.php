@extends('Layouts.app')
@section('content')
<div class="col-sm-8 offset-sm-2 mt-2">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header text-center bg-white  h3 fw-bold">
            Cache Managment
        </div>
        <div class="card-body  bg-white">

            <div class="">
                <form action="{{route('clearCache')}}">

                    <div class="form-group">
                        <label>Select Cache to Clear</label>
                        @foreach ($cacheOptions as $key=>$value)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="{{'option1'.$key}}" value="{{$key}}" name="{{$key}}">
                            <label class="form-check-label" for="{{'option1'.$key}}">
                                {{collect(explode('_',$key))->map(fn($item)=>ucfirst($item))->join(' ')}}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center">
                        <button class="btn-success btn" type="submit"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                    </div>

                </form>
            </div>
        </div>
        {{-- {testing here} --}}


    </div>
</div>
@endsection
