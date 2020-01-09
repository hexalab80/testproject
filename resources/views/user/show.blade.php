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

    <h5>User Detail</h5>
    <!-- Trigger the modal with a button -->
    <a href="javascript:void(0);" class="btn" onclick="userMsg('{{$user->id}}','/addMsg');">Send Message</a>
    <div class="row">
      <div class="col s12 responsive-table">
        <table class="bordered striped highlight" style="display:table;">
          <tbody>
            <div class="row">
              <div class="col s4">
                <tr>
                  <th>User Id</th>
                  <th>:</th>
                  <td>{{$user->id}}</td>
                </tr>
              </div> 
            </div>
            <div class="row">
              <div class="col s4">
                <tr>
                  <th>Name</th>
                  <th>:</th>
                  <td>{{$user->name}}</td>
                </tr>
              </div> 
              <div class="col s4">
                <tr>
                  <th>Email</th>
                  <th>:</th>
                  <td>{{$user->email}}</td>
                </tr>
              </div>
            </div>
            <div class="row">
              <div class="col s4">
                <tr>
                  <th>Mobile</th>
                  <th>:</th>
                  <td>{{$user->phone_number}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Role</th>
                  <th>:</th>
                  <td>{{$user->role->role}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Date of Birth</th>
                  <th>:</th>
                  <td>{{date('d F,Y',strtotime($user->date_of_birth))}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Weight (in lbs)</th>
                  <th>:</th>
                  <td>{{$user->weight}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Height (in cm)</th>
                  <th>:</th>
                  <td>{{$user->height}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Total Steps</th>
                  <th>:</th>
                  <td>{{$user->total_steps}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Reddem Coins</th>
                  <th>:</th>
                  <td>{{$user->redeem_coins}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Available Coins</th>
                  <th>:</th>
                  <td>{{$user->available_coins}}</td>
                </tr>
              </div>
            </div>
              <div class="col s4">
                <tr>
                  <th>Status</th>
                  <th>:</th>
                  <td>{{$user->status == 0 ? 'Inactive' : 'Active'}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Referal Code:</th>
                  <th>:</th>
                  <td>{{$user->referal_code}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Friend Referal Code:</th>
                  <th>:</th>
                  <td>{{$user->frd_referral_code}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Created At:</th>
                  <th>:</th>
                  <td>{{$user->created_at}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>User App Version:</th>
                  <th>:</th>
                  <td>{{$user->user_app_version}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Device type:</th>
                  <th>:</th>
                  <td>{{$user->device_type=='1' ? 'Android' :'IOS'}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Wallet Balance:</th>
                  <th>:</th>
                  <td>&#8377;{{$user->wallet_balance}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Paid Amount:</th>
                  <th>:</th>
                  <td>&#8377;{{$user->paytm_paid_amt}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Last Seen:</th>
                  <th>:</th>
                  <td>{{\Carbon\Carbon::createFromTimeStamp(strtotime($user->last_seen))->diffForHumans()}}</td>
                </tr>
              </div>
            </div>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <h5>Wallet Log</h5>
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th width="136px;">Reward Type</th>
              <th width="136px;">Amount</th>
              <th width="136px;">Redem Date</th>
              <th width="155px;">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($wallet_log as $key => $vv)
            <tr>
              <td>{{++$key}}</td>
              <td>
                  {{$vv->reward_id==1 ? '100 coins': ''}}
                  {{$vv->reward_id==2 ? '200 coins': ''}}
                  {{$vv->reward_id==3 ? '300 coins': ''}}
                  {{$vv->reward_id==4 ? '500 coins': ''}}
                  {{$vv->reward_id==5 ? '1000 coins': ''}}
                  {{$vv->reward_id==6 ? '700 coins': ''}}
              </td>
              <td>&#8377;{{$vv->rupees}}</td>
              <td>{{$vv->redeem_date}}</td> 
              <td>{{$vv->scratch_status==1 ? 'Scratch' : 'Claimed'}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <h5>Reward Coins</h5>
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th width="100px;">S No.</th>
              <th width="217px;">Type</th>
              <th width="410;">Coins</th>
              <th width="410;">Created At</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reward_coins as $key => $vvv)
            <tr>
              <td>{{++$key}}</td>
              <td>
                {{$vvv->reward_type==1 ? 'Friend Referal': ''}}
                {{$vvv->reward_type==2 ? 'Reward Ads': ''}}
                {{$vvv->reward_type==3 ? 'Lucky Coupons': ''}}
              </td>
              <td>{{$vvv->coins}}</td>
              <td>{{$vvv->created_at != '' ? \Carbon\Carbon::createFromTimeStamp(strtotime($vvv->created_at))->diffForHumans() : ''}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <h5>Paytm Transcation</h5>
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th>Payment Type</th>
              <th width="136px;">Name</th>
              <th>Amount</th>
              <th width="136px;">Mobile</th>  
              <th>Datetime</th> 
              <th width="136px;">Status</th>
              <th width="136px;">Remark</th>
            </tr>
          </thead>
          <tbody>
            @foreach($paytm_requests as $key => $vv)
            <tr>
              <td>{{++$key}}</td>
              <td>{{$vv->payment_type == '1' ? 'PayTm' : 'Google Pay'}}<!-- <img src="{{$vv->qr_code}}" height="50px" width="50px"> --></td>
              <td>{{$vv->user_info->name}}</td>
              <td>&#8377;{{$vv->amount}}</td>
              <td>{{$vv->paytm_mobile_number}}</td>
              <td>{{$vv->datetime}}</td> 
              <td>{{$vv->status==1 && $vv->hold_status==1? 'Unpaid' : ''}}
                {{ $vv->status==2 ? 'Paid' : ''}} 
                {{ $vv->status==3? 'Reject' :''}}
                {{$vv->status==1 && $vv->hold_status==2? 'Hold' : ''}}
              </td>
              <td>{{$vv->remark}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <h5>Message Info</h5>
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th width="336px;">Mobile Message</th>
              <th width="236px;">Broadcast Message</th> 
              <th width="136px;">Created At</th> 
            </tr>
          </thead>
          <tbody>
            @foreach($messages_data as $key => $vv)
            <tr>
              <td>{{++$key}}</td>
              <td>{{$vv->message}}</td>
              <td>{{$vv->broadcast_message}}</td> 
              <td>{{$vv->created_at}}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
  <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <a href="javascript:void(0);" class="close modal-action modal-close waves-effect" style="float: right;">&times;</a>
        <h4 class="modal-title">Send Message</h4>
      </div>
      <div class="modal-body">
        <form action="" method="post" onsubmit="if(!confirm('Are you sure to you confirm?')){return false;}">
          {{csrf_field()}}
          <div class="row">
          <div class="input-field col s12">
            <textarea name="message" class="materialize-textarea" data-length="120" rows="8" cols="80" placeholder="Enter Message"></textarea>
            <label for="message" class="active">Message</label>
          <span class="character-counter" style="float: right; font-size: 12px; height: 1px;"></span></div>
        </div>
          <div class="row">
          <div class="input-field col s12">
            <textarea name="broadcast_message" class="materialize-textarea" data-length="120" rows="8" cols="80" placeholder="Enter Broatcast Message"></textarea>
            <label for="" class="active">Broatcast Message</label>
          <span class="character-counter" style="float: right; font-size: 12px; height: 1px;"></span></div>
        </div>
        <input type="hidden" name="user_id" id="user_s"> 
          <button type="submit" class="waves-effect waves-green btn-flat btn">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    function userMsg(id, url){
      $('#user_s').val(id);
      $('#myModal').modal('open');
      $('#myModal form').attr('action', url);
    }
</script>
@endsection
