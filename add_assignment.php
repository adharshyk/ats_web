<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require('db.php');
    $con = connect_db();

    $faculty_id = $_POST['faculty_id'];
    $branch = $_POST['branch'];
    $sem = $_POST['sem'];
    $batch = $_POST['batch'];
    $topic = $_POST['topic'];
    $date_of_subm = $_POST['date'];

    if( !isset( $faculty_id ) || ! isset( $branch ) || ! isset( $sem ) || ! isset( $batch ) || ! isset( $topic ) || ! isset( $date_of_subm ) ){
        echo json_encode( array( 'status'=>0, 'text'=>'Invalid Request' ) );
        exit();
    }

    // get the subject
    $sql = "SELECT * FROM teaches_at WHERE faculty_id = '$faculty_id' and branch = '$branch' and sem = '$sem' and batch = $batch";
    $result = $con->query( $sql );

    $row = $result->fetch_assoc();
    $subject = $row['subject'];

    $date_of_subm = date( 'Y-m-d', strtotime( $date_of_subm ));
    
    // create a assignment
    $sql = "INSERT INTO assignment VALUES( '$faculty_id', '$branch', '$sem', $batch, '$subject', '$topic', '$date_of_subm' )";
    $result = $con->query( $sql );


    // create a notification
    $title = "New Assignment on $subject is here";
    $date_of_subm = date('D d M, Y', strtotime( $date_of_subm ) );
    $content = "There is new assignment on $subject about $topic. It should be submitted on or before $date_of_subm";
    $sql = "INSERT INTO notifications(title, content, branch, sem, batch, faculty_id, date_added) VALUES( '$title', '$content', '$branch', '$sem', '$batch', '$faculty_id', CURDATE() )";
    $result = $con->query( $sql );


    echo json_encode( array( 'status'=>1, 'text'=>'Assignment added'));
?>