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

        foreach($_POST["checkbox"] as $cb){
            $stmt = $conn -> prepare("SELECT * FROM EBOOK where cno='$cno' and title='$cb'");
            $stmt -> execute();

            $row = $stmt -> fetch(PDO::FETCH_ASSOC);

            $count = $row['EXTTIMES'];
            $date = $row['DATEDUE'];
        
            //예약이 되어있을 경우 대출 연장 불가능
            $stmt = $conn -> prepare("select count(*) from reserve, ebook where reserve.isbn=ebook.isbn and title='$cb'");
            $stmt -> execute();

            $res_cnt = $stmt -> fetchColumn();

            if($res_cnt ==0){ //예약한 사람이 없고
                if($count !=3){ //3회 이상 아니면, 최대 2회 가능 
                    $count = $count+1;   
    
                    $stmt = $conn -> prepare("UPDATE ebook  SET exttimes='$count', datedue= TO_DATE('$date', 'YY/MM/DD')+(INTERVAL '10' DAY) WHERE title='$cb'");
                    $stmt -> execute();

                    echo "<script>location.href='./returnbook.php'</script>";   
                }               
            }else{
                echo "<script>location.href='./returnbook.php'; alert('예약한사람이 존재합니다.');</script>";
            }

               
        }
    } 
    catch (PDOException $e) { 
            echo "Failed to obtain database handle " . $e->getMessage(); 
    }
?>