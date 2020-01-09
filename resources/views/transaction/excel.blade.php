<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <table>
      <thead>
        <tr>
          <th>S No.</th>
          
          <th>Amount</th>
          <th>Mobile</th>  
          <th>Datetime</th>
          <th>Status</th> 
        </tr>
      </thead>
      <tbody>
      @foreach($paytm_requests as $key => $vv)
        <tr>
          <td>{{++$key}}</td>
          <td>{{$vv->amount}}</td>
          <td>{{$vv->paytm_mobile_number}}</td>
          <td>{{$vv->datetime}}</td>
          <td>{{$vv->status==2 ? 'Paid': 'Reject'}}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </body>
</html>
