<?php
/**
 * Created by PhpStorm.
 * User: Wilson
 * Date: 2/24/2019
 * Time: 22:13
 */

//If receive post request -> logout
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["logout_btn"])){
        logout();
    }
}

session_start();

if(!isset($_SESSION["userDetails"]) || !isset($_SESSION["lastActivity"]) ){
    redirectToLoginPage();
}elseif(isset($_SESSION["userDetails"]) && (time() - $_SESSION["lastActivity"] > 1800)){
    //If users was afk for more than half hour
    logout();
}else{
    $_SESSION["lastActivity"] = time();
}

function redirectToLoginPage(){
    header("Location: ./login.php");
    exit;
}

function logout(){
    session_unset();
    session_destroy();
    redirectToLoginPage();
}