<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Reject Reward from Walk & Earn</title>
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
        <p>Rejected!</p>
        <p>PayTm requested Amount of Rs.{{$paytm['amount']}}/- has been rejected and same has been added to your Wallet. {{$paytm['remark']}} Order ID. WER{{str_pad($paytm['order_id'], 10, 0, STR_PAD_LEFT )}}.</p>
        <br/>
        <p>Now join us on Facebook - https://www.facebook.com/walknearn and on Telegram - https://t.me/walknearn</p>
        <br/>
        <p>Feel free to contact us at <b>walkearn2019@gmail.com</b> for any help.</p>
        <p>Regards,</p>
        <p>Team  Walk & Earn</p>
      </div>
    </div>
  </body>
</html>
