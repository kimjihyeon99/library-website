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
    <link rel="stylesheet" href="moreinformstyle.css">
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


    <h2 id="sub_title">상세 정보</h2>

    <div class="container">
        <!-- 상세 정보 -->
        <div class=profile>
            <?php
            $title = $_GET['title'];
            $isbn = "";
            // 작가 정보 가져오기
            $stmt = $conn->prepare("SELECT * FROM EBOOK,AUTHORS where EBOOK.ISBN=AUTHORS.ISBN AND TITLE='$title'");
            $stmt->execute();

            $authors = array();

            $count = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $authors[$count] = $row['AUTHOR'];
                $count = $count + 1;
            }


            //예약자 정보 가져오기
            $reserve = array();
            $count = 0;
            $stmt = $conn->prepare("SELECT RESERVE.CNO CNO2 FROM EBOOK,RESERVE where EBOOK.ISBN=RESERVE.ISBN AND TITLE='$title'");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reserve[$count] = $row['CNO2'];
                $count = $count + 1;
            }


            //세부정보 가져오기
            $stmt = $conn->prepare("SELECT distinct ebook.isbn isbn, ebook.title title, ebook.publisher publisher, ebook.year year, ebook.cno cno FROM EBOOK,AUTHORS WHERE EBOOK.ISBN=AUTHORS.ISBN AND TITLE='$title'");
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $isbn = $row['ISBN'];
            ?>
            <!-- 책 상세정보 박스 -->
                <div class=profileheader>
                    <h3><?= $row['TITLE'] ?></h3>
                </div>
                <div class=profilecontent>
                    <div class=briefinfo>
                        <img src="image/book.jpg" alt="책 커버 이미지">
                    </div>
                    <div class=divprofile>
                        <table class=profiletable>
                            <tbody>
                                <tr>
                                    <th scope=row>ISBN</th>
                                    <td id="isbn"><?= $row['ISBN'] ?></td>
                                </tr>
                                <tr>
                                    <th scope=row>저자</th>
                                    <td><?php
                                        foreach ($authors as $value) {
                                            echo $value . " ";
                                        }
                                        ?></td>
                                </tr>
                                <tr>
                                    <th scope=row>출판사</th>
                                    <td><?= $row['PUBLISHER'] ?></td>
                                </tr>
                                <tr>
                                    <th scope=row>발행년도</th>
                                    <td><?= $row['YEAR'] ?></td>
                                </tr>
                            </tbody>

                        </table>

                    </div>
                </div>


            <?php
            }
            ?>
        </div>
        <!-- 책 대여 정보 박스 -->
        <div class=searchinfo>
            <div class=searchHeader>
                <h3>소장정보</h3>
            </div>

            <div class=searchContents>
                <table class=searchTable>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>도서상태</th>
                            <th>반납예정일</th>
                            <th>대출 or 예약</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt->execute();

                        $cnt = 1;
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if ($cnt == 2) {
                                break;
                            }
                        ?>
                            <tr>
                                <td><?php echo $cnt; ?></td>
                                <td>
                                    <?php
                                    if ($row['CNO'] == null) {
                                        echo "<div id=bable>대출 가능</div>";
                                    } else {
                                        echo "<div id=bnotable>대출 불가</div>";
                                    }
                                    ?>
                                </td>
                                <!-- 대출 불가한 상태이면 반납날짜 출력, 대출 가능한 상태이면 대출 버튼 출력 -->
                                <td>
                                    <?php
                                    if ($row['CNO'] != null) {
                                        echo $row['DATEDUE'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($row['CNO'] == null) {
                                        echo "<input type='button' id='borrow_btn' value='대출'>";
                                    } else {
                                        // 예약한 상태인지 확인
                                        $reserve_ok = false;
                                        foreach ($reserve as $value) {
                                            if ($value == $id) {
                                                $reserve_ok = true;
                                                break;
                                            }
                                        }
                                        // 예약한 상태이면 "예약취소 버튼" 아니면 "예약 버튼"
                                        if ($reserve_ok == false) {
                                            echo "<input type='button' id='reserve_btn' value='예약'>";
                                        } else {
                                            echo "<input type='button' id='reservecancel_btn' value='예약취소'>";
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>

                        <?php
                            $cnt = $cnt + 1;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--로그아웃-->
    <script>
        $(document).ready(function() {
            //대출 버튼
            $("#borrow_btn").click(function() {
                $.post("borrow.php?title=<?= $_GET['title'] ?>", {
                    data: 1
                }, function(data, status) {
                    if (data == "success") {
                        alert("대출 성공하였습니다.");
                    } else {
                        alert(data);
                    }
                    $(location).attr("href", "./moreinform.php?title=<?= $_GET['title'] ?>");
                });

            });
            //예약 버튼
            $("#reserve_btn").click(function() {
                $.post("reserve.php?title=<?= $_GET['title'] ?>", {
                    data: $("#isbn").text()
                }, function(data, status) {
                    if (data == "success") {
                        alert("예약 성공하였습니다.");
                    } else {
                        alert(data);
                    }
                    $(location).attr("href", "./moreinform.php?title=<?= $_GET['title'] ?>");
                });
            });
            //예약 취소 버튼
            $("#reservecancel_btn").click(function() {
                $.post("reservecancel.php?title=<?= $_GET['title'] ?>", {
                    data: $("#isbn").text()
                }, function(data, status) {
                    if (data == "success") {
                        alert("예약 취소 성공하였습니다.");
                    } else {
                        alert(data);
                    }
                    $(location).attr("href", "./moreinform.php?title=<?= $_GET['title'] ?>");
                });
            });

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