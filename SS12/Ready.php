<?php
require 'Player.php';
session_start();


function newPlayer($pname)
{
    $player=new Player($pname);
    $player->connect();
    $player->insert();
    $player->close();
    return $player;
}

//function music($num)
//{
//    $music=array();
//    for($x=0;$x<$num;$x++)
//    {
//        $music[]=rand(1,100)%2;
//    }
//    return $music;
//}

$status=$_REQUEST["status"];
$pname=$_REQUEST["pname"];
if($status=="0"&&$pname!=="")
{
    $player=  newPlayer($pname);
    $_SESSION["player"]=$player;
}
if($status==1) // first search
{
    $player=$_SESSION["player"];
    $player->connect();
    if($player->ready())
    {
      
        echo "true";
    }
    else 
    {
        echo "false";
    }
    $_SESSION["player"]=$player;
    $player->close();
}
if($status==2) // wait another player
{
    $player=$_SESSION["player"];
    $player->connect();
    if($player->wait())
    {
      echo "true";
    }
    else
    {
        echo "false";
    }
    $_SESSION["player"]=$player;
    $player->close();
}

