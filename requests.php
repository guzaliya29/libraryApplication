<?php
require_once("connect.php");
?>
<h2>Введите ID читателя для получения количества заказов</h2>
<form method="post" action="">
    <label for="reader_id">ID Читателя:</label>
    <input type="number" name="reader_id" id="reader_id" required min="1">
    <input type="submit" name="fetch_orders" value="Получить количество заказов">
</form>
<?php
if (isset($_POST['fetch_orders'])) {
    $readerId = (int)$_POST['reader_id']; 

    $query = "
        SELECT COUNT(*) AS order_count 
        FROM reservation 
        WHERE id_reader = $readerId
    ";
    $result = mysqli_query($link, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $orderCount = $row['order_count'];

        echo "<h2>Количество заказов для читателя с ID $readerId: $orderCount</h2>";
    } else {
        echo "Ошибка выполнения запроса: " . mysqli_error($link);
    }
}

?>



<h2>Получить книги по ID читателя</h2>
<form method="post" action="">
    ID Читателя: <input type="number" name="reader_id" required>
    <input type="submit" name="fetch_books" value="Получить книги">
</form>
<?php
if (isset($_POST['fetch_books'])) {
    $readerId = (int)$_POST['reader_id'];

    $queryBooks = "
        SELECT r.title, r.date_reservation, r.count, rd.fio, rd.email 
        FROM reservation r 
        JOIN readers rd ON r.id_reader = rd.id_reader 
        WHERE r.id_reader = ? AND r.type_resource = 'book'
    ";

    if ($stmtBooks = mysqli_prepare($link, $queryBooks)) {
        mysqli_stmt_bind_param($stmtBooks, 'i', $readerId);
        mysqli_stmt_execute($stmtBooks);
        $resultBooks = mysqli_stmt_get_result($stmtBooks);

        if ($resultBooks && mysqli_num_rows($resultBooks) > 0) {
            echo "<h2>Информация о читателе и взятых книгах</h2>";
            echo "<table class='books-table' border='1'>
                    <tr>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Название книги</th>
                        <th>Дата заказа</th>
                        <th>Количество</th>
                    </tr>";

            $readerInfo = null;

            while ($row = mysqli_fetch_assoc($resultBooks)) {
                if ($readerInfo === null) {
                    $readerInfo = array(
                        'fio' => $row['fio'],
                        'email' => $row['email']
                    );
                }

                echo "<tr>
                        <td>{$readerInfo['fio']}</td>
                        <td>{$readerInfo['email']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['date_reservation']}</td>
                        <td>{$row['count']}</td>
                      </tr>";
            }

            echo "</table>";
        } else {
            echo "<div class='alert info'>У читателя с ID $readerId нет взятых книг.</div>";
        }
        mysqli_stmt_close($stmtBooks);
    } else {
        echo "<div class='alert error'>Ошибка при выполнении запроса.</div>";
    }
}
?>

<h2>Выберите язык для получения журналов</h2>
<form method="post" action="">
    <label for="language">Язык:</label>
    <select name="language" id="language" required>
        <option value="Русский">Русский</option>
        <option value="Английский">Английский</option>
        <option value="Французский">Французский</option>
    </select>
    <input type="submit" name="fetch_journals" value="Получить журналы">
</form>

<?php
if (isset($_POST['fetch_journals'])) {
    $selectedLanguage = mysqli_real_escape_string($link, $_POST['language']);

    $query = "
        SELECT id_journal, title, language, publishing_house, publishing_year, language, nunber_copies, location 
        FROM journal 
        WHERE language = '$selectedLanguage'
    ";
    $result = mysqli_query($link, $query);

    if ($result) {
        echo "<h2>Журналы на языке: $selectedLanguage</h2>";
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Язык</th>
                    <th>Дата публикации</th>
                    <th>Издатель</th>
                    <th>Количество экземпляров</th>
                    <th>Местоположение</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_journal']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['language']}</td>
                    <td>{$row['publishing_year']}</td>
                    <td>{$row['publishing_house']}</td>
                    <td>{$row['nunber_copies']}</td>
                    <td>{$row['location']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Ошибка выполнения запроса: " . mysqli_error($link);
    }
}

?>

<h2>Получить заказы по месяцу и году</h2>
<form method="post" action="">
    <label for="month">Месяц:</label>
    <select name="month" id="month" required>
        <?php
        for ($m = 1; $m <= 12; $m++) {
            echo "<option value='$m'>$m</option>"; 
        }
        ?>
    </select>

    <label for="year">Год:</label>
    <select name="year" id="year" required>
        <?php
        $currentYear = date("Y");
        for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
            echo "<option value='$y'>$y</option>";
        }
        ?>
    </select>

    <input type="submit" name="fetch_orders" value="Получить заказы">
</form>

<?php
if (isset($_POST['fetch_orders'])) {
    $selectedMonth = (int)$_POST['month'];
    $selectedYear = (int)$_POST['year'];

    $query = "
        SELECT * FROM reservation 
        WHERE MONTH(date_reservation) = $selectedMonth AND YEAR(date_reservation) = $selectedYear
    ";
    $result = mysqli_query($link, $query);

    if ($result) {
        echo "<h2>Заказы за $selectedMonth/$selectedYear</h2>";
        echo "<table border='1'>
                <tr>
                    <th>ID Заказа</th>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Тип ресурса</th>
                    <th>Название</th>
                    <th>Статус</th>
                    <th>Дата заказа</th>
                    <th>Количество</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_reservation']}</td>
                    <td>{$row['fio']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['type_resource']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['date_reservation']}</td>
                    <td>{$row['count']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Ошибка выполнения запроса: " . mysqli_error($link);
    }
}

?>

<h2>Выберите статус для получения заказов</h2>
<form method="post" action="">
    <label for="status">Статус:</label>
    <select name="status" id="status" required>
        <option value="Выполнено">Выполнено</option>
        <option value="Ожидает">Ожидание</option>
    </select>
    <input type="submit" name="fetch_orders" value="Получить заказы">
</form>

<?php
if (isset($_POST['fetch_orders'])) {
    $selectedStatus = mysqli_real_escape_string($link, $_POST['status']);

    $query = "
        SELECT * FROM reservation 
        WHERE status = '$selectedStatus'
    ";
    $result = mysqli_query($link, $query);

    if ($result) {
        echo "<h2>Заказы со статусом '$selectedStatus'</h2>";
        echo "<table border='1'>
                <tr>
                    <th>ID Заказа</th>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Тип ресурса</th>
                    <th>Название</th>
                    <th>Статус</th>
                    <th>Дата заказа</th>
                    <th>Количество</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_reservation']}</td>
                    <td>{$row['fio']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['type_resource']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['date_reservation']}</td>
                    <td>{$row['count']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Ошибка выполнения запроса: " . mysqli_error($link);
    }
}

?>

<h2>Введите возраст для поиска читателей</h2>
<form method="post" action="">
    <label for="age">Возраст:</label>
    <input type="number" name="age" id="age" required min="1">
    <input type="submit" name="fetch_readers" value="Найти читателей">
</form>

<?php
if (isset($_POST['fetch_readers'])) {
    $age = (int)$_POST['age']; 

    $query = "
        SELECT * 
        FROM readers 
        WHERE birthdate <= DATE_SUB(CURDATE(), INTERVAL $age YEAR)
    ";
    $result = mysqli_query($link, $query);

    if ($result) {
        echo "<h2>Читатели старше $age лет</h2>";
        echo "<table border='1'>
                <tr>
                    <th>ID Читателя</th>
                    <th>ФИО</th>
                    <th>Дата рождения</th>
                    <th>Email</th>
                    <th>Телефон</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id_reader']}</td>
                    <td>{$row['fio']}</td>
                    <td>{$row['birthdate']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['number_phone']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Ошибка выполнения запроса: " . mysqli_error($link);
    }
}

?>
<?php
// Запрос для получения топ 3 частых читателей
$query = "
    SELECT r.id_reader, r.fio, COUNT(res.id_reservation) AS order_count
    FROM readers r
    LEFT JOIN reservation res ON r.id_reader = res.id_reader
    GROUP BY r.id_reader
    ORDER BY order_count DESC
    LIMIT 3
";

$result = mysqli_query($link, $query);

if ($result) {
    echo "<h2>Топ 3 частых читателя</h2>";
    echo "<table border='1'>
            <tr>
                <th>ID Читателя</th>
                <th>ФИО</th>
                <th>Количество заказов</th>
            </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['id_reader']}</td>
                <td>{$row['fio']}</td>
                <td>{$row['order_count']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Ошибка выполнения запроса: " . mysqli_error($link);
}
?>
<?php
$query = "
    SELECT b.id_book, b.title, COUNT(r.id_reservation) AS order_count 
    FROM books b
    LEFT JOIN reservation r ON b.id_book = r.id_book 
    GROUP BY b.id_book 
    ORDER BY order_count DESC 
    LIMIT 3
";

$result = mysqli_query($link, $query);

if ($result) {
    echo "<h2>Топ 3 книги</h2>";
    echo "<table border='1'>
            <tr>
                <th>Название книги</th>
                <th>Количество заказов</th>
            </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['title']}</td>
                <td>{$row['order_count']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    // Вывод ошибки, если запрос не выполнен
    echo "<p style='text-align: center; color: red;'>Ошибка выполнения запроса: " . mysqli_error($link) . "</p>";
}
?>

<?php
mysqli_close($link);
?>
