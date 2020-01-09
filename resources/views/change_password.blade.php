@extends('layouts.app')

@section('content')
  <div class="container">
    <h5>Change Password</h5>
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
      <div class="row section">
      <form class="col s12" action="{{url('/change_password')}}" method="post">
        {{csrf_field()}}

        <div class="row">

        <div class="col s12 m12">
          <div class="input-field col s6">
            <input type="password" name="old_password" id="old_password" value="{{old('old_password') ? old('old_password') : ''}}" class="validate" placeholder="Enter Old Password">
            <label for="first_name">Old Password</label>
            @if($errors->has('old_password'))
            <div class="red-text">
              {{$errors->first('old_password')}}
            </div>
            @endif
          </div>
        </div>
          <div class="col s12 m12">
            <div class="input-field col s6">
              <input type="password" name="new_password" id="new_password" value="{{old('new_password') ? old('new_password') : ''}}" class="validate" placeholder="Enter New Password">
              <label for="last_name">New Password</label>
              @if($errors->has('new_password'))
              <div class="red-text">
                {{$errors->first('new_password')}}
              </div>
              @endif
            </div>
          </div>
          
          <div class="col s12 m12">
            <div class="input-field col s6">
              <input type="password" name="confirm_password" id="confirm_password" value="{{old('confirm_password') ? old('confirm_password') : ''}}" class="validate" placeholder="Enter Confirm Password">
              <label for="last_name">Confirm Password</label>
              @if($errors->has('confirm_password'))
              <div class="red-text">
                {{$errors->first('confirm_password')}}
              </div>
              @endif
            </div>
          </div>
        </div> 
        <div class="row">
          <div class="col s3">
            <button type="submit" class="waves-effect waves-light btn">Submit <i class="material-icons right">send</i></button>
          </div>
        </div>
      </form>
    </div>
    </div>
  </div>
@endsection
