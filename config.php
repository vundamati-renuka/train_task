<?php

session_start();

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/*
mysqli_connect
mysqli_query
mysqli_fetch_assoc
mysqli_fetch_array
mysqli_data_seek
mysqli_error
*/

$con = mysqli_connect( "localhost", "root", "","sample");
if( mysqli_connect_error() ){
    echo "Server connection error!";
    echo mysqli_connect_error();
    exit;
}

?>