<?php

    require 'db.php' ;

    $con = connect_db();

    // if connection successful
    if ( $con == 0 ){
        echo json_encode( array( 'status'=>0, 'text'=>'Could not connect to db') );
        exit();
    }

    // get data sent via POST
    $regno = $_POST['regno'];
    $passwrd = $_POST['password'];

    // authenticate
    $sql = "SELECT * FROM faculties WHERE regno = '$regno' and password = password('$passwrd')";
    $result = $con->query( $sql );

    if ( $result->num_rows <= 0 ){
        echo json_encode( array( 'status'=>0, 'text'=>'Incorrect faculty ID or password') );
        exit();
    }

    // get faculty details
    while( $row = $result->fetch_assoc() ){
        $details = array( 'status'=>1,
             'text'=>'Sign in successfull', 'regno'=> $row['regno'], 'name'=>$row['name'],
              'dept'=>$row['dept'], 'designation'=>$row['designation'], 'email'=> $row['email'],
                'phone_no'=> $row['phone_no'] );
        echo json_encode( $details );
    }

    $con->close();


?>
