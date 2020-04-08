@extends('layouts.app', ['cpt' => 'show-product'])

@section('title')
{{$p->brand}}-{{$p->name}}
@endsection

@section('content')
@if ($p->amount < 1) <div
    class="d-flex justify-content-center alert alert-danger">
    <strong>@lang('t.show.out')</strong>
    </div>
    @endif

    <div class="col-12 col-md-9">
        <div class="row">
            <div class="col-12">
                <h3>{{$p->brand}} {{$p->name}}</h3>
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-img-slider :imgArr="$p->img"></x-img-slider>
            </div>
        </div>

        @include('product.show.info')
        @include('product.show.social')
        @include('product.show.spec')
        @include('product.show.rev')
    </div>

    @endsection