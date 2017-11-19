<?php
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'hh292');
define('USERNAME', 'hh292');
define('PASSWORD', 'ic2BQ414k');
define('CONNECTION', 'sql1.njit.edu');


class Manage {
    public static function autoload($class) {
        //you can put any file name or directory here
        include $class . '.php';
    }
}
spl_autoload_register(array('Manage', 'autoload'));


$obj=new displayHtml;
//$obj=new model;
$obj=new main();

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
        //
        //return connection.
        return self::$db;
    }
}
abstract class collection {

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

        //print_r($recordsSet);
        return $recordsSet[0];
    }
}

class accounts extends collection {
    protected static $modelName = 'account';
}

class todos extends collection {
    protected static $modelName = 'todo';
}



abstract class model {
//-----------------
protected $tableName;
public function save()
    
    {
        if ($this->id != '') {
            $sql = $this->update();
        } else {
           $sql = $this->insert();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
        foreach (array_flip($array) as $key=>$value){
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
        $id = $db->lastInsertId();
        return $id;

    }
    private function insert() {

        
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        //print_r($columnString);
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }
    private function update() {

        
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value){
            if( ! empty($value)) {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
            }
        }
        $sql .= ' WHERE id='.$this->id;
        return $sql;
    }
    
    public function delete() {

echo"In delete";
        $db = dbConn::getConnection();
        $modelName=get_called_class();
       // $modelName=static::$modelName;

        $tableName = $modelName::getTablename();


        $sql = 'DELETE FROM '.$tableName.' WHERE id ='.$this->id;
        //print_r($id);  //print_r($this.id)
        $statement = $db->prepare($sql);
        //print_r($sql);
        $statement->execute();
    }
}

    
//---------------------------
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
        $tableName='accounts';
        return $tableName;
    }
}
//-----------------------------------
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
        $tableName='todos';
        return $tableName;
    }
} 



class main
{

   public function __construct()
  {
     
     //--------------- Accounts Table-------------------------hh292

     //-------------------------- Find All -------------------hh292
     $records = accounts::findAll();
         // Display Function : Html Table display

     $html = displayHtml::tableDisplayFunction($records);
     
     print_r($html);

//--------------------------- Find Unique Record---------------hh292

$records = accounts::findOne(4);
$html = displayHtml::tableDisplayFunction_1($records);
print_r("Todo table id - 4");
print_r($html);

//-------------------------- Insert Record---------------------hh292
echo "<h2>Insert One Record</h2>";
$record = new account();
$record->email="testnjit.edu";
$record->fname="hh";
$record->lname="hhhh";
$record->phone="66697";
$record->birthday="00-00-0000";
$record->gender="male";
$record->password="12345";
$lastInsertedId=$record->save();
$records = accounts::findAll();

$html = displayHtml::tableDisplayFunction($records);
echo "After inserting";
print_r($html);

//-----------------------------Update Record-------------------hh292

echo "<h2>Update Record</h2>";
echo "Updating the ";
//$id=30;
$records = accounts::findOne($lastInsertedId);
$record = new account();
$record->id=$records->id;
$record->fname="fname_Update";
$record->lname="lname_Update";
$record->gender="gender_Update";
$record->save();
$records = accounts::findAll();
echo "<h3>Record update with id: ".$records->id."</h3>";
        
$html = displayHtml::tableDisplayFunction($records);
 
print_r($html);

//------------------------- Delete Record -------------------hh292
echo "<h2>Delete One Record</h2>";
print_r($lastInsertedId);
$records = accounts::findOne($lastInsertedId);
$record= new account();
$record->id=$records->id;
echo "<br>";
//print_r($records);
$records->delete();
echo '<h3>Record with id: '.$records->id.' is deleted</h3>';
echo "After Delete";
$records = accounts::findAll();

$html = displayHtml::tableDisplayFunction($records);
echo "<h3>After Deleteing</h3>";
print_r($html);

//------------------End Of Account Table -----------------------hh292

//--------------- Todo Table-------------------------hh292

 $records = todos::findAll();
 echo "--------------- Todo Table-----------------------<br><br>";
 $html = displayHtml::tableDisplayFunction($records); 
 print_r($html);

//------------------Find Unique id-------------------hh292
$records = todos::findOne(4);
$html = displayHtml::tableDisplayFunction_1($records);
print_r("Todo table id - 4");
print_r($html);

//-------------------------Insert Record-----------------hh292
   echo "<h2>Insert One Record</h2>";
        $record = new todo();
        $record->owneremail="hh292@njit.edu";
        $record->ownerid=06;
        $record->createddate="11-05-2017";
        $record->duedate="11-13-2017";
        $record->message="Active record Assignment";
        $record->isdone=1;
        $lastInsertedId=$record->save();
        $records = todos::findAll();
        echo"<h3>After Inserting</h3>";
 
    $html = displayHtml::tableDisplayFunction($records);
//echo "<h3>After Inserting</h3>";
print_r($html);

//------------------------Update Record--------------------hh292 

echo "<h2>Update Record</h2>";
//$id=30;
$records = todos::findOne($lastInsertedId);
$record = new todo();
$record->id=$records->id;
$record->owneremail="updatetest@njit.edu";
$record->message="Update Active ";
$record->save();

$records = todos::findAll();
echo "<h3>Record update with id: ".$records->id."</h3>";
        
$html = displayHtml::tableDisplayFunction($records);
 
print_r($html);

// ------------------------Delete Record ------------------hh292

echo "<h2>Delete One Record</h2>";
print_r($lastInsertedId);
$records = todos::findOne($lastInsertedId);
$record= new todo();
$record->id=$records->id;
echo "<br>";
//print_r($records);
$records->delete();
echo '<h3>Record with id: '.$records->id.' is deleted</h3>';
echo "After Delete";
$records = todos::findAll();

$html = displayHtml::tableDisplayFunction($records);
echo "<h3>After Deleteing</h3>";
print_r($html);

}
}