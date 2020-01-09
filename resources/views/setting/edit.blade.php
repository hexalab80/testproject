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
    <h5>Settings</h5>

    <form class="" action="{{url('/settings/1')}}" method="post" enctype="multipart/form-data">
      {{csrf_field()}} {{method_field('PUT')}}
      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <input type="text" name="reward_ads_coin" value="{{old('reward_ads_coin') ? old('reward_ads_coin') : $setting->reward_ads_coin}}" placeholder="Enter Reward Ads Coin">
            <label for="category">Reward Ads Coin</label>
            @if($errors->has('reward_ads_coin'))
            <div class="red-text">
              {{$errors->first('reward_ads_coin')}}
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <input type="text" name="frd_refferal_coin" value="{{old('frd_refferal_coin') ? old('frd_refferal_coin') : $setting->frd_refferal_coin}}" placeholder="Enter Friend Refferal Coins">
            <label for="category">Friend Refferal Coins</label>
            @if($errors->has('frd_refferal_coin'))
            <div class="red-text">
              {{$errors->first('frd_refferal_coin')}}
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <input type="text" name="weekly_redem_coin" value="{{old('weekly_redem_coin') ? old('weekly_redem_coin') : $setting->weekly_redem_coin}}" placeholder="Enter Weekly Coins Limit">
            <label for="category">Weekly Coins Limit</label>
            @if($errors->has('weekly_redem_coin'))
            <div class="red-text">
              {{$errors->first('weekly_redem_coin')}}
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <input type="text" name="reward_ads_time_interval" value="{{old('reward_ads_time_interval') ? old('reward_ads_time_interval') : $setting->reward_ads_time_interval}}" placeholder="Enter Reward Ads Time Interval">
            <label for="category">Reward Ads Time Interval</label>
            @if($errors->has('reward_ads_time_interval'))
            <div class="red-text">
              {{$errors->first('reward_ads_time_interval')}}
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <button type="submit" class="waves-effect waves-light btn">Submit<i class="material-icons right">send</i></button>
          </div>
        </div>
      </div>
    </form>

    <div class="row">
      <div class="col s12">
        <div class="col s12">
          <h5>Setting Log</h5>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col s12">
        <table class="bordered striped highlight table-responsive mdl-data-table__cell--non-numeric" id="example">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th>Reward Ads Coins</th>
              <th>Friend Refferal Coins</th>
              <th>Weekly Coins Limit</th>
              <th>Reward Ads Time Interval</th>
              <th>Created At</th>  
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody>
            @foreach($settingLog as $key => $vv)
            <tr>
              <td>{{++$key}}</td>
              <td>{{$vv->reward_ads_coin}}</td>
              <td>{{$vv->frd_refferal_coin}}</td>
              <td>{{$vv->weekly_redem_coin}}</td>
              <td>{{$vv->reward_ads_time_interval}}</td>
              <td>{{$vv->created_at}}</td>
              <td>{{$vv->updated_at}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
