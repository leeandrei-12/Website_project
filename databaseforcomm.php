<?php
// Connect to MySQL database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "discussion";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// CREATE - Insert form data if submitted
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $comment  = mysqli_real_escape_string($conn, $_POST['comment']);

    $sql = "INSERT INTO `comments` (`Username`, `Comment`) VALUES ('$username', '$comment')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: index.html");
        exit();
    } else {
        die(mysqli_error($conn));
    }
}

// UPDATE - Save edited comment if submitted
if (isset($_POST['update'])) {
    $id       = (int) $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $comment  = mysqli_real_escape_string($conn, $_POST['comment']);

    $sql = "UPDATE `comments` SET `Username` = '$username', `Comment` = '$comment' WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: databaseforcomm.php");
        exit();
    } else {
        die(mysqli_error($conn));
    }
}

// DELETE - Delete comment if delete link is clicked
if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    $sql = "DELETE FROM `comments` WHERE id = $delete_id";
    if ($conn->query($sql)) {
        header("Location: databaseforcomm.php");
        exit();
    } else {
        die(mysqli_error($conn));
    }
}

// FETCH ONE RECORD FOR EDITING
$edit_row = null;
if (isset($_GET['updateid'])) {
    $update_id = (int) $_GET['updateid'];
    $edit_res = $conn->query("SELECT * FROM `comments` WHERE id = $update_id");
    if ($edit_res && $edit_res->num_rows > 0) {
        $edit_row = $edit_res->fetch_assoc();
    }
}

// READ - Fetch all comments
$sql_view = "SELECT * FROM `comments`";
$result = $conn->query($sql_view);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comments</title>
    <style>
  body {
    margin: 0;
    background: transparent;
    color: #f3ead8;
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 0.88rem;
  }
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    padding: 8px 10px;
    border-bottom: 1px solid rgba(217, 155, 56, 0.25);
    text-align: left;
  }
  th {
    color: #d99b38;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
  }
  a {
    color: #d99b38;
    text-decoration: none;
    font-weight: 600;
    margin-right: 8px;
  }
  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
    <?php if ($edit_row) { ?>
        <div class="edit-box">
            <form method="post" action="databaseforcomm.php">
                <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
                <input type="text" name="username" value="<?php echo htmlspecialchars($edit_row['Username']); ?>" required>
                <textarea name="comment" rows="2" required><?php echo htmlspecialchars($edit_row['Comment']); ?></textarea>
                <button type="submit" name="update">Save Update</button>
                <a href="databaseforcomm.php">Cancel</a>
            </form>
        </div>
    <?php } ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Comment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . htmlspecialchars($row['Username']) . "</td>
                        <td>" . htmlspecialchars($row['Comment']) . "</td>
                        <td>
                            <a href='databaseforcomm.php?updateid=" . $row['id'] . "'>Update</a><br>
                            <a href='databaseforcomm.php?delete=" . $row['id'] . "'>Delete</a>
                        </td>
                    </tr>";
                }
            }
            ?>
        </tbody>
    </table>
</body>
</html>