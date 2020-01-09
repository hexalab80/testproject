@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
        {!! $chart->html() !!}
      </div>
      <div class="col-md-6">
        {!!$line->html() !!}
      </div>
      <div class="col-md-6">
        {!!$area->html() !!}
      </div>
      <div class="col-md-6">
        {!! $chart1->html() !!}
      </div>
      <div class="col-md-6">
        {!! $chart2->html() !!}
      </div>
      <div class="col-md-6">
        {!! $pie->html() !!}
      </div>
       <div class="col-md-6">
        {!! $pie1->html() !!}
      </div>
    </div>
</div>
{!! Charts::scripts() !!}
{!! $chart->script() !!}
{!! $line->script() !!}
{!! $area->script() !!}
{!! $chart1->script() !!}
{!! $chart2->script() !!}
{!! $pie->script() !!}
{!! $pie1->script() !!}
@endsection