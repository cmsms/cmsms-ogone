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


echo hash('sha512', 'TEST');

echo $form->render();
?>
<div style="float:right;"><?= $this->CreateLink($id,'templates',$returnid, 'Templates');?></div>