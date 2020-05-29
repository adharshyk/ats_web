<?php

    require('db.php');
    $con = connect_db();

    // get batch
    $branch = $_POST['branch'];
    $sem = $_POST['sem'];
    $batch = $_POST['batch'];
    $faculty_id = $_POST['faculty_id'];

    if( ! isset( $branch ) || ! isset( $sem ) || ! isset( $batch ) || ! isset( $faculty_id ) ){
        echo json_encode( array( 'status'=>0, 'text'=>'Invalid request'));
        exit();
    }

    // get subject
    $sql = "SELECT * FROM teaches_at WHERE branch = '$branch' and sem = '$sem' and batch = $batch";
    $result = $con->query( $sql );
    $row = $result->fetch_assoc();
    $subject = $row['subject'];

    // get the attendance for the batch
    $sql = "SELECT * FROM total_attendance WHERE subject='$subject' and faculty_id = '$faculty_id'";
    $result = $con->query( $sql );

    $arr = array();
    while( $row = $result->fetch_assoc() ){
        // get student details
        $student_id = $row['student_id'];
        $attended = $row['attended'];
        $total = $row['total'];

        $sql = "SELECT * FROM student WHERE admno = '$student_id'";
        $result1 = $con->query( $sql );
        $row1 = $result1->fetch_assoc();
        $student_name = $row1['name'];
        array_push( $arr, array( 'name'=> $student_name, 'attended'=>$attended, 'total'=>$total ));
    }
    echo json_encode( array( 'status'=>1, 'array'=> $arr) );
?>