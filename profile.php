<?php

// Displays current user info, properties owned. If the user is an admin, display an admin button that will redirects the user to admin.php


require_once 'header.php';
require_once 'navbar.php';

//Create a user session or resume an existing one
session_start();

if(isset($_SESSION['user_id'])){

        echo "You're logged on" . nl;

        // SELECT query
        $query = "SELECT FName, LName, is_admin FROM user WHERE user_id=?";

        // prepare query for execution
        $stmt = $con->prepare($query);

        // bind the parameters.
        $stmt->bind_Param("s", $_SESSION['user_id']);

        // Execute the query
        $stmt->execute();

        // results
        $result = $stmt->get_result();

        // Row data
        $myrow = $result->fetch_assoc();

        if ( $myrow['is_admin'] == 1){
            echo "You're an admin" . nl;

            echo '<a href="admin.php">Click Here to go to admin.php</a>' . nl;
        }
        else{
            echo "You're NOT an admin" . nl;
        }


} else {
    //User is not logged in. Redirect the browser to the login index.php page and kill this page.
    echo "You're not logged in!" . nl;
    header("Location: index.php");
    die();
}


?>


Welcome  <?php echo $myrow['FName'] . ' ' . $myrow['LName']; ?>