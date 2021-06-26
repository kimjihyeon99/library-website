<?php 
    session_start();
    $flag = 0;
    $tns = " (DESCRIPTION = 
                (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) ) 
                (CONNECT_DATA = (SERVICE_NAME = xe) ) 
            )
        ";
    $dsn = "oci:dbname=".$tns.";charset=utf8";
    $username = 'd201802078';
    $password = '12345';
    try { 
        $conn = new PDO($dsn, $username, $password); 
        
        $cno = $_SESSION['id'];
        $isbn = $_POST["data"];
     
        //예약 내역 삭제
        $stmt = $conn -> prepare("DELETE FROM reserve WHERE isbn='$isbn' and cno='$cno'");
        $stmt -> execute();

        $flag = 1;
    
    } 
    catch (PDOException $e) { 
            echo "Failed to obtain database handle " . $e->getMessage(); 
    } 

    if($flag==1){
        echo "success";
    }else{
        echo "예약 취소에 실패했습니다.";
    }   
?>