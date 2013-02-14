<?php
if (!cmsms()) exit;

if(isset($params['prod']))
{
  $env = 'prod';
}
else
{
  $env = 'test'; // By default, we are in TEST mode
}

$form_url = 'https://secure.ogone.com/ncol/'.$env.'/orderstandard.asp';
  
  
?>

<form method="post" action="https://secure.ogone.com/ncol/test/orderstandard.asp" id=form1
name=form1>
<!-- general parameters -->
<input type="hidden" name="PSPID" value="">
<input type="hidden" name="ORDERID" value="">
<input type="hidden" name="AMOUNT" value="">
<input type="hidden" name="CURRENCY" value="">
<input type="hidden" name="LANGUAGE" value="">
<input type="hidden" name="CN" value="">
<input type="hidden" name="EMAIL" value="">
<input type="hidden" name="OWNERZIP" value="">
<input type="hidden" name="OWNERADDRESS" value="">
<input type="hidden" name="OWNERCTY" value="">
<input type="hidden" name="OWNERTOWN" value="">
<input type="hidden" name="OWNERTELNO" value="">
<!-- check before the payment: see Security: Check before the payment -->
<input type="hidden" name="SHASIGN" value="">
<!-- layout information: see Look and feel of the payment page -->
<input type="hidden" name="TITLE" value="">
<input type="hidden" name="BGCOLOR" value="">
<input type="hidden" name="TXTCOLOR" value="">
<input type="hidden" name="TBLBGCOLOR" value="">
<input type="hidden" name="TBLTXTCOLOR" value="">
<input type="hidden" name="BUTTONBGCOLOR" value="">
<input type="hidden" name="BUTTONTXTCOLOR" value="">
<input type="hidden" name="LOGO" value="">
<input type="hidden" name="FONTTYPE" value="">
<!-- post payment redirection: see Transaction feedback to the customer -->
<input type="hidden" name="ACCEPTURL" value="">
<input type="hidden" name="DECLINEURL" value="">
<input type="hidden" name="EXCEPTIONURL" value="">
<input type="hidden" name="CANCELURL" value="">
<input type="submit" value="" id=submit2 name=submit2>
</form>

