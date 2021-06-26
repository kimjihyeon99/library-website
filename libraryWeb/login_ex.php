<?php 
    session_start();

    $loginSuccess= false;
    $id=  trim($_POST['user_id']);
    $pw=  trim($_POST['user_pw']);

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
        
        if(!isset($_SESSION["id"])){
            if(isset($id)){
                //관리자 계정인경우
                if($id == "999"){
                    if($pw =="999"){
                        $_SESSION["id"] = $id;
                        $_SESSION["admin"] = true;
                        $loginSuccess =true;
                    }
                //고객 인경우
                }else{
                     // 고객정보 가져오기
                    $stmt = $conn -> prepare("select COUNT(CNO) from customer where CNO='$id' and PASSWD='$pw'");
                    $stmt -> execute();

                    $count = $stmt -> fetchColumn();
                
                    if($count == "1"){
                        $_SESSION["id"] = $id;
                        $_SESSION["admin"] = false;
                        $loginSuccess =true;
                    }
                }

                
            }
        }else{
            $loginSuccess =true;
        }
    } 
    catch (PDOException $e) { 
            echo "Failed to obtain database handle " . $e->getMessage(); 
    } 

   
    if($loginSuccess){
        echo "success";
    } else{
        echo "입력하신 아이디가 존재하지 않거나 패스워드가 틀립니다.";
    }        

?>
