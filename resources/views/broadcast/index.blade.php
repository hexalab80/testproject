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
      <div class="col s12">
        <div class="col s12">
          <h5>Broadcast List</h5>
        </div>
        <a href="{{url('broadcasts/create')}}" class="waves-effect waves-light btn right red"><i class="material-icons left">add</i>Send Broadcast Message</a>
      </div>
    </div>

    <div class="row section">
      <div class="col s12">
        <table class="striped bordered highlight responsive-table mdl-data-table__cell--non-numeric" id="example"style="display:table;">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th>Image</th>
              <th>Description</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            @foreach($notifications as $key => $notification)
            <tr>
              <td>{{++$key}}</td>
               <td><img src="{{$notification->image}}" height="50px" width="50px" class="img-preview"></td>
              <td>{{$notification->description}}</td>
              <td>{{$notification->created_at}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
