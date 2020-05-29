<?php
    require('db.php');

    function get_students_with_shortage(){
        $con = connect_db();

        // get faculty id
        $faculty_id = $_POST['faculty_id'];

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

?>