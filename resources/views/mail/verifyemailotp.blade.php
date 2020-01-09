<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Otp Verification</title>
    <style media="screen">
    .template1{
      margin-top: 10%;
      margin-left: 20%;
      margin-right: 20%;
      margin-bottom: 10%;
    }
    .border{
      border: 1px solid #ccc;
      padding: 5%;
    }
    p{
      font-size: 14px;
    }
    @media screen and (max-width: 720px){
      .template1{
        margin: 0;
      }
    }
    </style>
  </head>
  <body>
    <div class="template1">
      <div class="border">
        <p>Dear {{$user['name']}},</p>
        <p>Your one time password(OTP) is: {{$otp}}</p>
        <p>Feel free to contact us at <b>walkearn2019@gmail.com</b> for any help.</p>
        <p>Regards,</p>
        <p>Team  Walk & Earn</p>
      </div>
    </div>
  </body>
</html>
