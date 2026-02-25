<?php
namespace Onelab;

header('Content-Type: application/json');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/onelab/removeuser.php");

$User_table = new \Onelab\Removeuser\Users();
if($_POST['ACTION'] == 'REMOVE'){
    $User_table-> DeactivateUser($_POST['ID']);
}

if($_POST['ACTION'] == 'SHOW'){
    $User_table->ShowCommentUser($_POST['ID']);
}
if($_POST['ACTION'] == 'ADD'){
   $User_table->AddCommentUser($_POST['ID'], $_POST['TEXT']);
}

