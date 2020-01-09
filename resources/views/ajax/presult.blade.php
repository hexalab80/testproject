<div class="row">
  <div class="col s3" style="float: right;">  
  <input type="text" name="filter" id="filter" placeholder="Enter Search">
</div>
</div>
<table class="table table-bordered">
    <thead style="background-color: #ff8000; color: #fff;">
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
    <tbody>
        @foreach ($data as $key => $user)
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
              <td>{{$user->device_type == '1' ? 'Android' :'IOS'}}</td>
              <td>{{$user->user_app_version}}</td>
              <td>
                <a href="{{url('/users', [$user->id])}}" class="waves-effect waves-light btn"> <i class="material-icons">remove_red_eye</i> </a>
                <br><br>
                <a href="javascript:void(0);" class="waves-effect waves-light btn" alt="{{$user->status=='2' ? 'Unblock' : 'Block'}}" onclick="userPopup('{{$user->id}}', '/users/{{$user->id}}','{{$user->status!=2 ? 2: 1}}')">{{$user->status=='2' ? 'Unblock' : 'Block'}}</a> 
             </td>
            </tr>
        @endforeach
    </tbody>
</table>
  
{!! $data->render() !!}