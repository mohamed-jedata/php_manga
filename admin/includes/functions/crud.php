<?php


class Crud{

    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }


    //return elements with Select statement with multiple parameters
    public function getAllFrom($fields,$table,$where=null,$orderby=null,$sort=null,$limit=null,$offset=null,$fetchOne=FALSE){

        $_orderby = !empty($orderby) ? "order by $orderby" : "";
        $_limit = !empty($limit) ? "limit $limit" : "";
        $_offset = !empty($offset) ? "offset $offset" : "";

        $stmp = $this->connection->prepare("SELECT $fields FROM $table $where $_orderby $sort $_limit $_offset");
    
        $stmp->execute();
        if(!$fetchOne)
          return $stmp->fetchAll();
        else
          return $stmp->fetch();

    }

    //INSERT Statement
    public function InsertInto($table,$fields,$values){

        $nbFields = count(explode(",",$fields));
        $quMark=[];
        for($i=1 ; $i <= $nbFields ; $i++){
            $quMark[] = " ? ";
        }
        $quMark = implode(",",$quMark); //create a string of ? separed with coma


        $stmp = $this->connection->prepare("Insert into $table ($fields) Values ($quMark)");
        
        $values = !is_array($values) ? explode(",",$values) :$values ;
        
        $stmp->execute($values);

        return $stmp->rowCount();
        

    }

    //update function : update single or multiple lines is datavase
    #Parameters
    #-Table  : table to update (type =String)
    #-Fields : fields or one field to update (type = array()) 
    #-Values : Values to set in given fields (type = array())
    #-ID_Name,ID_Value : ID column name is table and ID Value

    public function UpdateInfo($table,$fields = array(),$values = array(),$ID_Name,$ID_Value){
        
        $fields = implode(" = ?, ",$fields);
        $fields .= " = ?";
    
        $stmp = $this->connection->prepare("UPDATE $table SET $fields WHERE $ID_Name = ?");

        array_push($values,$ID_Value);

        $stmp->execute($values);

        return $stmp->rowCount();

    }


    //delete function with tow parameters , Table name and ID name And ID value
    public function Delete($table,$ID_Name,$ID_Value){
    
        $stmp = $this->connection->prepare("Delete From $table Where $ID_Name = :ID_Value");

        $stmp->execute(array(":ID_Value"=>$ID_Value));

        return $stmp->rowCount();
    }    




}









?>