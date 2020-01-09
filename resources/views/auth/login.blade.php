@extends('layouts.login_layout')

@section('content')
<div class="container">
  <form class="" action="{{route('login')}}" method="post">
    {{csrf_field()}}
    <div class="row section">
      <div class="col s12 m3"></div>
      <div class="col s12 m6">
        <div class="card">
          <div class="card-title  lighten-2 white-text  padding-15">
            <center><span class="small">SIGN IN TO WALKnEARN <br><small>Please sign in to WalknEarn dashboard</small></span></center>
          </div>
          <div class="card-content">
            <div class="row">
              <div class="col s12">
                <div class="input-field col s12">
                  <input type="email" name="email" id="email" value="{{old('email')}}">
                  <label for="email">Email</label>
                  @if($errors->has('email'))
                  <strong class="red-text">{{$errors->first('email')}}</strong>
                  @endif
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col s12">
                <div class="input-field col s12">
                  <input type="password" name="password" id="password" value="{{old('password')}}">
                  <label for="password">Password</label>
                  @if($errors->has('password'))
                  <strong class="red-text">{{$errors->first('password')}}</strong>
                  @endif
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col s12">
                <div class="input-field col s12">
                  <button type="submit" class="waves-effect waves-light btn"><i class="material-icons right">send</i>Submit</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col s12 m3"></div>
    </div>
  </form>
</div>
@endsection
