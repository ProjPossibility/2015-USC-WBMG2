<?php
require 'Player.php';
session_start();

$player=$_SESSION['player'];

function quit(Player $player)
{
    $player->connect();
    $player->quit();
    $player->close();
    return $player;
}
function score(Player $player,$score)
{
    $player->connect();
    $player->score($score);
    $player->close();
    return $player;
}

function isLose(Player $player)
{
    $player->connect();
    $lose=$player->isLose();
    $player->close();
    return $lose;
}

function win(Player $player)
{
    $player->connect();
    $result=$player->win();
    echo $result;
    $player->close();
    
    if($result<900)
    {
        $player->connect();
        $player->quit();
        $player->close();
        $_SESSION['player']=$player;
        
    }
}

function wait_game(Player $player)
{
    $player->connect();
    $player->wait_after_game();
    $player->close();
    return $player;
}

function delete(Player $player)
{
    $player->connect();
    $player->delete();
    $player->close();
}

$para=$_REQUEST["para"];
if($para=="quit")
{
    $_SESSION['player']=quit($player);
    
}
if($para=="score")
{
    $score=$_REQUEST["score"];
    $_SESSION['player']=score($player,$score);
    
}
if($para=="isLose")
{
    echo isLose($player);
}
if($para=="win")
{
    win($player);
}
if($para=="wait_game")
{
    $_SESSION['player']=  wait_game($player);
}
if($para=="delete")
{
    delete($player);
}