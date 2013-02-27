<?php
if (!cmsms()) exit;

if(isset($_REQUEST['orderID']))
{
  $transaction = OgoneTransaction::retrieveByPk($_REQUEST['orderID']);
  
  if($transaction)
  {
    $transaction->setShaOut($this->GetPreference('sha-out-test')); // TODO: Switch between prod & test
    $transaction->setAlgorithm($this->GetPreference('hash-algorithm'));

    if($transaction->analyseFeedback($_REQUEST))
    {
      // Transaction success, fire an event with the module name and the status
 			$this->SendEvent('OrderStatusChange', array('transaction' => $transaction));
    }
  }
  else
  {
    echo '<p>An error occurred regarding your transaction (Transaction N°: '.$_REQUEST['orderID'].'). Please contact the support.</p>';
  }
}