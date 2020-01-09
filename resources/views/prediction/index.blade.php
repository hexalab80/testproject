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
        <div class="col s6">
          <h5>Prediction Report</h5>
        </div>
        <div class="col s6" style="float: right; padding-left: 234px;">
          <!-- <h5> Users Count : {{$user_count}}</h5> -->
        </div>   
      </div>
    </div>

    <div class="row section">
      <div class="col s6">       
        @if($user_count > 9)
          <table class="responsive-table">
            <thead>
              <tr>
                  <th>User Count</th>
                  <th>20% of User Count</th>
                  <th></th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td>{{$user_count}}</td>
                <td>{{$user_cutoff}} </td>
                <!-- <form class="" action="{{url('/predictions')}}" method="post"> --><!-- 
                <td><input type="text" name="bucket_amt" value="{{$user_cutoff *3}}"></td> -->
                <td>

                  
                 
                  <input type="hidden" name="user_count" value="{{$user_count}}">
                  <input type="hidden" name="user_cutoff" value="{{$user_cutoff}}">
                  <a href="{{url('predictions')}}" class="waves-effect waves-light btn">Run</a>
                  <!-- <input type="hidden" name="bucket_amt" value="{{$setting->bucket_amt}}"> -->
                  <!-- <button type="submit" class="waves-effect waves-light btn">Run</button> -->
                  
                </td><!-- 
                </form> -->
              </tr>
            </tbody>
          </table> 
        @endif
      </div>
      <div class="col s6"> 
        <h5 style="margin-top: 7px;">Expected Bucket Amount</h5>
        <!-- <form class="" action="{{url('/predictions')}}" method="post"> -->
          <form class="" action="{{url('/predictions/sentotp')}}" method="post">
        {{csrf_field()}} 
        <div class="row">
          <div class="col s12 m6">
            <div class="input-field col s8">
              <input type="text" name="bucket_amt" value="{{old('bucket_amt') ? old('bucket_amt') : 2*$user_cutoff}}" placeholder="Enter Bucket Amount">
              <label for="category">Bucket Amount</label>
              @if($errors->has('bucket_amt'))
              <div class="red-text">
                {{$errors->first('bucket_amt')}}
              </div>
              @endif
            </div>
          </div>
          <div class="col s12 m4">
            <div class="input-field col s12">
                 <input type="hidden" name="user_count" value="{{$user_count}}">
                  <input type="hidden" name="user_cutoff" value="{{$user_cutoff}}">
                  <!-- <button type="button" class="waves-effect waves-light btn" onclick="OtpPopup();">Send Otp<i class="material-icons right">send</i></button> -->
              @if(empty($prediction))
              <button type="submit" class="waves-effect waves-light btn">Submit<i class="material-icons right">send</i></button>
              @elseif($prediction->otp != null)
              <a href="javascript:void(0);" onclick="OtpPopup('{{$prediction->id}}','predictions');">Verify OTP</a>
              @else
               <p style="color: red;">Today you sent it.</p>
              @endif
            </div>
          </div>
        </div>
      </form>
      </div>
    </div>
    <?php /*/
    <div class="row section">
      <div class="col s12"> 
        <h5>Expected Bucket Amount</h5>
        <!-- <form class="" action="{{url('/predictions')}}" method="post"> -->
          <form class="" action="{{url('/predictions/sentotp')}}" method="post">
        {{csrf_field()}} 
        <div class="row">
          <div class="col s12 m3">
            <div class="input-field col s12">
              <input type="text" name="bucket_amt" value="{{old('bucket_amt') ? old('bucket_amt') : 2*$user_cutoff}}" placeholder="Enter Bucket Amount">
              <label for="category">Bucket Amount</label>
              @if($errors->has('bucket_amt'))
              <div class="red-text">
                {{$errors->first('bucket_amt')}}
              </div>
              @endif
            </div>
          </div>
          <div class="col s12 m4">
            <div class="input-field col s12">
                 <input type="hidden" name="user_count" value="{{$user_count}}">
                  <input type="hidden" name="user_cutoff" value="{{$user_cutoff}}">
                  <!-- <button type="button" class="waves-effect waves-light btn" onclick="OtpPopup();">Send Otp<i class="material-icons right">send</i></button> -->
              @if(empty($prediction))
              <button type="submit" class="waves-effect waves-light btn">Submit<i class="material-icons right">send</i></button>
              @elseif($prediction->otp != null)
              <a href="javascript:void(0);" onclick="OtpPopup('{{$prediction->id}}','predictions');">Verify OTP</a>
              @else
               <p style="color: red;">Today you sent it.</p>
              @endif
            </div>
          </div>
        </div>
      </form>
      </div>
    </div>
    */ ?>

    <div class="row section">
      <div class="col s12">
        <table class="striped bordered highlight responsive-table mdl-data-table__cell--non-numeric" id="example" style="display:table;">
          <thead class="teal white-text">
            <tr>
              <th>S No.</th>
              <th>Date</th>
              <th>User Count</th>
              <th>CutOff User</th>
              <th>Bucket Amt</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            @foreach($predictions as $key => $predict)
            <tr>
              <td>{{++$key}}</td>
              <td>{{$predict->date}}</td>
              <td>{{$predict->user_count}}</td>
              <td>{{$predict->user_cutoff}}</td>
              <td>{{$predict->bucket_amt}}</td>
              <td>{{$predict->created_at}}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Otp modal-->
  <div class="modal" id="otpmodal">
    <div class="modal-content">
      <p>Otp Verification</p>
      <form action="" method="post">
         {{csrf_field()}} 
        <input type="text" name="otp" id="otp" value=""  onblur="checkOtp('predictions');" placeholder="Enter Otp" maxlength="4" class="Number">
           <input type="hidden" name="user_count" value="{{isset($prediction->user_count) ? $prediction->user_count:''}}">
           <input type="hidden" name="user_cutoff" value="{{ isset($prediction->user_cutoff) ? $prediction->user_cutoff : ''}}">
           <input type="hidden" name="bucket_amt" value="{{isset($prediction->bucket_amt) ? $prediction->bucket_amt:''}}">
        <button type="submit" class="waves-effect waves-green btn-flat" id="otp_submit" style="background-color: green;" disabled="disabled">Yes</button>
        <!-- <button type="button" class="modal-action modal-close waves-effect waves-red btn-flat" style="background-color: red;">No</button> -->
      </form>
    </div>
  </div>
  <script type="text/javascript">
    function OtpPopup(id,url){
      $('#otpmodal').modal('open');

      //$err=0;
      // if($('#otp').val() ==''){
      //   alert('Please fill the OTP.');
      //   $err=1;
      // }
      // if($('#otp').length !=4){
      //   alert('OTP is not valid.');
      //   $err=1;
      // }
      // if($err==0){
      //   $('#otpmodal form').attr('action', url);
      // }
    }

    function checkOtp(url){
      $err=0;
      if($('#otp').val() ==''){
        alert('Please fill the OTP.');
        $err=1;
      }

      if($err==0){
        $('#otp_submit').removeAttr("disabled");
        $('#otpmodal form').attr('action', url);
      }
    }
  </script>
@endsection
