<?php
$tns = " (DESCRIPTION = 
                (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) ) 
                (CONNECT_DATA = (SERVICE_NAME = xe) ) 
            )
        ";
$dsn = "oci:dbname=" . $tns . ";charset=utf8";
$username = 'd201802078';
$password = '12345';
try {
    $conn = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    echo "Failed to obtain database handle " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bookliststyle.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    </style>
    <title>BOOK LIST</title>
</head>

<body>
    
    <div id="header">
        <div id="login_status">
            <!-- 로그인 세션이 없으면 login 화면으로 가기 -->
            <?php        
            session_start();
            if (isset($_SESSION["id"])) {
                $id = $_SESSION["id"];
                echo "<div id='userid'>" . $id . "</div>";
            } else {
                header('Location: ./loginview.html');
            }
            ?>
            <!-- 로그아웃하면 세션 reset -->
            <input type="button" id="logout_btn" value="로그아웃" formaction="logout.php">
        </div>
        <!-- 메뉴바 -->
        <div id="manu_bar">
            <ul>
                <li><a href="booklist.php">도서선택</a></li>
                <li><a href="returnbook.php">대출현황</a></li>
            </ul>
        </div>
    </div>


    <div class="container">
        <!-- 검색 바 -->
        <div id=search_bar>
            <!-- 검색 조건 설정을 위한 체크박스 -->
            <form action="booklist.php" method="GET">
                <input type="text" name="first" placeholder="and or not">
                <label><input type="checkbox" name="searchoption[]" value="title"> 서명</label>
                <input type="text" name="second"  placeholder="and or not">
                <label><input type="checkbox" name="searchoption[]" value="author"> 저자</label>
                <input type="text" name="third"  placeholder="and or not">
                <label><input type="checkbox" name="searchoption[]" value="publisher"> 출판사</label>
                <input type="text" name="four"  placeholder="and or not">
                <label><input type="checkbox" name="searchoption[]" value="year"> 발행년도</label>
                <br>
                <input id=input_bar name=search type="text">
                <button id=search_btn>검색</button>
            </form>
        </div>
        <!-- 책 리스트 보여주는 곳 -->
        <div class=book_list>
            <ul>
                <?php
                $option = "";
                $search = "";

                $condition = null;

                if (isset($_GET["search"])) {
                    $search = $_GET["search"];
                    if (isset($_GET["searchoption"])) {
                        $op = array();
                        $op[0] = $_GET["first"];
                        $op[1] = $_GET["second"];
                        $op[2] = $_GET["third"];
                        $op[3] = $_GET["four"];

                        
                        $option = $_GET["searchoption"];

                        $i=0;
                        $ip_op = array();

                        foreach ($op as $value){
                            if(strcmp($value,"and")==0 ||strcmp($value,"or")==0 ||strcmp($value,"not")==0){
                                $is_op[$i]= $value;
                                $i = $i+1;
                            }
                        }
                      
                        $j=0;
                        foreach ($option as $value) {
                            $condition .= "$is_op[$j] $value like '%$search%'";
                            $j= $j+1;
                        }
                    }
                }


                if (!isset($condition)) {
                    $stmt = $conn->prepare("SELECT distinct ebook.isbn isbn, ebook.title title, ebook.publisher publisher, ebook.year year FROM EBOOK,AUTHORS WHERE EBOOK.ISBN=AUTHORS.ISBN");
                } else {
                    $stmt = $conn->prepare("SELECT distinct ebook.isbn isbn, ebook.title title, ebook.publisher publisher, ebook.year year FROM EBOOK,AUTHORS WHERE EBOOK.ISBN=AUTHORS.ISBN $condition");
                }


                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <li>
                        <p class=book_Title>
                            <a href="moreinform.php?title=<?= $row['TITLE'] ?>"><?= $row['TITLE'] ?></a>
                        </p>
                        <div class=book_inform>
                            <p class=book_cover> <img src="image/book.jpg" alt="책 커버 이미지"> </p>
                            <p>출판사 : <?= $row['PUBLISHER'] ?></p>
                            <p>발행년도 : <?= $row['YEAR'] ?></p>
                        </div>
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>

    </div>

    <!--로그아웃-->
    <script>
        $(document).ready(function() {
            //로그아웃 버튼
            $("#logout_btn").click(function() {
                var action = $("#logout_btn").attr('formaction');
                $.ajax({
                    type: "POST",
                    url: action,
                    success: function(response) {
                        if (response.trim() == "success") {
                            alert("로그아웃 되었습니다.");
                            //로그아웃 후 로그인 페이지 이동
                            $(location).attr("href", "./loginview.html");
                        } else {
                            alert("로그아웃 실패");
                        }
                    }
                });
                return false;
            });
        });
    </script>
</body>

</html>