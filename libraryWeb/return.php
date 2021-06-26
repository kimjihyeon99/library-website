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
        
        // 대출 기록 지워 반납 처리
        foreach($_POST["checkbox"] as $cb){
            $stmt = $conn -> prepare("UPDATE ebook  SET cno=NULL,exttimes=NULL,daterented=NULL,datedue=NULL WHERE  title='$cb'");
            $stmt -> execute();
        }

    } 
    catch (PDOException $e) { 
            echo "Failed to obtain database handle " . $e->getMessage(); 
    } 
    
    echo "<script>location.href='./returnbook.php'</script>";

?>