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
          <h5>Transaction History</h5>
      </div>
      <div class="col s6">
          <a href="{{url('transactions/download')}}" class="waves-effect waves-light btn teal right"><i class="material-icons left">file_download</i> Download</a>
      </div>
    </div>

    <div class="row">
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th width="136px;">Order ID</th>
              <th>Name</th>
              <th>Amount</th>
              <th>Mobile</th>  
              <th>Datetime</th>
              <th>Paid Time</th>
              <th>Paid Time Status</th>
              <th>Status</th>
              <th width="136px;">Remark</th>
              <th width="136px;">Payment Type</th>  
              <th width="136px;">Device Type</th> 
            </tr>
          </thead>
          <tbody>
            @foreach($paytm_requests as $key => $vv)
            <tr>
              <td>{{++$key}}</td>
              <td>WER{{str_pad($vv->order_id, 10, 0, STR_PAD_LEFT )}}</td>
              <td><a href="{{url('users/'.$vv->user_info->id)}}">{{$vv->user_info->name}}</a></td>
              <td>&#8377;{{$vv->amount}}</td>
              <td>{{$vv->paytm_mobile_number}}</td>
              <td>{{$vv->datetime}}</td>
              <td>{{$vv->updated_at}}</td>
              <td>{{$vv->updated_at->diffForHumans()}}</td>
              <td>{{$vv->status==2 ? 'Paid': 'Reject'}}</td>
              <td>{{$vv->remark != '' ? $vv->remark : '-'}}</td>
              <td>{{$vv->payment_type =='1' ? 'PayTm': 'Google Pay'}}</td>
              <td>{{$vv->user_info->device_type =='1' ? 'Android': 'IOS'}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
