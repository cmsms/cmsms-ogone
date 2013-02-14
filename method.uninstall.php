<?php
if(!cmsms()) exit;
if (! $this->CheckAccess()) // Restrict to admin panel and groups with permission
{
	return $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	exit;
}

// Typical Database Removal
$db =& cms_utils::get_db();
	
// remove the database table
$dict = NewDataDictionary( $db );
$sqlarray = $dict->DropTableSQL( cms_db_prefix() . 'module_ogone_transactions' );
$dict->ExecuteSQLArray($sqlarray);

$this->RemovePermission();
$this->RemovePreference();


$this->Audit( 0, $this->GetFriendlyName(), $this->Lang('uninstalled'));