<?php 
    session_start();
    $flag = false;
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
        $title= $_GET["title"];
        
        $stmt = $conn -> prepare("select count(*) from ebook where cno='$cno'");
        $stmt -> execute();
        //고객이 빌린 책의 수 
        $count = $stmt -> fetchColumn();

        // 고객이 3권이상 빌리지 않았으면, 대출 처리 
        if($count !="3"){
             $stmt = $conn -> prepare("UPDATE ebook  SET cno='$cno',exttimes=0,daterented=SYSDATE,datedue=SYSDATE+(INTERVAL '10' DAY)  WHERE  title ='$title'");
             $stmt -> execute();

            $flag = true;
        }

    } 
    catch (PDOException $e) { 
            echo "Failed to obtain database handle " . $e->getMessage(); 
    } 

    if($flag){
        echo "success";
    } else{
       echo "3권이상 대출 불가능합니다.";
    }        
?>