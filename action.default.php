<?php
if (!cmsms()) exit;

if(isset($params['prod']))
{
  $env = 'prod';
}
else
{
  $env = 'test'; // By default, we are in TEST mode
  
  if(!isset($params['transaction_id']))
  {
    // $params['transaction_id'] = 7;
    $test_transaction = OgoneTransaction::prepareTransaction('Ogone', time(), 10000);
    $params['transaction_id'] = $test_transaction->getOrderId();
  }
}

// vaR_dump($_SERVER);

if(isset($params['transaction_id']))
{
  $transaction = OgoneTransaction::retrieveByPk((int) $params['transaction_id']);
  
  if($transaction)
  {
    if($transaction->status == 'initialized')
    {
      $form_url = 'https://secure.ogone.com/ncol/'.$env.'/orderstandard.asp';
      
      $form = new CMSForm($this->getName(), '', 'default', $returnid);
      $form->setActionUrl($form_url);
      $form->setButtons(array('submit'));
      $form->setMethod('post');
      $transaction->PSPID = $this->GetPreference('pspid');
      $transaction->ACCEPTURL = $this->GetPreference('ACCEPTURL');
      $transaction->DECLINEURL = $this->GetPreference('DECLINEURL');
      $transaction->EXCEPTIONURL = $this->GetPreference('EXCEPTIONURL');
      $transaction->CANCELURL = $this->GetPreference('CANCELURL');
      
      if('test' == $env)
      {
        $transaction->CN = 'CMS Made Simple';
        $transaction->CARDNO = '4111111111111111';
      }
      
      $transaction->setPassphrase($this->GetPreference('sha-in-' . $env));
      $transaction->setAlgorithm($this->GetPreference('hash-algorithm'));
      $transaction->prepareForm($form);
      
      // 4111111111111111
      
      echo $form->render();      
    }
    else
    {
      echo 'This transaction ID is not valid';
    }
  }
  else
  {
    echo 'This transaction ID is not valid';
  }
  
}
else
{
  echo 'You must provide a transaction ID';
}


