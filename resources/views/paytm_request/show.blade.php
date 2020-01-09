@extends('layouts.app')

@section('content')
<style>

#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 100%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}
</style>
  <div class="container">
    <h5>Paytm Request</h5>
    <div class="row">
      <div class="col s12 responsive-table">
        <table class="bordered striped highlight" style="display:table;">
          <tbody>
            <div class="row">
              <div class="col s4">
                <tr>
                  <th>Oreder Id</th>
                  <th>:</th>
                  <td><strong>WER{{str_pad($paytm_request->order_id, 10, 0, STR_PAD_LEFT )}}</strong></td>
                </tr>
              </div> 
              <div class="col s4">
                <tr>
                  <th>ID</th>
                  <th>:</th>
                  <td>{{$paytm_request->user_info->id}}</td>
                </tr>
              </div> 
              <div class="col s4">
                <tr>
                  <th>Name</th>
                  <th>:</th>
                  <td><a href="{{url('/users/'.$paytm_request->user_info->id)}}" target="_blank">{{$paytm_request->user_info->name}}</a></td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Payment Type</th>
                  <th>:</th>
                  <td>{{$paytm_request->payment_type == '1' ? 'PayTm' : 'Google Pay'}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>QR Code</th>
                  <th>:</th>
                  <td>
                    @if(strpos($paytm_request->qr_code, 'walkandearn.tech') !== false) 
                        <img src="{{$paytm_request->qr_code}}" height="150px" width="150px" id="myImg">
                    @else
                    <img src="https://walkearn.s3.ap-south-1.amazonaws.com/{{$paytm_request->qr_code}}" height="150px" width="150px" id="myImg">
                    @endif
                  </td>
                </tr>
              </div>
            </div>
            <div class="row">
              <div class="col s4">
                <tr>
                  <th>Mobile</th>
                  <th>:</th>
                  <td>{{$paytm_request->paytm_mobile_number}}</td>
                </tr>
              </div>
              <div class="col s4">
                <tr>
                  <th>Amount</th>
                  <th>:</th>
                  <td>&#8377;{{$paytm_request->amount}}</td>
                </tr>
              </div>
            </div>
              <div class="col s4">
                <tr>
                  <th>Action</th>
                  <th>:</th>
                  <td>
                    <form class="col s12" action="{{url('/paytm_requests', [$paytm_request->id])}}" method="post" onSubmit="if(!confirm('Are you sure to you confirm?')){return false;}">
                    {{csrf_field()}} {{method_field('PUT')}}
                    <select class="validate" name="status" id="status">
                      <option value="" disabled selected>Choose Status</option>
                      <option value="1" {{$paytm_request->status == 1 ? 'selected' : ''}}>Unpaid</option>
                      <option value="2" {{$paytm_request->status == 2 ? 'selected' : ''}}>Paid</option>
                      <option value="3" {{$paytm_request->status == 3 ? 'selected' : ''}}>Reject</option>
                      <option value="4" {{$paytm_request->status == 4 ? 'selected' : ''}}>Hold</option>
                    </select>
                    <textarea name="remark"></textarea>
                    <button type="submit" class="waves-effect waves-light btn">Update<i class="material-icons right">send</i></button>
                    </form>
                  </td>
                </tr>
              </div>
            </div>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- The Modal -->
<div id="myModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="img01">
  <div id="caption"></div>
</div>
<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the image and insert it inside the modal - use its "alt" text as a caption
var img = document.getElementById("myImg");
var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");
img.onclick = function(){
  modal.style.display = "block";
  modalImg.src = this.src;
  captionText.innerHTML = this.alt;
}

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() { 
  modal.style.display = "none";
}
</script>
@endsection
