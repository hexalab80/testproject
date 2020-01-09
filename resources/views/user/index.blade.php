@extends('layouts.app')

@section('content')
<style type="text/css">
  .select-dropdown {
    display: none !important;
}
</style>
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
      <div class="col s12">
        <div class="col s6">
          <h5>User List</h5>
        </div>
        <div class="col s6">
          <a href="{{url('users/download')}}" class="waves-effect waves-light btn teal right"><i class="material-icons left">file_download</i> Download</a>
      </div>
      </div>
    </div>

    <div class="row">
      <div class="col s12">
        <!-- <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example"> -->
          <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="user_list" style="width: 100%;">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th>User Id</th>
              <th>Name</th>
              <th>Role</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Available Coins</th>  
              <th>Total Steps</th>
              <th>Referal Count</th>
              <th>Reward Ads Count</th>  
              <th>Wallet Balance</th>
              <th>Paytm Paid Amount</th> 
              <th>Email Verified</th>
              <th>Created At</th>
              <th>Join Time</th>
              <th>Last Seen</th>
              <th>Device Type</th>
              <th>User App Version</th>
              <th>Action</th>
            </tr>
          </thead>
         <!-- <tbody>
            @foreach($users as $key => $user)
            <tr>
              <td>{{++$key}}</td>
              <td>{{$user->id}}</td>
              <td><a href="{{url('/users', [$user->id])}}">{{ucwords($user->name)}} </a></td>
              <td>{{$user->role->role}}</td>
              <td>{{$user->email}}</td>
              <td>{{$user->phone_number}}</td>
              <td>{{$user->available_coins}}</td>
              <td>{{$user->total_steps}}</td>
              <td>{{$user->refferal_count}}</td>
              <td>{{$user->reward_ads}}</td>
              <td>{{$user->wallet_balance}}</td>
              <td>{{$user->paytm_paid_amt}}</td>
              <td>
                @if($user->email_verified_at)
                <i class="material-icons green-text">check</i>
                @else
                <i class="material-icons red-text">clear</i>
                @endif
              </td>
              <td>{{$user->created_at}}</td>
              <td>{{$user->created_at->diffForHumans()}}</td>
              <td>{{$user->last_seen != '' ? \Carbon\Carbon::createFromTimeStamp(strtotime($user->last_seen))->diffForHumans() : ''}}</td>
              <td>
                <a href="{{url('/users', [$user->id])}}" class="waves-effect waves-light btn"> <i class="material-icons">remove_red_eye</i> </a>
                <br><br>
                <a href="javascript:void(0);" class="waves-effect waves-light btn" alt="{{$user->status=='2' ? 'Unblock' : 'Block'}}" onclick="userPopup('{{$user->id}}', '/users/{{$user->id}}','{{$user->status!=2 ? 2: 1}}')">{{$user->status=='2' ? 'Unblock' : 'Block'}}</a>
                <!-- <button type="button" class="waves-effect waves-light btn red" onclick="deletePopup('{{$user->id}}', '/users/{{$user->id}}')"><i class="material-icons">clear</i></button> -->
            <!--  </td>
            </tr>
            @endforeach
          </tbody> -->
        </table>
      </div>
    </div>
  </div>
  <!-- user modal-->
  <div class="modal" id="usermodel">
        <div class="modal-content">
          <p id="status_mod"></p>
          <form action="" method="post">
            {{csrf_field()}} {{method_field('PUT')}}
            <input type="hidden" name="status" id="status_v">
            <button type="submit" class="waves-effect waves-green btn-flat" style="background-color: green;">Yes</button>
            <button type="button" class="modal-action modal-close waves-effect waves-red btn-flat" style="background-color: red;">No</button>
          </form>
        </div>
      </div>
  <script type="text/javascript">
    function userPopup(id, url,status){
      $('#status_v').val(status);
      if(status==2){
        $('#status_mod').html('Are you sure want to block?');
      }else{
        $('#status_mod').html('Are you sure want to unblock?');
      }
     $('#usermodel').modal('open');
     $('#usermodel form').attr('action', url); 
    }

    $(document).ready(function() {

      $('#user_list').DataTable({
          "processing": true,
          "serverSide": true,
          "ajax": {
            "url": "/users/serverProcessing",
            "dataType": "json",
            "type": "POST",
            "data": {"_token": "<?= csrf_token() ?>"}
          },
          "columns": [
            {"data": "serial_number"},
            {"data": "id"},
            {"data": "name"},
            {"data": "role"},
            {"data": "email"},
            {"data": "phone_number"},
            {"data": "available_coins"},
            {"data": "total_steps"},
            {"data": "refferal_count"},
            {"data": "reward_ads"},
            {"data": "wallet_balance"},
            {"data": "paytm_paid_amt"},
            {"data": "email_verified"},
            {"data": "created_at"},
            {"data": "join_time"},
            {"data": "last_seen"},
            {"data": "device_type"},
            {"data": "user_app_version"},
            {"data": "action", "searchable": false, "orderable": false}
          ],"order":[[1, 'desc']]
      });
    }); 
  </script>
@endsection
