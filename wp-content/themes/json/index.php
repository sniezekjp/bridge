<?php //require('app/index.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>
  <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="address_override" value="1">
    <input type="hidden" name="cancel_return" value="http://jpapi.dev/?cancel=true">
    <input type="hidden" name="business" value="jpbusiness@jp.com"/>
    <input type="hidden" name="currency_code" value="USD">
    <input type="text" name="item_name_1" value="Registration 1"/>
    <input type="text" name="amount_1" value="160.00"/>
    <input type="text" name="item_name_2" value="Registration 1"/>
    <input type="text" name="amount_2" value="160.00"/>
    <p><input type="text" name="email" value="jsniezek@aol.com"></p>
    <p><input type="text" name="first_name" value="JP"></p>
    <p><input type="text" name="last_name" value="Sniezek"></p>
    <p><input type="text" name="address1" value="1305 Rockland Ter"></p>
    <p><input type="text" name="city" value="McLean"></p>
    <p><input type="text" name="state" value="VA"></p>
    <p><input type="text" name="zip" value="22101"></p>
    <p><input type="text" name="night_phone_a" value="703-556-0334"></p>
    <input type="image" value="submit">
  </form>
</body>
</html>