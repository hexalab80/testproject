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

    <div class="row">
      <div class="col s12">
        <div class="col s6">
          <h5>Paytm Request </h5>
        </div>
        <div class="col s6" style="float: right; padding-left: 234px;">
          <h5>Pending Money  &#8377;{{$pending_rupee->pending_rupee}}</h5>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th width="136px;">Action</th>  
              <th>Last Seen</th>
              <th>S No.</th>
             <!--  <th>QR Code</th> -->
             <th width="136px;">Payment Type</th>
              <th width="136px;">Order ID</th>
              <th width="136px;">Name</th>
              <th width="136px;">Device Type</th>
              <th width="136px;">App Version</th>
              <th>Amount(&#8377;)</th>
              <th width="136px;">Mobile</th>
              <th width="136px;">Paid Request Count</th> 
              <th width="136px;">Paid Total Amt (&#8377;)</th>
              <th>Datetime</th> 
              <th>Created At</th>
              <th>User Request Status</th>
              
            </tr>
          </thead>
          <tbody>
            @foreach($paytm_requests as $key => $vv)
            <tr>
              <td>
                <a href="{{url('/paytm_requests', [$vv->id])}}" class="waves-effect waves-light btn"> <i class="material-icons">remove_red_eye</i> </a>
                <br><br>
                
                <!-- <button type="button" class="waves-effect waves-light btn red" onclick="deletePopup('{{$vv->id}}', '/paytm_requests/{{$vv->id}}')"><i class="material-icons">clear</i></button> -->
              </td>
               <td>{{$vv->last_seen != '' ? \Carbon\Carbon::createFromTimeStamp(strtotime($vv->last_seen))->diffForHumans() : ''}}</td> 
              <td>{{++$key}}</td>
             <!--  <td><img src="{{$vv->qr_code}}" height="50px" width="50px"></td>   -->
             <td>{{$vv->payment_type=='1' ? 'Paytm' : 'Google Pay'}}</td>
              <td>WER{{str_pad($vv->order_id, 10, 0, STR_PAD_LEFT )}}</td>
              <td><a href="{{url('/users/'.$vv->user_info->id)}}">{{$vv->user_info->name}}</a></td>
              <td>{{$vv->user_info->device_type == '1' ? 'Android': 'IOS' }}</td>
              <td>{{$vv->user_info->user_app_version }}</td>
              <td>{{$vv->amount}}</td>
              <td>{{$vv->paytm_mobile_number}} 
                @if($vv->duplicate_count)
              <img src="{{asset('1525600435_duplicate-finder.png')}}" height="30px;" width="30px;">
              @endif</td>
              <td>{{$vv->paytm_paid_count}}</td>
              <td>{{$vv->paid_amt}}</td>
              <td>{{$vv->datetime}}</td> 
              <td>{{$vv->created_at}}</td>
             
              <td>
                @if((strtotime(date('Y-m-d H:i:s')))-((strtotime($vv->datetime))) > 86400)
                <i class="material-icons red-text">arrow_downward</i>
                @else
                <i class="material-icons green-text">arrow_upward</i></td> 
                @endif
              
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
