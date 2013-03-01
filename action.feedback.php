<?php
if (!cmsms()) exit;

if(isset($_REQUEST['orderID']))
{
  $transaction = OgoneTransaction::retrieveByPk($_REQUEST['orderID']);
  
  if($transaction)
  {
    $transaction->setShaOut($this->GetPreference('sha-out-' . $this->GetPreference('env', 'test')));
    $transaction->setAlgorithm($this->GetPreference('hash-algorithm'));

    if($transaction->analyseFeedback($_REQUEST))
    {
      // Transaction success, fire an event with the module name and the status
 		$this->SendEvent('OrderStatusChange', array('transaction' => $transaction));
		echo "Your payment has been made with the status: " . $transaction->status;
    }
  }
  else
  {
    echo '<p>An error occurred regarding your transaction (Transaction N°: '.$_REQUEST['orderID'].'). Please contact the support.</p>';
  }
}