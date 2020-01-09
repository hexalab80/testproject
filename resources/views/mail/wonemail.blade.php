<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Won Reward from Walk & Earn</title>
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
        <p>Congratulations!</p>
        <p>Requested Amount of Rs. {{$paytm['amount']}}/- has been successfully credited to your PayTm No. {{$paytm['paytm_mobile_number']}}. Order ID. WER{{str_pad($paytm['order_id'], 10, 0, STR_PAD_LEFT )}}. Walk more and earn more with Walk and Earn. Cheers!</p>
        <br/>
        <p>{{$paytm['remark']}}</p>
        <p>Liked Walk and Earn? Love us back. Leave a five-star review on the Play Store to make it even more awesome.</p>
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
