<?php


require('db.php');
$con = connect_db();

if( !isset( $_POST['student_id']) ){
    echo json_encode( array( 'status'=>0, 'text'=>"Invalid Request") );
    exit();
}

$student_id = $_POST['student_id'];

$sql = "SELECT * FROM student WHERE admno = '$student_id' ";
$result = $con->query( $sql );

if( $result->num_rows <= 0 ){
    echo json_encode( array( 'status'=>0, 'text'=>'Invaid student ID' ) );
    exit();
}

$row = $result->fetch_assoc();
$branch = $row['branch'];
$sem = $row['sem'];
$batch = $row['batch'];

$sql = "SELECT * FROM timetable WHERE branch = '$branch' and sem = '$sem' and batch = '$batch' ";
$result = $con->query( $sql );

$timetable = array( "0", "0", "0", "0", "0" );
$i = 0;
while( $row = $result->fetch_assoc() ){
    unset( $row['branch'] );
    unset( $row['sem'] );
    unset( $row['batch'] );
    $timetable[ $i ] = $row;
    $i = $i + 1;
}
echo json_encode( $timetable );

?>