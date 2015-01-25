<?php

/**
 * Description of Player
 *
 * @author fqcprodigy
 */

define("LENGTH", 20);

class Player {
    public $P_id;
    public $Status;
    public $Room;
    public $Score;
    public $Pair;
    
    private $connection;
    
    public function __construct($nick) {
       $this->P_id=$nick;
       $this->Status=0;
       $this->Room=0;
       $this->Score=0;
       $this->Pair="";
       $this->connection=null;
    }
    
    public function connect() {
       $servername = "127.0.0.1";
       $username = "root";
       $password = "123456";
       $dbname = "SS12";
       $this->connection=new mysqli($servername, $username, $password,$dbname);
       if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }
    
    public function close()
    {
        $this->connection->close();
    }
    
    public function isLose()
    {
        $sql="SELECT Status,Score FROM Player WHERE PlayerID='".$this->Pair."'";
        $result=$this->connection->query($sql);
        $row=mysqli_fetch_array($result);
        if($row[0]==3&&$row[1]>=LENGTH)
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }

    public function win()
    {
        if($this->Score==LENGTH)
        {
            return 1+LENGTH; // win ahead of time
        }
        $sql="SELECT Score,Status FROM Player WHERE PlayerID='".$this->Pair."'";
        $result=$this->connection->query($sql);
        $row=mysqli_fetch_array($result);
        if($row[1]!=3)
        {
            return 999;
        }
        else
        {
            return $this->Score-$row[0];
        }
    }


    public function quit()
    {
        $this->connection->autocommit(FALSE);
        $sql="UPDATE Player SET Room=0,Pair='#' WHERE PlayerID='".$this->P_id."'";
        $this->connection->query($sql);
        $this->Room=0;
        $this->connection->commit();
        return $this;
    }
    
    public function wait_after_game()
    {
        $this->Status=1;
        $this->Score=0;
        $this->connection->autocommit(FALSE);
        $sql="UPDATE Player SET Status=1,Score=0 WHERE PlayerID=".$this->P_id;
        $this->connection->query($sql);
        $this->connection->commit();
    }
    public function score($score)
    {
        $this->Score=$score;
        $this->Status=3;
        $this->connection->autocommit(FALSE);
        $sql="UPDATE Player SET Status=3,Score=".$score." WHERE PlayerID='".$this->P_id."'";
        $this->connection->query($sql);
        $this->connection->commit();
    }

    public function wait()
    {
        $sql="SELECT Status FROM Player WHERE PlayerID='".$this->P_id."'";
        //echo $sql;
        $this->connection->autocommit(FALSE);
        $result=  $this->connection->query($sql);
        $this->connection->commit();
        $row = mysqli_fetch_array($result);
        if($row[0]==2)
        {
            $sql="SELECT PlayerID FROM Player WHERE Pair='".$this->P_id."'";
            $result=  $this->connection->query($sql);
            $row=mysqli_fetch_array($result);
            $this->Pair=$row[0];
            return true;
        }
        else
        {   
            return false;
        }
    }


    public function ready() {
        $name=  $this->P_id;
        $sql="SELECT Roomid FROM Room WHERE Num=1 LIMIT 1";
        $this->connection->autocommit(FALSE);
        $result=  $this->connection->query($sql);
        if($result->num_rows>0)
        {
            $row = mysqli_fetch_array($result);
            $Room=$row[0];
            $sql="SELECT PlayerID FROM Player WHERE Room=".$Room;
            $rresult=$this->connection->query($sql);
            $rrow=mysqli_fetch_array($rresult);
            $this->Pair=$rrow[0];
            $this->Status=2;
            $sql="UPDATE Player SET Score=0, Status=2,Pair='".$name."' WHERE PlayerID='".$this->Pair."'";
            $this->connection->query($sql);
            $sql="UPDATE Player SET Room=".$Room.",Status=2,Score=0,Pair='".$this->Pair."' WHERE PlayerID='".$name."'";
            $this->connection->query($sql);
            $sql="UPDATE Room SET Num=Num+1 WHERE Roomid=".$Room;
            $this->connection->query($sql);
            $this->connection->commit();
            return true;
        }
        else
        {
            $sql="INSERT INTO Room VALUES();";
            $this->connection->query($sql);
            $last_room=  $this->connection->insert_id;
            $this->Room=$last_room;
            $this->Status=1;
            $sql="UPDATE Player SET Room=".$last_room.",Status=1 WHERE PlayerID='".$name."'";
    
            $this->connection->query($sql);
            $this->connection->commit();
            return false;
        }
    }
    
    private function check($name)
    {
        $sql="SELECT * FROM Player WHERE PlayerID='".$name."';";
        $result=  $this->connection->query($sql);
        if($result->num_rows>0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function delete()
    {
        $sql="DELETE FROM Player WHERE PlayerID='".$this->P_id."'";
        $this->connection->query($sql);
    }

    public function insert() 
    {
        echo $this->P_id;
        $this->connection->autocommit(FALSE);
        while($this->check($this->P_id))
        {
            $this->P_id=  $this->P_id."#";
            
        }
        $sql="INSERT INTO Player VALUES('".$this->P_id."',0,0,'#',0);";
        // id,status,score,pair,room
        $this->connection->query($sql);
        $this->connection->commit();
    }
}
