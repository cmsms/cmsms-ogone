<?php
#-------------------------------------------------------------------------
# Module: Ogone - Ogone Gateway
# Version: 0.0.1, Jean-Christophe Cuvelier
#

class Ogone extends CMSModule
{
  var $currencies = array(
    'EUR' => 'Euro (€)',
    'GBP' => 'British Pound Sterling (£)',
    'USD' => 'United States dollar ($)'
  );
  
  var $languages = array(
    'en_US' => 'English (US)',
    'fr_BE' => 'French (Belgium)',
    'fr_FR' => 'French (France)',
    'nl_NL' => 'Nederlands (Nederlands)',
    'nl_BE' => 'Nederlands (Belgium)'
  );
  
  public function GetName()             {  return 'Ogone';              }
  public function GetFriendlyName()     {  return 'Ogone';  }
  public function GetVersion()          {  return '0.0.6';              }
  // public function GetHelp()          { return $this->Lang('help');       }
  public function GetAuthor()           {  return 'Jean-Christophe Cuvelier';    }
  public function GetAuthorEmail()      {  return 'cybertotophe@gmail.com';    }
  // public function GetChangeLog()        { return $this->Lang('changelog');    }
  public function IsPluginModule()      {  return true;              }
  public function HasAdmin()            { return true;              }
  public function GetAdminSection()     { return 'extensions';          }
  // public function GetAdminDescription()   { return $this->Lang('admindescription'); }
  public function VisibleToAdminUser()    {   return $this->CheckAccess();          }
  public function CheckAccess($perm = 'Manage Ogone')  {  return $this->CheckPermission($perm);  }

  public function GetDependencies()      {  return array('CMSForms' => '1.0.7');  }
  public function MinimumCMSVersion()    {  return "1.10";  }

  // public function InstallPostMessage()    { return $this->Lang('postinstall');  }
  // public function UninstallPostMessage()    { return $this->Lang('postuninstall');  }
  // public function UninstallPreMessage()   { return $this->Lang('really_uninstall'); }
  public function SetParameters()       {   $this->InitializeFrontend();  }
  
  /**
   * GetEventDescription()
   * If your module can create events, you will need
   * to provide the API with documentation of what
   * that event does. This method wraps up a simple
   * return of the localized description.
   * @param string Eventname
   * @return string Description for event 
   */
   
  function GetEventDescription ( $eventname )
  {
    return $this->Lang('event_info_'.$eventname );
  }
  
  /**
   * GetEventHelp()
   * If your module can create events, you will need
   * to provide the API with documentation of how to
   * use the event. This method wraps up a simple
   * return of the localized description.
   * @param string Eventname
   * @return string Help for event
   */
   
  function GetEventHelp ( $eventname )
  {
    return $this->Lang('event_help_'.$eventname );
  }
  
  function HandlesEvents () {
    return true;
  }
  
  public function InitializeFrontend() {
    $this->RegisterModulePlugin();
  }
  
  public function SetupRoutes()
  {
    $this->RegisterRoute('/ogone\/feedback\/(?P<control>[a-zA-Z0-9_-]+)(\/.*?)?$/', 
    array(
      'action' => 'feedback',
      'returnid' => $this->GetPreference('default_page',  cmsms()->GetContentOperations()->GetDefaultPageID()),
      ));
  }
  
  // OGONE SPECIFIC CODE
  
  public static function getShaAvailable()
  {
    $hash = array();
    $hashs = hash_algos();
    
    if(in_array('sha1', $hashs)) $hash['sha1'] = 'SHA-1';
    if(in_array('sha256', $hashs)) $hash['sha256'] = 'SHA-256';
    if(in_array('sha512', $hashs)) $hash['sha512'] = 'SHA-512';
    
    return $hash;
  }
  
  public static function generateShaSecret()
  {
    return self::generatePassword(32,3);
  }
  
  public static function generatePassword($length=6,$level=2){

     list($usec, $sec) = explode(' ', microtime());
     srand((float) $sec + ((float) $usec * 100000));

     $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
     $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
     $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

     $password  = '';

     for($i = 0;  $i < $length; $i++) {
       $password .= substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);
    }
     
     return $password;
  }
  
  // public function DoEvent($originator, $eventname, $params) {
  //     // var_dump('DoEvent');
  //     $this->Audit( 0, 'Ogone', 'Ogone event');
  //     // if('Ogone' == $originator)
  //     // {
  //     //   if('OrderStatusChange' == $eventname)
  //     //   {
  //     //     if(isset($transaction))
  //     //     {
  //       // $this->AddEventHandler('Ogone', 'OrderStatusChange', false);
  //     //       // echo $transaction->id . ' received feedback for the module ' .$transaction->module_name . ' with the status ' . $transaction->status;
  //     //      
  //     //     }
  //     //   }
  //     // }    
  //   }
}

?>
