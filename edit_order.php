<?php
require_once("connect.php");

if (isset($_POST['order_id']) || isset($_GET['order_id'])) {
    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : (int)$_GET['order_id'];

    $query = "SELECT * FROM reservation WHERE id_reservation = $orderId";
    $result = mysqli_query($link, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo "<div class='alert error'>Заказ не найден.</div>";
        exit;
    }

    $order = mysqli_fetch_assoc($result);
} else {
    echo "<div class='alert error'>ID заказа не передан.</div>";
    exit;
}

if (isset($_POST['update_order'])) {
    $type_resource = mysqli_real_escape_string($link, $_POST['resource_type']);
    $date_reservation = mysqli_real_escape_string($link, $_POST['order_date']);
    $count = (int)$_POST['quantity'];
    $status = mysqli_real_escape_string($link, $_POST['status']);

    if ($type_resource === 'book') {
        $titleId = (int)$_POST['book_title'];
        $titleQuery = "SELECT title FROM books WHERE id_book = $titleId";
        $titleResult = mysqli_query($link, $titleQuery);
        $titleRow = mysqli_fetch_assoc($titleResult);
        $title = mysqli_real_escape_string($link, $titleRow['title']);
    } elseif ($type_resource === 'journal') {
        $titleId = (int)$_POST['journal_title'];
        $titleQuery = "SELECT title FROM journal WHERE id_journal = $titleId";
        $titleResult = mysqli_query($link, $titleQuery);
        $titleRow = mysqli_fetch_assoc($titleResult);
        $title = mysqli_real_escape_string($link, $titleRow['title']);
    } elseif ($type_resource === 'electronic_resource') {
        $titleId = (int)$_POST['electronic_title'];
        $titleQuery = "SELECT title FROM electronic_resources WHERE id_electronic_resource = $titleId";
        $titleResult = mysqli_query($link, $titleQuery);
        $titleRow = mysqli_fetch_assoc($titleResult);
        $title = mysqli_real_escape_string($link, $titleRow['title']);
    } else {
        echo "<div class='alert error'>Некорректный тип ресурса.</div>";
        exit;
    }

    $updateQuery = "UPDATE reservation SET type_resource='$type_resource', title='$title', date_reservation='$date_reservation', count=$count, status='$status' WHERE id_reservation=$orderId";

    if (mysqli_query($link, $updateQuery)) {
        header("Location: index.php");
        exit;
    } else {
        echo "<div class='alert error'>Ошибка при обновлении заказа: " . mysqli_error($link) . "</div>";
    }
}

$booksResult = mysqli_query($link, "SELECT id_book, title FROM books");
$journalsResult = mysqli_query($link, "SELECT id_journal, title FROM journal");
$electronicResult = mysqli_query($link, "SELECT id_electronic_resource, title FROM electronic_resources");

?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="index.css">
<title>Редактирование заказа</title>
</head>
<body>

<h2>Редактировать заказ</h2>
<form method="post" action="">
   <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id_reservation']); ?>">

   Тип ресурса:
   <select name="resource_type" required onchange="showResourceFields(this.value)">
       <option value="">Выберите тип</option>
       <option value="book" <?php echo ($order['type_resource'] == 'book') ? 'selected' : ''; ?>>Книга</option>
       <option value="journal" <?php echo ($order['type_resource'] == 'journal') ? 'selected' : ''; ?>>Журнал</option>
       <option value="electronic_resource" <?php echo ($order['type_resource'] == 'electronic_resource') ? 'selected' : ''; ?>>Электронный ресурс</option>
   </select><br>

   <div id="book_fields" style="<?php echo ($order['type_resource'] == 'book') ? 'display:block;' : 'display:none;'; ?>">
       Название книги:
       <select name="book_title">
           <?php while ($bookRow = mysqli_fetch_assoc($booksResult)) { 
               $selected = ($bookRow['id_book'] == $order['title']) ? 'selected' : ''; 
               echo "<option value='{$bookRow['id_book']}' {$selected}>{$bookRow['title']}</option>";
           } ?>
       </select><br>
   </div>

   <div id="journal_fields" style="<?php echo ($order['type_resource'] == 'journal') ? 'display:block;' : 'display:none;'; ?>">
       Название журнала:
       <select name="journal_title">
           <?php while ($journalRow = mysqli_fetch_assoc($journalsResult)) { 
               $selected = ($journalRow['id_journal'] == $order['title']) ? 'selected' : ''; 
               echo "<option value='{$journalRow['id_journal']}' {$selected}>{$journalRow['title']}</option>";
           } ?>
       </select><br>
   </div>

   <div id="electronic_fields" style="<?php echo ($order['type_resource'] == 'electronic_resource') ? 'display:block;' : 'display:none;'; ?>">
       Название электронного ресурса:
       <select name="electronic_title">
           <?php while ($electronicRow = mysqli_fetch_assoc($electronicResult)) { 
               $selected = ($electronicRow['id_electronic_resource'] == $order['title']) ? 'selected' : ''; 
               echo "<option value='{$electronicRow['id_electronic_resource']}' {$selected}>{$electronicRow['title']}</option>";
           } ?>
       </select><br>
   </div>

   Дата заказа:<input type="date" name="order_date" value="<?php echo htmlspecialchars($order['date_reservation']); ?>" required><br>
   Количество:<input type="number" name="quantity" min="1" value="<?php echo htmlspecialchars($order['count']); ?>" required><br>

   Статус:
   <select name="status">
       <option value="Ожидает" <?php echo ($order['status'] == 'Ожидает') ? 'selected' : ''; ?>>Ожидает</option>
       <option value="Выполнено" <?php echo ($order['status'] == 'Выполнено') ? 'selected' : ''; ?>>Выполнено</option>
   </select><br>

   <input type="submit" name="update_order" value="Сохранить изменения">
</form>

<script>
function showResourceFields(value) {
   document.getElementById('book_fields').style.display = value === 'book' ? 'block' : 'none';
   document.getElementById('journal_fields').style.display = value === 'journal' ? 'block' : 'none';
   document.getElementById('electronic_fields').style.display = value === 'electronic_resource' ? 'block' : 'none';
}
</script>

</body>
</html>

<?php
mysqli_close($link);
?>
