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
    <link rel="stylesheet" href="returnbookstyle.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">

    </style>
    <title>BOOK LIST</title>
</head>

<body>
    <div id="header">
        <div id="login_status">
            <?php
            session_start();
            if (isset($_SESSION["id"])) {
                $id = $_SESSION["id"];
                echo "<div id='userid'>" . $id . "</div>";
            } else {
                header('Location: ./loginview.html');
            }
            ?>

            <input type="button" id="logout_btn" value="로그아웃" formaction="logout.php">
        </div>

        <div id="manu_bar">
            <ul>
                <li><a href="booklist.php">도서선택</a></li>
                <li><a href="returnbook.php">대출현황</a></li>
            </ul>
        </div>
    </div>

    <h2 id="sub_title">대출 현황</h2>

    <div class="container">
        <!-- sub menubar -->
        <div class="sub_menubar">
            <ul>
                <li><a href="returnbook.php">대출조회/연장</li>
                <li><a href="borrowlist.php">대출기록</li>
            </ul>
        </div>
        <!-- query table -->
        <div class="searchinfo">
            <table class="searchTable">
                <caption>대출기록과 고객 정보</caption>
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>빌린날짜</th>
                        <th>반납날짜</th>
                        <th>고객번호</th>
                        <th>이름</th>
                        <th>메일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT ISBN, PREVIOUSRENTAL.DATERENTED DATERENTED 
                        ,PREVIOUSRENTAL.DATERETURNED DATERETURNED, CUSTOMER.CNO CNO, 
                        CUSTOMER.NAME NAME , CUSTOMER.EMAIL EMAIL FROM CUSTOMER,PREVIOUSRENTAL WHERE CUSTOMER.CNO=PREVIOUSRENTAL.CNO");
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td><?= $row['ISBN'] ?></td>
                            <td><?= $row['DATERENTED'] ?></td>
                            <td><?= $row['DATERETURNED'] ?></td>
                            <td><?= $row['CNO'] ?></td>
                            <td><?= $row['NAME'] ?></td>
                            <td><?= $row['EMAIL'] ?></td>
                        </tr>

                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- 출판사 통계 정보 테이블 -->
        <div class="searchinfo">
            <table class="searchTable">
                <caption>출판사 통계</caption>
                <thead>
                    <tr>
                        <th>출판사</th>
                        <th>합계</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT PUBLISHER, COUNT(PUBLISHER) 출판사_COUNT FROM EBOOK GROUP BY PUBLISHER ");
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td><?= $row['PUBLISHER'] ?></td>
                            <td><?= $row['출판사_COUNT'] ?></td>
                        </tr>

                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- 연장횟수 순위 테이블 -->
        <div class="searchinfo">
            <table class="searchTable">
                <caption>연장횟수 랭킹</caption>
                <thead>
                    <tr>
                        <th>제목</th>
                        <th>고객번호</th>
                        <th>연장횟수</th>
                        <th>순위</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT TITLE, CNO, EXTTIMES, RANK() OVER (ORDER BY EXTTIMES DESC) RANK FROM EBOOK WHERE EXTTIMES IS NOT NULL");
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td><?= $row['TITLE'] ?></td>
                            <td><?= $row['CNO'] ?></td>
                            <td><?= $row['EXTTIMES'] ?></td>
                            <td><?= $row['RANK'] ?></td>
                        </tr>

                    <?php
                    }
                    ?>
                </tbody>
            </table>
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
                            alert("로그인 실패");

                        }
                    }
                });
                return false;
            });
        });
    </script>
</body>

</html>