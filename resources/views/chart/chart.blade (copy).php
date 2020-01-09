@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
        {!! $chart->html() !!}
      </div>
       <div class="col-md-6">
        {!!$pie->html() !!}
      </div>
      <div class="col-md-6">
        {!!$donut->html() !!}
      </div>
      <div class="col-md-6">
        {!!$line->html() !!}
      </div>
      <div class="col-md-6">
        {!!$area->html() !!}
      </div>
       <div class="col-md-6">
        {!!$areaspline->html() !!}
      </div>
      <div class="col-md-6">
        {!!$geo->html() !!}
      </div>
      <div class="col-md-6">
        {!!$percent->html() !!}
      </div>
    </div>
</div>
{!! Charts::scripts() !!}
{!! $chart->script() !!}
{!! $pie->script() !!}
{!! $donut->script() !!}
{!! $line->script() !!}
{!! $area->script() !!}
{!! $areaspline->script() !!}
{!! $geo->script() !!}
{!! $percent->script() !!}
@endsection