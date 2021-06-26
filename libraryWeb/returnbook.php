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
                <li><a href="returnbook.php">대출조회/연장</a></li>
                <li>
                    <?php
                    if ($_SESSION["admin"] == true) {
                        echo "<a href='borrowlist.php'>대출기록</a>";
                    }
                    ?>
                </li>
            </ul>
        </div>
        <!-- 대출한 도서 정보 테이블 -->
        <div class="searchinfo">
            <form id="frm" name="frm" method="post">
                <table class="searchTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="all" title="전체선택" value="checkbox"></th>
                            <th>No.</th>
                            <th>제목</th>
                            <th>대출날짜</th>
                            <th>반납날짜</th>
                            <th>연장횟수</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $id = $_SESSION["id"];
                        $stmt = $conn->prepare("SELECT * FROM EBOOK where CNO='$id'");
                        $stmt->execute();

                        $cnt = 1;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            <tr>
                                <td><input type="checkbox" name="checkbox[]" value='<?= $row['TITLE'] ?>'></td>
                                <td><?= $cnt ?></td>
                                <td><?= $row['TITLE'] ?></td>
                                <td><?= $row['DATERENTED'] ?></td>
                                <td><?= $row['DATEDUE'] ?></td>
                                <td><?= $row['EXTTIMES'] ?>회</td>
                            </tr>

                        <?php
                            $cnt = $cnt + 1;
                        }
                        ?>
                    </tbody>
                </table>
                <div id="return_btns">
                    <input type="submit" id="return_btn" value="반납" formaction="return.php">
                    <input type="submit" id="extend_btn" value="연장" formaction="extend.php">
                </div>
            </form>
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