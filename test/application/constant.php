<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 3/22/2019
 * Time: 4:41 AM
 */

//if (!$required) {
   // die("Cannot be executed independently.");
//}

//$dbhost = '127.0.0.1';
//$dbport = '3306';
//$dbname = 'admin';
//$charset = 'utf8';
//$tns = "mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset={$charset}";
$tns = 'mysql:dbname=admin;host=database.cjzuwj48cotq.ap-southeast-1.rds.amazonaws.com;port=3306';
$db_username = 'admin';
$db_password = 'leo12345';

function build_sql_update($table, $data, $where)
{
    $cols = array();

    foreach($data as $key=>$val) {
        if (gettype($val) == "string"){
            $cols[] = "$key = '$val'";
        }else{
            $cols[] = "$key = $val";
        }
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

    return($sql);
}

function check_admin_permission($rows){
    if(sizeof($rows) == 1 && $rows[0]["role"] == "admin" && $rows[0]["status"] == 0){
        return true;
    }
    return false;
}

function check_permission($rows){
    if(sizeof($rows) == 1 && $rows[0]["status"] == 0){
        return true;
    }
    return false;
}

function get_permission_sql($token){
    return "SELECT userid, role,status FROM admin.useraccount WHERE UToken='$token'";
}
