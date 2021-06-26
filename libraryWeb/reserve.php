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
        $title= $_GET["title"];
        $isbn = $_POST["data"];
        
        //고객이 빌린 책인지 확인
        $stmt = $conn ->prepare("select count(*) from ebook where isbn='$isbn' and cno='$cno'");
        $stmt -> execute();

        $count = $stmt -> fetchColumn();

        if($count != "1"){
            $stmt = $conn -> prepare("select count(*) from reserve where cno='$cno'");
            $stmt -> execute();
            //고객이 예약한 책의 수 
            $count = $stmt -> fetchColumn();

            if($count !="3"){
                //예약하지 않은 상태면 가능함.
                $stmt = $conn -> prepare("select count(*) from reserve where isbn='$isbn' and cno='$cno'");
                $stmt -> execute();
                
                $count = $stmt -> fetchColumn();

                if($count !="1"){
                    $stmt = $conn -> prepare("INSERT INTO reserve VALUES ($isbn,$cno, SYSDATE)");
                    $stmt -> execute();
                    $flag = 1;
                }else{
                    //이미 예약한 경우
                    $flag = 2;
                }

            }else{
                //예약한 책의 수가 3권인경우
                $flag = 3;
            }
        }else{
            //고객이 이미 빌린 책의 경우
            $flag=4;
        }

    } 
    catch (PDOException $e) { 
            echo "Failed to obtain database handle " . $e->getMessage(); 
    } 

    if($flag==1){
        echo "success";
    } else if($flag==2){
       echo "이미 예약한 책은 예약 불가능합니다.";
    } else if($flag==3){
        echo "3권이상 예약 불가능합니다.";
    } else if($flag==4){
        echo "이미 빌린책은 예약 불가능합니다.";
    } else{
        echo "예약 실패했습니다.";
    }   
?>