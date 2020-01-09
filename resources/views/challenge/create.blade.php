@extends('layouts.app')

@section('content')
  <div class="container">
    @if(Session::has('success'))
    <div class="card-panel teal lighten-2">
      {{Session::get('success')}}
      <a onclick="$(this).parent().hide();" class="waves-effect waves-light btn right red lighten-2">&times;</a>
    </div>
    @elseif(Session::has('error'))
    <div class="card-panel red lighten-2 white-text">
      {{Session::get('error')}}
      <a onclick="$(this).parent().hide();" class="waves-effect waves-light btn right teal lighten-2">&times;</a>
    </div>
    @endif
    <h5>Create Challenge</h5>

    <form class="" action="{{url('/challenges')}}" method="post">
      {{csrf_field()}}
      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <input type="text" name="name" value="{{old('name') ? old('name') : ''}}" placeholder="Enter Challenge (1000)" class="Number" maxlength="4">
            <label for="category">Challenge</label>
            @if($errors->has('name'))
            <div class="red-text">
              {{$errors->first('name')}}
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <input type="text" name="time_period" value="{{old('time_period') ? old('time_period') : ''}}" placeholder="Enter Time Period (in hrs)" class="Number" maxlength="2">
            <label for="category">Time Period</label>
            @if($errors->has('time_period'))
            <div class="red-text">
              {{$errors->first('time_period')}}
            </div>
            @endif
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <input type="text" name="entry_fee" value="{{old('entry_fee') ? old('entry_fee') : ''}}" placeholder="Enter Entry Fee (in coins)" class="Number" maxlength="2">
            <label for="category">Entry Fee</label>
            @if($errors->has('entry_fee'))
            <div class="red-text">
              {{$errors->first('entry_fee')}}
            </div>
            @endif
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col s12 m6">
          <div class="input-field col s12">
            <select name="status">
              <option value="1">Enabled</option>
              <option value="0">Disabled</option>
            </select>
            <label for="category">Status</label>
            @if($errors->has('status'))
            <div class="red-text">
              {{$errors->first('status')}}
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
  </div>
@endsection
