<?php
if (!isset($gCms)) exit;

	/*---------------------------------------------------------
	   Install()
	   When your module is installed, you may need to do some
	   setup. Typical things that happen here are the creation
	   and prepopulation of database tables, database sequences,
	   permissions, preferences, etc.
	   	   
	   For information on the creation of database tables,
	   check out the ADODB Data Dictionary page at
	   http://phplens.com/lens/adodb/docs-datadict.htm
	   
	   This function can return a string in case of any error,
	   and CMS will not consider the module installed.
	   Successful installs should return FALSE or nothing at all.
	  ---------------------------------------------------------*/
		
		// Typical Database Initialization

		
		// mysql-specific, but ignored by other database
    $taboptarray = array('mysql' => 'TYPE=MyISAM');
		$dict = NewDataDictionary($db);
		
    // table schema description
      //     $flds = array(
      // 'id I KEY AUTO',
      // 'created_at DT',
      //      'updated_at DT',
      //      
      // 'module_name C(255)',
      // 'module_order_id C(255)',
      // 
      // 'amount I',
      // 'currency C(3)',
      // 'language C(5)',
      // 'cn C(35)',
      // 'email C(50)',
      // 'ownerzip C(10)',
      // 'owneraddress C(35)',
      // 'ownercty C(2)',
      // 'ownertown C(40)',
      // 'ownertelno C(30)',      
      // 
      // 'status C(255)'
      // );

    $flds = OgoneTransaction::$db_schema;
    
		// create it. This should do error checking, but I'm a lazy sod.
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().OgoneTransaction::DB_NAME,
				implode(',',$flds), $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$sql = $dict->CreateIndexSQL('module_ogone_transactions_index', cms_db_prefix() . OgoneTransaction::DB_NAME, 'id, module_name,module_order_id');
    $dict->ExecuteSQLArray($sql);		
		
		// permissions
		
		$this->CreatePermission('Manage Ogone', 'Manage Ogone');	
		
		// preferences
		
		$this->SetPreference('pspid', '');
		$this->SetPreference('env', 'test');
		$this->SetPreference('currency', 'EUR');
		$this->SetPreference('language', 'en_US');
		
		$this->SetPreference('hash-algorithm', end(Ogone::getShaAvailable())); // Choose the higest availabe HASH
		
    $this->SetPreference('sha-in-test', Ogone::generateShaSecret());
    $this->SetPreference('sha-out-test', Ogone::generateShaSecret());
    $this->SetPreference('sha-in-prod', Ogone::generateShaSecret());
    $this->SetPreference('sha-out-prod', Ogone::generateShaSecret());
		
		// EVENTS		
		$this->CreateEvent('OrderStatusChange');

		// put mention into the admin log
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
		
?>