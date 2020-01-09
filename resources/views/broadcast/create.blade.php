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
    <h5>Create Broadcast Message</h5>

    <form class="" action="{{url('/broadcasts')}}" method="post" enctype="multipart/form-data">
      {{csrf_field()}}
      <div class="row">
        <div class="col s12 m12">
          <div class="input-field col s12">
            <textarea name="description" id="description" rows="8" cols="80" class="materialize-textarea">{{old('description') ? old('description') : ''}}</textarea>
            <label for="description">Description</label>
            @if($errors->has('description'))
            <div class="red-text">
              {{$errors->first('description')}}
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col s12">
          <div class="input-field file-field col s12">
            <div class="btn">
              <span>Image</span>
              <input type="file" name="image" accept="image/x-png,image/jpeg" onchange="loadFiles(this, 'div.output')">
            </div>
            <div class="file-path-wrapper">
              <input type="text" name="" value="" class="file-path validate">
            </div>
            @if($errors->has('image'))
            <div class="red-text">
              {{$errors->first('image')}}
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col s12">
          <div class="col s12 output"></div>
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
