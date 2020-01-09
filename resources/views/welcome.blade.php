@extends('layouts.app')

@section('content')
  <div class="container">
    <h5>Dashboard</h5>
    <small>Welcome to administration</small>
    <div class="row">
      <div class="col s6 m4">
        <div class="card ">
          <div class="card-title  lighten-2 white-text">
            <span class="small padding-15">Total User</span>
          </div>
          <div class="card-content">
            <h5>{{$user_count}}</h5>
          </div>
        </div>
      </div>
      <div class="col s6 m4">
        <div class="card ">
          <div class="card-title  lighten-2 white-text">
            <span class="small padding-15">Total Paytm Amount</span>
          </div>
          <div class="card-content">
            <h5>{{$total_paytm_amt}}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
