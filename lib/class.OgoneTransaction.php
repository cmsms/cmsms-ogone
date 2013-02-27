<?php

class OgoneTransaction {
  
  protected $id; // ORDERID in Ogone
  protected $created_at;
  protected $udpated_at;
  private $sha_in;
  private $sha_out;
  private $algorithm = 'sha1';
  
  protected $vars = array();
  protected $is_modified;
  
  public static $languages = array('en_US' => 'English', 'ar_AR' => 'Arabic','cs_CZ' => 'Czech','dk_DK' => 'Danish','de_DE' => 'German','el_GR' => 'Greek','es_ES' => 'Spanish','fi_FI' => 'Finnish','fr_FR' => 'French','he_IL' => 'Hebrew','hu_HU' => 'hungarian','it_IT' => 'Italian','ja_JP' => 'Japanese','ko_KR' => 'Korean','nl_BE' => 'Flemish','nl_NL' => 'Dutch','no_NO' => 'Norwegian','pl_PL' => 'Polish','pt_PT' => 'Portugese','ru_RU' => 'Russian','se_SE' => 'Swedish','sk_SK' => 'Slovak','tr_TR' => 'Turkish','zh_CN' => 'Simplified Chinese');
  
  public static $currencies = array('AED', 'ANG', 'ARS', 'AUD', 'AWG', 'BGN', 'BRL', 'BYR', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK', 'EEK', 'EGP', 'EUR', 'GBP', 'GEL', 'HKD', 'HRK', 'HUF', 'ILS', 'ISK', 'JPY', 'KRW', 'LTL', 'LVL', 'MAD', 'MXN', 'NOK', 'NZD', 'PLN', 'RON', 'RUB', 'SEK', 'SGD', 'SKK', 'THB', 'TRY', 'UAH', 'USD', 'XAF', 'XOF', 'XPF', 'ZAR');
  
  private static $ogone_fields = array(
    'general' => array('PSPID', 'ORDERID', 'AMOUNT', 'CURRENCY', 'LANGUAGE', 'CN', 'EMAIL', 'OWNERZIP', 'OWNERADDRESS', 'OWNERCTY', 'OWNERTOWN', 'OWNERTELNO'),
    'security' => array('SHASIGN'),
    'layout' => array('TITLE', 'BGCOLOR', 'TXTCOLOR', 'TBLBGCOLOR', 'TBLTXTCOLOR', 'BUTTONBGCOLOR', 'BUTTONTXTCOLOR', 'LOGO', 'FONTTYPE'),
    'post_payment' => array('ACCEPTURL', 'DECLINEURL', 'EXCEPTIONURL', 'CANCELURL'),
    'extra' => array('CARDNO')
  );
  
  private static $fields = array(
    'module_name',
    'module_order_id',
    'amount',
    'currency',
    'language',
    'cn',
    'email',
    'ownerzip',
    'owneraddress',
    'ownercty',
    'ownertown',
    'ownertelno',
    'status',
    'ogone_feedback'
  );
  
  private $form_fields;
  
  const DB_NAME = 'module_ogone_transactions';
  
  public static $db_schema = array(
    'id I KEY AUTO',
    'created_at DT',
    'updated_at DT',
    
    'module_name C(255)',
    'module_order_id C(255)',
    
    'amount I',
    'currency C(3)',
    'language C(5)',
    'cn C(35)',
    'email C(50)',
    'ownerzip C(10)',
    'owneraddress C(35)',
    'ownercty C(2)',
    'ownertown C(40)',
    'ownertelno C(30)',			
    
    'status C(255)',
    'ogone_feedback X'
  );
  
  public static $error_codes = array(
    0 => 'Incomplete or invalid',
    1 => 'Cancelled by client',
    2 => 'Authorisation refused',
    4 => 'Order stored',
    41 => 'Waiting client payment',
    5 => 'Authorised',
    51 => 'Authorisation waiting',
    52 => 'Authorisation not known',
    59 => 'Author. to get manually',
    6 => 'Authorised and canceled',
    61 => 'Author. deletion waiting',
    62 => 'Author. deletion uncertain',
    63 => 'Author. deletion refused',
    7 => 'Payment deleted',
    71 => 'Payment deletion pending',
    72 => 'Payment deletion uncertain',
    73 => 'Payment deletion refused',
    74 => 'Payment deleted (not accepted)',
    75 => 'Deletion processed by merchant',
    8 => 'Refund',
    81 => 'Refund pending',
    82 => 'Refund uncertain',
    83 => 'Refund refused',
    84 => 'Payment declined by the acquirer (will be debited)',
    85 => 'Refund processed by merchant',
    9 => 'Payment requested',
    91 => 'Payment processing',
    92 => 'Payment uncertain',
    93 => 'Payment refused',
    94 => 'Refund declined by the acquirer',
    95 => 'Payment processed by merchant',
    97 => 'Being processed (intermediate technical status)',
    98 => 'Being processed (intermediate technical status)',
    99 => 'Being processed (intermediate technical status)'
  );
  
  public function __set($var, $val) {
    $this->is_modified = true;
    $this->vars[$var] = $val;
  }

  public function __get($var) {
    try
    {
      if(method_exists($this, $var))
      {
        return $this->$var();
      }
      elseif (array_key_exists($var, $this->vars))
      {
        return $this->vars[$var];
      }
      else 
      {
        //throw new Exception("Property $var does not exist");
      }
    }
    catch(Exception $e)
    {
      echo 'Error: ',  $e->getMessage(), "\n";
    }
  }
  
  // FUNCTIONNALITIES
  
  public function setShaIn($sha_in)
  {
    $this->sha_in = $sha_in;
  }

  public function setShaOut($sha_out)
  {
    $this->sha_out = $sha_out;
  }
  
  public function setAlgorithm($algorithm)
  {
    switch($algorithm)
    {
      case 'sha512':
        $this->algorithm = 'sha512';
        break;
      case 'sha256':
        $this->algorithm = 'sha256';
        break;
      default:
        $this->algorithm = 'sha1';
        break;
    }
  }
  
  public function getOrderId()
  {
    return $this->id;
  }
  
  public static function prepareTransaction($module_name, $module_order_id, $amount, $currency = 'EUR', $language = 'en_US')
  {
    $transaction = new self();
    
    $transaction->module_name = $module_name;
    $transaction->module_order_id = $module_order_id;
    $transaction->amount = $amount;
    $transaction->currency = $currency;
    $transaction->language = $language;
    
    $transaction->status = 'initialized';
    
    $transaction->save();
    
    return $transaction;
  }
  
  public function prepareForm(&$form)
  {
    $this->prepareFields();
    
    foreach($this->form_fields as $name => $value)
    {
      $form->setWidget($name, 'hidden', array('value' => $value));
    }
    
    // var_dump($this->form_fields);
    
    // DO THE CALLBACK URLS
    
    // DO THE SHA-IN
    
    $form->setWidget('SHASIGN', 'hidden', array('value' => $this->calculateDigest()));
    
    return $form;
  }
  
  public function calculateDigest($string = null)
  {
    if(is_null($string))
    {
      $string = $this->calculateStringToHash();
    }
    // var_dump($this->algorithm);
    return strtoupper(hash($this->algorithm, $string));
  }
  
  public function calculateStringToHash()
  {
    $string = '';
    foreach($this->form_fields as $name => $value)
    {
      $string .= $name . '=' . $value . $this->sha_in;
    }
    return $string;
  }
  
  private function prepareFields()
  {
    foreach(self::$ogone_fields as $group => $fields)
    {
      foreach($fields as $field)
      {
        $field_lower = strtolower($field);
        if($this->$field)
        {
          $this->form_fields[$field] = (string) $this->$field;
        }
        elseif($this->$field_lower)
        {
          $this->form_fields[$field]  = (string) $this->$field_lower;
        }
        elseif('ORDERID' == $field)
        {
          $this->form_fields[$field] = (string) $this->id;
        }
      }
    }
    
    ksort($this->form_fields);
  }
  
  public function analyseFeedback($request)
  {
    $request = self::cleanRequest($request);
    
    $original_digest = $request['SHASIGN'];
    unset($request['SHASIGN']);
    
    $string = '';
    $feedback = '';
    
    foreach($request as $name => $value)
    {
      $entry = $name . '=' . $value;
      $feedback .= $entry . "\r\n";
      $string .= $entry . $this->sha_out;
    }
    
    $this->ogone_feedback = $feedback;
    
    $new_digest = $this->calculateDigest($string);
    
    if($original_digest == $new_digest)
    {
      if(isset(self::$error_codes[$request['STATUS']]))
      $this->status = self::$error_codes[$request['STATUS']];
      $this->save(); 
      return true;
    }
    else
    {
      $this->save(); 
      echo "Invalid transaction " . $request['ORDERID'];
      return false;
    }
    
   
  }
  
  private static function cleanRequest($request)
  {
    unset($request['page']); // CMSMS PARAM
    
    $vars = array();
    
    foreach($request as $name => $value)
    {
      $vars[strtoupper($name)] = $value;
    }
    
    ksort($vars);
    
    return $vars; // TODO: ONLY KEEP VALID KEYS
  }
  
  // STATUS: initialized, paid, refused, cancelled
  
  // DATABASE
    
  public static function retrieveByPk($id)  {
    return self::doSelectOne(array('where' => array('id' => (int)$id)));
  }

  public static function doSelectOne($params = array()) {
    $params['limit'] = 1;
    $items = self::doSelect($params);
    reset($items);
    return current($items);
  }
  
  public static function doSelect($params = array()) {
    
    $query = 'SELECT * FROM '.cms_db_prefix().self::DB_NAME;
    
    $values = array();
    
    if(isset($params['where']) && is_array($params['where']))
    {
      $fields = array();
      foreach($params['where'] as $field => $value)
      {
        $fields[] = $field . '= ?';
        $values[] = $value;
      }
      $query .= ' WHERE ' . implode(' AND ', $fields);
    }
    
    if(isset($params['order_by']))
    {
     $query .= ' ORDER BY ' . implode(', ' , $params['order_by']);
    }
    
    if(isset($params['limit']))
    {
      $query .= ' LIMIT ' . (int)$params['limit'];
    }
    
    $db = cms_utils::get_db();
    $dbresult = $db->Execute($query, $values);
    $items = array();

    if ($dbresult && $dbresult->RecordCount() > 0)
     {
       while ($dbresult && $row = $dbresult->FetchRow())
       {	
         $item = new self();
         $item->populate($row);
         if(isset($params['with_id']))
         {
           $items[$row['id']] = $item;
         }
         else
         {
           $items[] = $item;
         }
       }
     }

     return  $items;
  }
  
  public function populate($row)  {
    $this->id = $row['id'];    
    $this->created_at = $row['created_at'];
    $this->updated_at = $row['updated_at'];
    
    foreach(self::$fields as $field)
    {
      $this->$field = (isset($row[$field]))?$row[$field]:null;
    }
  }
  
  // public function toArray() {
  //     return array(
  //       'id' => $this->id,
  //       'created_at' => $this->created_at,
  //       'updated_at' => $this->updated_at,
  //       'collection_id' => $this->collection_id,
  //       'position' => $this->position,
  //       'title' => $this->title,
  //       'filename' => $this->filename,
  //       'original_filename' => $this->original_filename,
  //       'filename_url' => $this->getUrl()
  //     );
  //   }
  
  // public static function itemsToArray(Array $list) {
  //     $array = array();
  //     foreach($list as $item)
  //     {
  //       $array[$item->id] = $item->toArray();
  //     }
  //     return $array;
  //   }
  
  public function save($params = array()) {
    if($this->id && !isset($params['force_insert'])){
      $this->update($params);
    } else {
      $this->insert($params);
    }
    return true;
  }
  
  protected function insert($params = array()) {
    
    $db = cms_utils::get_db();
    
    $query = 'INSERT INTO '.cms_db_prefix().self::DB_NAME . '
      SET
        created_at = NOW(),
        updated_at = NOW()';

    $values = array();        
    
    foreach(self::$fields as $field)
    {
      $query .= ', ' . $field . ' = ?';
      $values[] = $this->$field;
    }

        
    if(isset($this->id) && !is_null($this->id) && isset($params['force_insert']))
    {
      $query .= ', id = ?';
      $values[] = $this->id;
    }
        
    $db->Execute($query, $values);

    $this->id = $db->Insert_ID();
    return true;
  }

  protected function update($params = array()) {
    $db = cms_utils::get_db();
    if(isset($params['frontend']))
    {
      $userid = null;
    }
    else
    {
      $userid = get_userid();
    }
    $query = 'UPDATE '.cms_db_prefix().self::DB_NAME. '
      SET 
        updated_at = ?';
    
    if(isset($params['no_time_increment']))
    {
      $values = array($this->updated_at);
    }
    else
    {
      $values = array(date('Y-m-d H:i:s'));
    }
    
    foreach(self::$fields as $field)
    {
      $query .= ', ' . $field . ' = ?';
      $values[] = $this->$field;
    }
    
    $query .= ' WHERE id = ?';
    $values[] = $this->id;
    
    // UPDATE
    
    $db->Execute($query, $values);

    return true;
  }
  
  public function delete() {
    if ($this->id) {
      $query = 'DELETE FROM ' . cms_db_prefix() . self::DB_NAME .' WHERE id = ?';
      $db = cms_utils::get_db();
      $db->Execute($query, array($this->id));       
    }
    return true;
  }
  
  
}