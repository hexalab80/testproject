@extends('layouts.app')

@section('content')
  <div class="container">
    @if(Session::has('success'))
    <div class="card-panel teal lighten-2 white-text">
      {{Session::get('success')}}
      <a onclick="$(this).parent().hide();" class="waves-effect waves-light btn right red lighten-2">&times;</a>
    </div>
    @elseif(Session::has('error'))
    <div class="card-panel red lighten-2 white-text">
      {{Session::get('error')}}
      <a onclick="$(this).parent().hide();" class="waves-effect waves-light btn right teal lighten-2">&times;</a>
    </div>
    @endif

    <div class="row section">
      <div class="col s6">
          <h5>Per Day Spend Total Amount</h5>
      </div>
    </div>

    <div class="row">
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th width="236px;">Date</th>
              <th width="236px;">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($paytm_requests as $key => $vv)
            <tr>
              <td>{{++$key}}</td>
              <td>{{$vv->update_date}}</td>
              <td>&#8377;{{$vv->paid_per_day_amount}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
