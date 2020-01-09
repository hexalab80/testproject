<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>WalknEarn</title>
    <link rel="icon" href="{{asset('admin.png')}}">
    <!--Import Google Icon Font-->
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link rel="stylesheet" href="{{asset('css/materialize.min.css')}}">
      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <link rel="stylesheet" href="{{asset('css/app.css')}}">
      <link rel="stylesheet" href="{{asset('css/jquery.dataTables.min.css')}}">
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/material-design-lite/1.1.0/material.min.css">
      <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.material.min.css">
      <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
      <style>
        /*nav,.btn,.page-footer,.teal,.lighten-1,.lighten-2{ background-color: #ff8000 !important; }*/
       nav,.btn,.page-footer,.teal,.lighten-1,.lighten-2{ background-color: #4290f5 !important; }
      </style>
  </head>
  <body>
    <header>
      <ul id="dropdown1" class="dropdown-content">
        <!-- <li><a href="#">Profile</a></li> -->
        <li> <a href="{{url('change_password')}}">Change Password</a> </li>
        <li>
          <a onclick="document.getElementById('login-form').submit();">Logout</a>
          <form class="" action="{{route('logout')}}" method="post" id="login-form">
            {{csrf_field()}}
          </form>
        </li>
      </ul>
      <nav>
        <div class="nav-wrapper">
          <ul id="nav-mobile" class="right">
            <li><a href="#" class="dropdown-button" data-activates="dropdown1">Settings <i class="material-icons right">arrow_drop_down</i></a></li>
          </ul>
          <ul class="left">
            <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>
          </ul>
        </div>
      </nav>

      

      <ul id="slide-out" class="side-nav fixed">
        <li><div class="user-view">
          <div class="background">
            <img src="https://materializecss.com/images/office.jpg">
          </div>
          <a href="#!user"><img class="circle" src="{{asset('walkandearnicon.png')}}"></a>
          <a href="#!name"><span class="white-text name">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</span></a>
          <a href="#!email"><span class="white-text email">{{Auth::user()->email}}</span></a>
        </div></li>

        <li><div class="divider"></div></li>
        <li><a class="subheader">Navigation</a></li>
          <li class="no-padding dashboardlink">
            <ul class="collapsible collapsible-accordion">
              <li><a href="{{url('/')}}" class="collapsible-header">Dashboard<i class="material-icons left">home</i></a></li> 
              <li><a href="{{url('/chart')}}" class="collapsible-header">Graph Report<i class="material-icons left">view_column</i> </a> </li>
              <li><a href="{{url('/predictions')}}" class="collapsible-header">Prediction Report<i class="material-icons left">view_column</i> </a> </li>
              <li><a href="{{url('/ajax-pagination')}}" class="collapsible-header">User Management<i class="material-icons left">group</i></a></li>
              <li><a href="{{url('/challenges')}}" class="collapsible-header">Challenge<i class="material-icons left">accessibility</i> </a> </li>
              <li><a href="{{url('/broadcasts')}}" class="collapsible-header">Broadcast<i class="material-icons left">cloud</i> </a> </li>
              <li><a href="{{url('/paytm_requests')}}" class="collapsible-header">Paytm Request<i class="material-icons left">attach_money</i> </a> </li>
              <li><a href="{{url('/hold_requests')}}" class="collapsible-header">Paytm Hold Request<i class="material-icons left">attach_money</i> </a> </li>
              <li><a href="{{url('/transactions')}}" class="collapsible-header">Transaction History<i class="material-icons left">monetization_on</i> </a> </li>
              <li><a href="{{url('/paidAmount')}}" class="collapsible-header">Amount Paid History<i class="material-icons left">business_center</i> </a> </li>
              <li><a href="{{url('/pendingAmount')}}" class="collapsible-header">Amount Pending History<i class="material-icons left">business_center</i> </a> </li>
              <li><a href="{{url('/settings/1')}}" class="collapsible-header">Settings<i class="material-icons left">settings</i> </a> </li>
            </ul>
          </li>
      </ul>

    </header>
    <main>
      @yield('content')
      <div class="modal" id="deletemodel">
        <div class="modal-content">
          <p>Are you sure want to delete? </p>
          <form action="" method="post">
            {{csrf_field()}} {{method_field('DELETE')}}
            <button type="submit" class="waves-effect waves-green btn-flat">Yes</button>
            <button type="button" class="modal-action modal-close waves-effect waves-red btn-flat">No</button>
          </form>
        </div>
      </div>
    </main>
    <footer class="page-footer">
      <div class="container">
        <a href="#" class="white-text">WalknEarn disclaimer</a> ||
        <a href="{{url('android-terms')}}"  target="_blank" class="white-text">Terms and conditions</a>
        <p>&copy; 2019 WalknEarn.</p>
      </div>
    </footer>
    <!--Import jQuery before materialize.js-->

    <script type="text/javascript" src="{{asset('js/materialize.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.material.min.js">

    </script>
    <script type="text/javascript">
    $.extend( true, $.fn.dataTable.defaults, {
        "paging": true
    } );
    $(document).ready(function() {
      $('#example,#example2').DataTable( {
          columnDefs: [
              {
                  className: 'mdl-data-table__cell--non-numeric'
              }
          ]
      } );
      $('#example1').DataTable( {
            columnDefs: [
                {
                    className: 'mdl-data-table__cell--non-numeric'
                }
            ]
        } );

    } );

    </script>
  </body>
</html>
