<?php

require( 'db.php' );


function get_faculty_timetable(){
    $con = connect_db();

    if( ! isset( $_POST['regno'] ) ){
        echo json_encode( array( 'status'=>0, 'text'=>'Invalid request'));
        exit();
    }

    $regno = $_POST['regno'];

    // get those classess he teaches at
    $sql = "SELECT * FROM teaches_at WHERE faculty_id = '$regno'";
    $result = $con->query( $sql );

    if( $result->num_rows <= 0 ){
        echo json_encode( array( 'status'=>0, 'text'=>'No Classes found', 'batches'=> array() ));
        exit();
    }

    // get today
    $today = (int) date('N');

    $hours = array("0","0","0","0","0","0");

    function get_hours_of_subject( $timetable, $subject, $branch, $sem, $batch ){
        foreach( $timetable as $hour=>$sub ){
            if( $sub == $subject ){
                $h = (int) substr( $hour, -1);
                $j = array( 'branch'=> $branch, 'sem'=>$sem, 'batch'=> $batch, 'sub'=>$sub );
                $GLOBALS['hours'][ $h - 1 ] = $j;
            }
        }
    }

    while( $row = $result->fetch_assoc() ){
        $branch = $row['branch'];
        $sem = $row['sem'];
        $batch = $row['batch'];
        $subject = $row['subject'];

        // get todays timetable for this batch
        $sql = "SELECT * FROM timetable WHERE weekday = $today and branch ='$branch' and sem = '$sem' and batch = '$batch'";
        $res = $con->query( $sql );

        $timetable = $res->fetch_assoc();

        // removing unwanted fields
        unset( $timetable['branch']);
        unset( $timetable['batch']);
        unset( $timetable['sem']);
        unset( $timetable['weekday']);
        // now only hour_1, hour_2, ....
        


        get_hours_of_subject( $timetable, $subject, $branch, $sem, $batch );
    }

    $todays_timetable = $hours;

    // get alll batches of this faculty
    $sql = "SELECT * FROM teaches_at WHERE faculty_id = '$regno'";
    $result = $con->query( $sql );
    $batches = [];
    while( $row = $result->fetch_assoc() ){
        unset( $row['faculty_id'] );
        unset( $row['subject'] );
        array_push( $batches, $row );
    }

    // get complete timetable also
    $complete_timetable = array("0","0","0","0","0");

    $sql = "SELECT * FROM teaches_at WHERE faculty_id = '$regno'";
    $result = $con->query( $sql );


    while( $row = $result->fetch_assoc() ){
        $branch = $row['branch'];
        $sem = $row['sem'];
        $batch = $row['batch'];
        $subject = $row['subject'];

        // get todays timetable for this batch
        for( $i = 1 ; $i < 6; $i++  ){
            $hours = array("0","0","0","0","0","0");
            $sql = "SELECT * FROM timetable WHERE weekday = $i and branch ='$branch' and sem = '$sem' and batch = '$batch'";
            $res = $con->query( $sql );

            while( $timetable = $res->fetch_assoc() ){
                // removing unwanted fields
                unset( $timetable['branch']);
                unset( $timetable['batch']);
                unset( $timetable['sem']);
                unset( $timetable['weekday']);
                // now only hour_1, hour_2, ....
                
                get_hours_of_subject( $timetable, $subject, $branch, $sem, $batch );
            }
            $complete_timetable[ $i - 1 ] = $hours;
    }
    }
    return array( 'today'=>$todays_timetable, 'complete_timetable'=>$complete_timetable, 'batches'=> $batches );
}


?>
