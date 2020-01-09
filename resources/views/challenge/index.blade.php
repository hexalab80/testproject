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
          <h5>Challenge List</h5>
        </div>
        <a href="{{url('challenges/create')}}" class="waves-effect waves-light btn right red"><i class="material-icons left">add</i>Add Challenge</a>
      </div>
    </div>

    <div class="row section">
      <div class="col s12">
        <table class="striped bordered highlight responsive-table mdl-data-table__cell--non-numeric" id="example"style="display:table;">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th>Name</th>
              <th>Time Period(in hrs)</th>
              <th>Entry Fee(in coins)</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($challenges as $key => $challenge)
            <tr>
              <td>{{++$key}}</td>
              <td>{{$challenge->name}}</td>
              <td>{{$challenge->time_period}}</td>
              <td>{{$challenge->entry_fee}}</td>
              <td>{{$challenge->status ==1 ? 'Enabled' : 'Disabled'}}</td>
              <td>{{$challenge->created_at}}</td>
              <td>
                @if($challenge->status ==1)
                <a href="javascript:void(0);" class="waves-effect waves-light btn" alt="Disabled" onclick="chanllengePop('{{$challenge->id}}', '/challenges/{{$challenge->id}}','0');">Disabled</a>
                @else
                <a href="javascript:void(0);" class="waves-effect waves-light btn" alt="Enabled" onclick="chanllengePop('{{$challenge->id}}', '/challenges/{{$challenge->id}}','1');">Enabled</a>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- challenge modal-->
  <div class="modal" id="challengemodel">
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
    function chanllengePop(id,url,status){
      $('#status_v').val(status);
      if(status==1){
        $('#status_mod').html('Are you sure want to disable?');
      }else{
        $('#status_mod').html('Are you sure want to enable?');
      }
      $('#challengemodel').modal('open');
      $('#challengemodel form').attr('action', url);
    }
  </script>
@endsection
