<?php
if (!cmsms()) exit;
if (!$this->CheckAccess()) {
	return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

$form = new CMSForm($this->getName(), $id, 'defaultadmin', $returnid);
$form->setButtons(array('submit'));
$form->setLabel('submit', $this->lang('save'));

$form->setWidget('pspid', 'text', array('preference' => 'pspid'));

$form->setWidget('hash-algorithm', 'select', array('preference' => 'hash-algorithm', 'values' => Ogone::getShaAvailable()));

$form->setWidget('sha-in-test', 'static', array('preference' => 'sha-in-test'));
$form->setWidget('sha-out-test', 'static', array('preference' => 'sha-out-test'));
$form->setWidget('sha-in-prod', 'static', array('preference' => 'sha-in-prod'));
$form->setWidget('sha-out-prod', 'static', array('preference' => 'sha-out-prod'));


$form->setWidget('ACCEPTURL', 'text', array('preference' => 'ACCEPTURL', 'tips' => '<br />URL of the web page to show the customer when the payment is authorised (status 5), accepted (status 9) or waiting to be accepted (pending status 51 or 91).'));
$form->setWidget('DECLINEURL', 'text', array('preference' => 'DECLINEURL', 'tips' => '<br />URL of the web page to show the customer when the acquirer refuses the authorisation (status 2) up to the maximum authorised number of attempts.'));
$form->setWidget('EXCEPTIONURL', 'text', array('preference' => 'EXCEPTIONURL', 'tips' => '<br />URL of the web page to show the customer when the payment result is uncertain (status 52 or 92). If this field is empty, the customer will be referred to the ACCEPTURL instead.'));
$form->setWidget('CANCELURL', 'text', array('preference' => 'CANCELURL', 'tips' => '<br />URL of the web page to show the customer when he cancels the payment (status 1). If this field is empty, the customer will be redirected to the DECLINEURL instead.'));


// Shared secret
$this->smarty->assign('sha-in-test', $this->GetPreference('sha-in-test'));
$this->smarty->assign('sha-out-test', $this->GetPreference('sha-out-test'));
$this->smarty->assign('sha-in-prod', $this->GetPreference('sha-in-prod'));
$this->smarty->assign('sha-out-prod', $this->GetPreference('sha-out-prod'));

if($form->isPosted())
{
  $form->process();
  return $this->Redirect($id,'defaultadmin',$returnid);
}


// echo hash('sha512', 'TEST');

echo $form->render();
?>
<div style="float:right;"><?= $this->CreateLink($id,'templates',$returnid, 'Templates');?></div>