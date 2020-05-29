<?php
    require('db.php');
    
    function get_students_with_shortage(){
        $con = connect_db();

        // get faculty id
        $faculty_id = $_POST['regno'];

        if( ! isset( $faculty_id ) ){
            echo json_encode( array('status'=>0, 'text'=>'Invalid Request'));
            exit();
        }

        // get all the attendance records of this faculty
        $sql = "SELECT * FROM student s JOIN total_attendance t ON s.admno = t.student_id";
        $result = $con->query( $sql );

        $students_with_shortage = array();
        while( $row = $result->fetch_assoc() ){
            $student_id = $row['student_id'];

            
            // $sql = "SELECT * FROM student WHERE admno = '$student_id'";
            // $res = $con->query( $sql );
            // $row = $res->fetch_assoc();

            $attended = $row['attended'];
            $total = $row['total'];
            $percentage = $attended / $total * 100;
            
            if( $percentage < 75 ){
                $sql = "SELECT * FROM teaches_at WHERE ";
                // TODO
                
                $student = array( 'student_id'=> $student_id );
                $student['name'] = $row['name'];
                $student['email'] = $row['email'];
                $student['branch'] = $row['branch'];
                $student['sem'] = $row['sem'];
                $student['batch'] = $row['batch'];
                $student['attended'] = $row['attended'];
                $student['total'] = $row['total'];

                array_push( $students_with_shortage, $student );
            }
        }
        return $students_with_shortage;
    }      

    function get_full_timetable(){
        $conn = connect_db();

        $faculty_id = $_POST['regno'];
        if( ! isset( $faculty_id ) ){
            return 0;
        }

        $t = [ "0", "0", "0", "0", "0" ];
        $subjects = [];

        // get all subjects
        $sql = "SELECT * FROM teaches_at WHERE faculty_id = '$faculty_id'";
        $result = $conn->query( $sql );

        while( $row = $result->fetch_assoc() ){
            array_push( $subjects, $row );
        }

        // get entire time table 
        $sql = "SELECT * FROM timetable";
        $result = $conn->query( $sql );
        // find my hours from that and fill out t
        while( $row = $result->fetch_assoc() ){
            $today = ["0", "0", "0", "0", "0", "0"];
            $branch = $row['branch'];
            $sem = $row['sem'];
            $batch = $row['batch'];
            $weekday = $row['weekday'];
            
            unset( $row['weekday']);
            unset( $row['branch']);
            unset( $row['sem']);
            unset( $row['batch']);

            foreach( $row as $hour=>$sub ){
                if( is_in_subjects( $sub, $subjects )){
                    $h = substr( $hour, -1 );
                    $today[ $h - 1 ] = array( 'branch'=>$branch, 'sem'=>$sem, 'batch'=>$batch, 'subject'=>$sub );
                }
            }
            $t[ $weekday ] = $today;
        }
        return $t;

    }
    function is_in_subjects( $subject, $subjects ){
        foreach( $subjects as $sub ){
            if( $sub['subject'] == $subject ){
                return true;
            }
        }
        return false;
    }
    
    
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
    
        $GLOBALS['hours'] = array("0","0","0","0","0","0");
    
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
    
        $todays_timetable = $GLOBALS['hours'];
    
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
    
    //     $sql = "SELECT * FROM teaches_at WHERE faculty_id = '$regno'";
    //     $result = $con->query( $sql );
    
    
    //     while( $row = $result->fetch_assoc() ){
    //         $branch = $row['branch'];
    //         $sem = $row['sem'];
    //         $batch = $row['batch'];
    //         $subject = $row['subject'];
    
    //         // get todays timetable for this batch
    //         for( $i = 1 ; $i < 6; $i++  ){
    //             $sql = "SELECT * FROM timetable WHERE weekday = $i and branch ='$branch' and sem = '$sem' and batch = '$batch'";
    //             $res = $con->query( $sql );
                
    //             $GLOBALS['hours'] = array("0","0","0","0","0","0");
    //             while( $timetable = $res->fetch_assoc() ){
    //                 // removing unwanted fields
    //                 unset( $timetable['branch']);
    //                 unset( $timetable['batch']);
    //                 unset( $timetable['sem']);
    //                 unset( $timetable['weekday']);
    //                 // now only hour_1, hour_2, ....
                    
    //                 get_hours_of_subject( $timetable, $subject, $branch, $sem, $batch );
    //             }
    //             $complete_timetable[ $i - 1 ] = $GLOBALS['hours'];
    //             print_r( $complete_timetable );
    //         }
        
    //     }
        return array( 'today'=>$todays_timetable, 'batches'=> $batches );
    }
    
    
    $faculty_timetable =  get_faculty_timetable();
    $students_with_shortage = get_students_with_shortage();
    $complete_timetable = get_full_timetable();

    $faculty_timetable['students_with_shortage'] = $students_with_shortage;
    $faculty_timetable['complete_timetable'] = $complete_timetable;

    $con = connect_db();
    // get all notifications too ( datewise )
    $sql = "SELECT * FROM notifications ORDER BY date_added";
    $result = $con->query( $sql );

    $notifications = array();
    while( $row = $result->fetch_assoc() ){
        $faculty_id = $row['faculty_id'];

        $sql = "SELECT * FROM faculties WHERE regno = '$faculty_id'";
        $res = $con->query( $sql );
        $details = $res->fetch_assoc();
        
        $fac_name = $details['name'];
        $desig = $details['designation'];
        $dept = $details['dept'];

        $row['faculty_name'] = $fac_name;
        $row['designation'] = $desig;
        $row['dept'] = $dept;
        $row['date_added'] = date( "D d M, Y", strtotime( $row['date_added'] ));

        array_push( $notifications, $row );
    }

    $faculty_timetable['notifications'] = $notifications;
    echo json_encode( $faculty_timetable );
?>