<?php

//turn on debugging messages

ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('DATABASE', 'hh292');
define('USERNAME', 'hh292');
define('PASSWORD', 'ic2BQ414k');
define('CONNECTION', 'sql1.njit.edu');

class dbConn{
    //variable to hold connection object.
    protected static $db;
    //private construct - class cannot be instatiated externally.
    private function __construct() {
        try {
            // assign PDO object to db variable
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch (PDOException $e) {
            //Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
        }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection() {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) {
            //new connection object.
            new dbConn();
        }
        //return connection.
        return self::$db;
    }
}
class collection {
    
    protected $html;

    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }

        }
}

class accounts extends collection {
    protected static $modelName = 'account';
}

class todos extends collection {
    protected static $modelName = 'todo';
}


class model {
    protected $tableName;
    public function save()
    {
        if ($this->id = '') {
            $sql = $this->insert();
        } else {
            $sql = $this->update();
        }

        echo $tableName;

        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        $tableName = get_called_class();
        $array = get_object_vars($this);
        $columnString = implode(',', $array);
        $valueString = ":".implode(',:', $array);
        echo "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ") </br>";
        echo 'I just saved record: ' . $this->id;
    }
    private function insert() {
        $sql = 'INSERT';
        return $sql;
    }
    private function update() {
        $sql = 'sometthing';
        return $sql;
        echo 'I just updated record' . $this->id;
    }
    public function delete() {
        echo 'I just deleted record' . $this->id;
    }
}
class account extends model {
}
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public function __construct()
    {
        $this->tableName = 'todos';
    
    }
}
// this would be the method to put in the index page for accounts
$records = todos::findAll();
//print_r($records);
// to print all accounts records in html table  
$html = '<table border = 6><tbody>';

  // Displaying Header Row ...... hh292
  
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';

    // Displayng Data Rows .......hh292
    
    //$i = 0;
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }

    $html .= '</tbody></table>';
    print_r($html);

echo"<br>";

//------------------------------------------------------
// this would be the method to put in the index page for todos
$records = todos::findAll();
//print_r($records);
//this code is used to get one record and is used for showing one record or updating one record

//------------------------------------------------------


// to retrive the selected data 
$record = todos::findOne(4);
//print_r($records);
print_r("Todo table id - 4");

//.$html = '<table border = 6><tbody>';

  // Displaying Header Row ...... hh292
  
  $html .= '<tr>';
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';

    // Displayng Data Rows .......hh292
    
    //$i = 0;
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      //$i++;
    }

    $html .= '</tbody></table>';
    
    print_r($html);

//----------------------------------------------------------
//[1] => todo Object ( [id] => 2 [owneremail] => mjlee@njit.edu [ownerid] => 1 [createddate] => 2017-05-03 00:00:00 [duedate] => 2017-05-27 00:00:00 [message] => new name 2 [isdone] => 0 [tableName:protected] => todos )

$columns = array('id'=> ' ','owneremail'=> 'hh292@njit.edu', 'ownerid'=> '123', 'createddate'=> '2017-06-07 00:01:00', 'duedate'=>'2017-07-10 00:03:00', 'message'=>'Th', 'isdone' => '0');

print_r($columns);
$r = todos::insertRecord($columns);

//this is used to save the record or update it (if you know how to make update work and insert)
// $record->save();
//$record = accounts::findOne(1);
//This is how you would save a new todo item
$record = new todo();

$record->message = 'some taskjhgyg';
$record->isdone = 0;
//$record->save();
//print_r($record);
$record = todos::create();
//print_r($record);