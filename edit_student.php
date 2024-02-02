<?php
$host = 'db'; # nazwa usługi bazy danych w Docker Compose
$database = 'cyfrowauczelnia';
$username = 'studentadmin';
$password = 'mypassword';

$connect = mysqli_connect($host, $username, $password, $database);

$conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => $database));

if (!$conn) {
    die("Błąd połączenia z bazą danych: " . sqlsrv_errors());
}

// Sprawdź, czy jest przekazany identyfikator studenta
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Pobierz dane studenta do edycji
    $query = "SELECT * FROM students WHERE id = '$studentId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $studentData = mysqli_fetch_assoc($result);
    } else {
        echo "Błąd podczas pobierania danych studenta do edycji: " . mysqli_error($conn);
        exit;
    }
} else {
    echo "Brak identyfikatora studenta do edycji.";
    exit;
}

// Obsługa formularza edycji
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateStudent"])) {
    $newAlbumNumber = $_POST['album_number'];
    $newFirstName = $_POST['first_name'];
    $newLastName = $_POST['last_name'];
    $newEmail = $_POST['email'];
    $newPhone = $_POST['phone'];

    // Zaktualizuj dane studenta w bazie danych
    $updateQuery = "UPDATE students SET album_number = '$newAlbumNumber', first_name = '$newFirstName', last_name = '$newLastName', email = '$newEmail', phone = '$newPhone' WHERE id = '$studentId'";

    if (mysqli_query($conn, $updateQuery)) {
        echo "Dane studenta zostały pomyślnie zaktualizowane.";
    } else {
        echo "Błąd podczas aktualizacji danych studenta: " . mysqli_error($conn);
    }
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
    <form action="edit_student.php?id=<?php echo $studentId; ?>" method="post">
        
        <label for="album_number">Numer albumu:</label>
        <input type="text" name="album_number" id="album_number" value="<?php echo $studentData['album_number']; ?>">

        <label for="first_name">Imię:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo $studentData['first_name']; ?>">

        <label for="last_name">Nazwisko:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo $studentData['last_name']; ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $studentData['email']; ?>">

        <label for="phone">Telefon:</label>
        <input type="text" name="phone" id="phone" value="<?php echo $studentData['phone']; ?>">

        <input type="submit" name="updateStudent" value="Zaktualizuj dane studenta">
    </form>
    <a href="student_management.php">Powrót do strony głównej</a>
</body>
</html>
