
<?php
require "db.php";

$message = "";

/* ADD OR UPDATE GAME */
if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["save_game"])) {

    $id = (int)($_POST["id"] ?? 0);

    $game_name = trim($_POST["game_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $players = trim($_POST["players"] ?? "");
    $equipment = trim($_POST["equipment"] ?? "");
    $mechanics = trim($_POST["mechanics"] ?? "");
    $instructions = trim($_POST["instructions"] ?? "");
    $image = trim($_POST["image"] ?? "");

    if ($game_name === "") {
        $message = "Game name is required.";
    } else {

        if ($id > 0) {

            $stmt = $conn->prepare(
                "UPDATE games
                 SET game_name = ?,
                     description = ?,
                     players = ?,
                     equipment = ?,
                     mechanics = ?,
                     instructions = ?,
                     image = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "sssssssi",
                $game_name,
                $description,
                $players,
                $equipment,
                $mechanics,
                $instructions,
                $image,
                $id
            );

            $stmt->execute();
            $stmt->close();

            header("Location: admin.php?success=updated");
            exit;

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO games
                (game_name, description, players,
                 equipment, mechanics, instructions, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssss",
                $game_name,
                $description,
                $players,
                $equipment,
                $mechanics,
                $instructions,
                $image
            );

            $stmt->execute();
            $stmt->close();

            header("Location: admin.php?success=added");
            exit;
        }
    }
}

/* DELETE GAME */
if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["delete_game"])) {

    $id = (int)($_POST["id"] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare(
            "DELETE FROM games WHERE id = ?"
        );

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin.php?success=deleted");
    exit;
}

/* EDIT: LOAD SELECTED GAME */
$edit_game = null;

if (isset($_GET["edit"])) {

    $id = (int)$_GET["edit"];

    $stmt = $conn->prepare(
        "SELECT * FROM games WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $edit_game = $result->fetch_assoc();

    $stmt->close();
}

/* GET ALL GAMES */
$result = $conn->query(
    "SELECT * FROM games ORDER BY id DESC"
);

function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Project LaNL | Admin Dashboard</title>

    <link rel="stylesheet" href="admin.css">
</head>

<body>

<div class="admin-container">

    <header class="admin-header">
        <div>
            <p class="eyebrow">PROJECT LANL</p>
            <h1>Admin Dashboard</h1>
            <p>Manage your Laro ng Lahi games.</p>
        </div>

        <a href="Homepage.html" class="back-link">
            View Website
        </a>
    </header>

    <?php if (isset($_GET["success"])): ?>
        <p class="success-message">
            <?php
            if ($_GET["success"] === "added") {
                echo "Game added successfully!";
            } elseif ($_GET["success"] === "updated") {
                echo "Game updated successfully!";
            } elseif ($_GET["success"] === "deleted") {
                echo "Game deleted successfully!";
            }
            ?>
        </p>
    <?php endif; ?>

    <?php if ($message !== ""): ?>
        <p class="error-message">
            <?= e($message) ?>
        </p>
    <?php endif; ?>

    <!-- GAME FORM -->
    <section class="form-section">

        <h2>
            <?= $edit_game ? "Edit Game" : "Add New Game" ?>
        </h2>

        <form method="POST" action="admin.php">

            <input type="hidden"
                   name="id"
                   value="<?= e($edit_game["id"] ?? "") ?>">

            <label>Game Name</label>
            <input type="text"
                   name="game_name"
                   required
                   value="<?= e($edit_game["game_name"] ?? "") ?>">

            <label>Description</label>
            <textarea name="description"
                      rows="4"><?= e($edit_game["description"] ?? "") ?></textarea>

            <label>Number of Players</label>
            <input type="text"
                   name="players"
                   placeholder="At least 3 players"
                   value="<?= e($edit_game["players"] ?? "") ?>">

            <label>Equipment</label>
            <textarea name="equipment"
                      rows="3"><?= e($edit_game["equipment"] ?? "") ?></textarea>

            <label>Game Mechanics</label>
            <textarea name="mechanics"
                      rows="4"><?= e($edit_game["mechanics"] ?? "") ?></textarea>

            <label>Instructions</label>
            <textarea name="instructions"
                      rows="5"><?= e($edit_game["instructions"] ?? "") ?></textarea>

            <label>Image Filename</label>
            <input type="text"
                   name="image"
                   placeholder="langit-lupa.jpg"
                   value="<?= e($edit_game["image"] ?? "") ?>">

            <div class="form-buttons">

                <button type="submit" name="save_game">
                    <?= $edit_game ? "Update Game" : "Add Game" ?>
                </button>

                <?php if ($edit_game): ?>
                    <a href="admin.php" class="cancel-button">
                        Cancel
                    </a>
                <?php endif; ?>

            </div>

        </form>

    </section>

    <!-- GAME LIST -->
    <section class="games-section">

        <h2>Manage Games</h2>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Game Name</th>
                        <th>Players</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($game = $result->fetch_assoc()): ?>

                    <tr>
                        <td><?= e($game["id"]) ?></td>

                        <td><?= e($game["game_name"]) ?></td>

                        <td><?= e($game["players"]) ?></td>

                        <td class="actions">

                            <a href="admin.php?edit=<?= e($game["id"]) ?>"
                               class="edit-button">
                                Edit
                            </a>

                            <form method="POST"
                                  action="admin.php"
                                  onsubmit="return confirm('Delete this game?');">

                                <input type="hidden"
                                       name="id"
                                       value="<?= e($game["id"]) ?>">

                                <button type="submit"
                                        name="delete_game"
                                        class="delete-button">
                                    Delete
                                </button>

                            </form>

                        </td>
                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>

</body>
</html>
