<?php

    require('db.php');

    $con = connect_db();

    if( ! isset( $_POST['branch'], $_POST['sem'], $_POST['batch'] )){
        echo json_encode( array( 'status'=>0, 'text'=>'Invalid Request') );
    }

    $branch = $_POST['branch'];
    $sem = $_POST['sem'];
    $batch = $_POST['batch'];

    $sql = "SELECT * FROM student WHERE branch = '$branch' and sem = '$sem' and batch = '$batch'";
    $result = $con->query( $sql );

    $students = array();
    while( $row = $result->fetch_assoc() ){
        unset( $row['password'] );
        array_push( $students, $row );
    }

    echo json_encode( $students );

?>