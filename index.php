<?php
require_once("connect.php");

// Обработка добавления нового заказа
if (isset($_POST['add_order'])) {
    $idReader = (int)$_POST['reader_id'];
    if ($idReader < 1 || $idReader > 36) {
        die('Ошибка: Читатель не найден. Пожалуйста, введите корректный ID читателя (от 1 до 36).');
    }
    $resourceType = $_POST['resource_type'];
    $orderDate = $_POST['order_date'];
    $quantity = (int)$_POST['quantity'];

    $idBook = null;
    $idJournal = null;
    $idElectronicResource = null;

    switch ($resourceType) {
        case 'book':
            $idBook = isset($_POST['book_title']) ? (int)$_POST['book_title'] : null;
            break;
        case 'journal':
            $idJournal = isset($_POST['journal_title']) ? (int)$_POST['journal_title'] : null;
            break;
        case 'electronic_resource':
            $idElectronicResource = isset($_POST['electronic_title']) ? (int)$_POST['electronic_title'] : null;
            break;
        default:
            die('Некорректный тип ресурса');
    }

    $title = null; 
    $queryTitle = '';

    if ($resourceType === 'book') {
        $queryTitle = "SELECT title FROM books WHERE id_book = $idBook";
    } elseif ($resourceType === 'journal') {
        $queryTitle = "SELECT title FROM journal WHERE id_journal = $idJournal";
    } elseif ($resourceType === 'electronic_resource') {
        $queryTitle = "SELECT title FROM electronic_resources WHERE id_electronic_resource = $idElectronicResource";
    }

    $resultTitle = mysqli_query($link, $queryTitle);
    if ($resultTitle && mysqli_num_rows($resultTitle) > 0) {
        $rowTitle = mysqli_fetch_assoc($resultTitle);
        $title = mysqli_real_escape_string($link, $rowTitle['title']);
    } else {
        die('Ошибка получения названия ресурса');
    }

    // Получение информации о читателе
    $readerQuery = "SELECT fio, email FROM readers WHERE id_reader = ?";
    if ($stmtReader = mysqli_prepare($link, $readerQuery)) {
        mysqli_stmt_bind_param($stmtReader, 'i', $idReader);
        mysqli_stmt_execute($stmtReader);
        mysqli_stmt_bind_result($stmtReader, $fio, $email);
        mysqli_stmt_fetch($stmtReader);
        mysqli_stmt_close($stmtReader);
    }

    $queryInsert = "INSERT INTO reservation (fio, email, type_resource, title, status, date_reservation, count, id_book, id_journal, id_electronic_resource, id_reader
              ) VALUES ('$fio', '$email','$resourceType','$title','Ожидает','$orderDate',$quantity,
                " . (isset($idBook) ? $idBook : 'NULL') . ",
                " . (isset($idJournal) ? $idJournal : 'NULL') . ",
                " . (isset($idElectronicResource) ? $idElectronicResource : 'NULL') . ",
                '$idReader'
              )";

    if (mysqli_query($link, $queryInsert)) {
        echo "<div class='alert success'>Заказ успешно оформлен!</div>";
    } else {
        echo "<div class='alert error'>Ошибка: " . mysqli_error($link) . "</div>";
    }
}

// Удаление заказа по ID
if (isset($_POST['delete'])) {
    $orderIdToDelete = (int)$_POST['order_id'];

    $queryDelete = "DELETE FROM reservation WHERE id_reservation = $orderIdToDelete";

    if (mysqli_query($link, $queryDelete)) {
        echo "<div class='alert success'>Заказ успешно удалён!</div>";
    } else {
        echo "<div class='alert error'>Ошибка при удалении заказа: " . mysqli_error($link) . "</div>";
    }
}

function displayOrders($link) {
    $queryDisplay = "SELECT * FROM reservation";
    $resultDisplay = mysqli_query($link, $queryDisplay);

    if ($resultDisplay && mysqli_num_rows($resultDisplay) > 0) {
        echo "<h2>Текущие заказы</h2>";
        echo "<table class='orders-table'>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Email</th>
                    <th>Тип ресурса</th>
                    <th>Название</th>
                    <th>Статус</th>
                    <th>Дата заказа</th>
                    <th>Количество</th>
                    <th>Действия</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($resultDisplay)) {
            echo "<tr>
                    <td>{$row['id_reservation']}</td>
                    <td>{$row['fio']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['type_resource']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['date_reservation']}</td>
                    <td>{$row['count']}</td>
                    <td>
                        <!-- Форма для удаления -->
                        <form method='post' action='' style='display:inline;'>
                            <input type='hidden' name='order_id' value='{$row['id_reservation']}'>
                            <input type='submit' name='delete' value='Удалить' class='btn delete'>
                        </form>

                        <!-- Форма для редактирования -->
                        <form method='post' action='edit_order.php' style='display:inline;'> <!-- Переход на страницу редактирования -->
                            <input type='hidden' name='order_id' value='{$row['id_reservation']}'>
                            <input type='submit' name='edit' value='Изменить' class='btn edit'>
                        </form>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert info'>Нет текущих заказов.</div>";
    }
}

displayOrders($link);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="index.css">
    <title>Управление заказами</title>
    <script>
        function showResourceFields(value) {
            document.getElementById('book_fields').style.display = value === 'book' ? 'block' : 'none';
            document.getElementById('journal_fields').style.display = value === 'journal' ? 'block' : 'none';
            document.getElementById('electronic_fields').style.display = value === 'electronic_resource' ? 'block' : 'none';
        }

        function fetchReaderData() {
            const readerId = document.getElementById('reader_id').value;
            if (readerId) {
                fetch(`get_reader.php?id=${readerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('reader_info').innerText = `ФИО: ${data.fio}, Email: ${data.email}`;
                        } else {
                            document.getElementById('reader_info').innerText = 'Читатель не найден.';
                        }
                    })
                    .catch(error => console.error('Ошибка:', error));
            }
        }
    </script>
</head>
<body>

<h2>Добавить новый заказ</h2>
<form method="post" action="">
    Читатель:
    <select id="reader_id" name="reader_id" required onchange="fetchReaderData()">
        <option value="">Выберите читателя</option>
        <?php
        $readersQuery = "SELECT id_reader, fio FROM readers";
        $readersResult = mysqli_query($link, $readersQuery);
        while ($readerRow = mysqli_fetch_assoc($readersResult)) {
            echo "<option value='{$readerRow['id_reader']}'>{$readerRow['fio']}</option>";
        }
        ?>
    </select><br>

        


    Тип ресурса:
    <select name="resource_type" required onchange="showResourceFields(this.value)">
        <option value="">Выберите тип</option>
        <option value="book">Книга</option>
        <option value="journal">Журнал</option>
        <option value="electronic_resource">Электронный ресурс</option>
    </select><br>

    <div id="book_fields" style="display:none;">
        Название книги:
        <select name="book_title">
            <?php
            $booksQuery = "SELECT id_book, title FROM books";
            $booksResult = mysqli_query($link, $booksQuery);
            while ($bookRow = mysqli_fetch_assoc($booksResult)) {
                echo "<option value='{$bookRow['id_book']}'>{$bookRow['title']}</option>";
            }
            ?>
        </select><br>
    </div>

    <div id="journal_fields" style="display:none;">
        Название журнала:
        <select name="journal_title">
            <?php
            $journalsQuery = "SELECT id_journal, title FROM journal";
            $journalsResult = mysqli_query($link, $journalsQuery);
            while ($journalRow = mysqli_fetch_assoc($journalsResult)) {
                echo "<option value='{$journalRow['id_journal']}'>{$journalRow['title']}</option>";
            }
            ?>
        </select><br>
    </div>

    <div id="electronic_fields" style="display:none;">
        Название электронного ресурса:
        <select name="electronic_title">
            <?php
            $electronicQuery = "SELECT id_electronic_resource, title FROM electronic_resources";
            $electronicResult = mysqli_query($link, $electronicQuery);
            while ($electronicRow = mysqli_fetch_assoc($electronicResult)) {
                echo "<option value='{$electronicRow['id_electronic_resource']}'>{$electronicRow['title']}</option>";
            }
            ?>
        </select><br>
    </div>

    Дата заказа:<input type="date" name="order_date" required><br>
    Количество:<input type="number" name="quantity" min="1" required><br>

    <input type="submit" name="add_order" value="Добавить заказ">
</form>
<form action="requests.php" method="get">
    <button type="submit">Запросы</button>
</form>

<?php
mysqli_close($link);
?>
</body>
</html>