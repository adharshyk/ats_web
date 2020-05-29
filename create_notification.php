<?php

    require('db.php');
    $con = connect_db();

    $faculty_id = $_POST['faculty_id'];
    $branch = $_POST['branch'];
    $sem = $_POST['sem'];
    $batch = $_POST['batch'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    if( !isset( $faculty_id ) || ! isset( $branch ) || ! isset( $sem ) || ! isset( $batch ) || ! isset( $title ) || ! isset( $content ) ){
        echo json_encode( array( 'status'=>0, 'text'=>'Invalid Request' ) );
        exit();
    }

    // create a notification
    $sql = "INSERT INTO notifications(title, content, branch, sem, batch, faculty_id, date_added) VALUES( '$title', '$content', '$branch', '$sem', '$batch', '$faculty_id', CURDATE() )";
    $result = $con->query( $sql );

    echo json_encode( array( 'status'=>1, 'text'=>'Notification added'));
?>