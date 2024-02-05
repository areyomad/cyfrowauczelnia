<?php
$host = 'db';
$database = 'cyfrowauczelnia';
$username = 'studentadmin';
$password = 'mypassword';


$connect = mysqli_connect($host, $username, $password, $database);


if (!$connect) {
    die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
}


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $studentId = $_GET['id'];


    $query = "SELECT * FROM students WHERE id = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($studentData = mysqli_fetch_assoc($result)) {

    } else {
        echo "Błąd podczas pobierania danych studenta do edycji: " . mysqli_error($connect);
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Brak identyfikatora studenta do edycji.";
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateStudent"])) {
    $newAlbumNumber = $_POST['album_number'];
    $newFirstName = $_POST['first_name'];
    $newLastName = $_POST['last_name'];
    $newEmail = $_POST['email'];
    $newPhone = $_POST['phone'];


    $updateQuery = "UPDATE students SET album_number = ?, first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = mysqli_prepare($connect, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssssi", $newAlbumNumber, $newFirstName, $newLastName, $newEmail, $newPhone, $studentId);

    if (mysqli_stmt_execute($stmt)) {
        echo "Dane studenta zostały pomyślnie zaktualizowane.";

    } else {
        echo "Błąd podczas aktualizacji danych studenta: " . mysqli_error($connect);
    }
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cyfrowa Uczelnia - Edytuj studenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="email"] {
            width: 300px;
            padding: 5px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Edytuj dane studenta</h1>
    <form action="edit_student.php?id=<?= htmlspecialchars($studentId); ?>" method="post">
        
        <label for="album_number">Numer albumu:</label>
        <input type="text" name="album_number" id="album_number" value="<?= htmlspecialchars($studentData['album_number']); ?>">

        <label for="first_name">Imię:</label>
        <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($studentData['first_name']); ?>">

        <label for="last_name">Nazwisko:</label>
        <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($studentData['last_name']); ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($studentData['email']); ?>">

        <label for="phone">Telefon:</label>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($studentData['phone']); ?>">

        <input type="submit" name="updateStudent" value="Zaktualizuj dane studenta">
    </form>
    <a href="student_management.php">Powrót do strony głównej</a>
</body>
</html>
