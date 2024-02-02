<?php
$host = 'db'; # nazwa usługi bazy danych w Docker Compose
$database = 'cyfrowauczelnia';
$username = 'studentadmin';
$password = 'mypassword';

$conn = sqlsrv_connect($host, array("UID" => $username, "PWD" => $password, "Database" => $database));

if ($conn === false) {
    $errors = sqlsrv_errors();
    $errorMessages = [];
    foreach($errors as $error) {
        $errorMessages[] = "SQLSTATE: " . $error['SQLSTATE'] . " Code: " . $error['code'] . " Message: " . $error['message'];
    }
    die("Błąd połączenia z bazą danych: " . implode(" | ", $errorMessages));
}


function dodajStudenta($studentData) {
    global $conn;

    $albumNumber = $studentData['album_number'];
    $firstName = $studentData['first_name'];
    $lastName = $studentData['last_name'];
    $email = $studentData['email'];
    $phone = $studentData['phone'];

    $query = "INSERT INTO students (album_number, first_name, last_name, email, phone) VALUES (?, ?, ?, ?, ?)";
    $params = array($albumNumber, $firstName, $lastName, $email, $phone);

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        echo "Student został pomyślnie dodany.";
    } else {
        echo "Błąd podczas dodawania studenta: " . sqlsrv_errors();
    }
}

function usunStudenta($studentId) {
    global $conn;

    $query = "DELETE FROM students WHERE id = ?";
    $params = array($studentId);

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt) {
        echo "Student został pomyślnie usunięty.";
    } else {
        echo "Błąd podczas usuwania studenta: " . sqlsrv_errors();
    }
}

function pobierzStudentow($search_query = null) {
    global $conn;

    $students = array();
    if ($search_query) {
        $query = "SELECT * FROM students WHERE album_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?";
        $params = array("%$search_query%", "%$search_query%", "%$search_query%", "%$search_query%", "%$search_query%");
    } else {
        $query = "SELECT * FROM students";
        $params = array();
    }

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt !== false) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $students[] = $row;
        }
    }

    return $students;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addStudent"])) {
    if (!empty($_POST['album_number']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['phone'])) {
        $studentData = array(
            'album_number' => $_POST['album_number'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        );

        dodajStudenta($studentData);
        header("Location: student_management.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteStudent"])) {
    $studentId = $_POST['student_id'];
    usunStudenta($studentId);
    header("Location: student_management.php");
    exit;
}

$search_query = isset($_GET['search']) ? $_GET['search'] : null;
$students = pobierzStudentow($search_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cyfrowa Uczelnia</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: #fff;
        }
    </style>
</head>
<body>
<h1>Dodaj studenta</h1>
<form action="student_management.php" method="post">
        
        <label for="album_number">Numer albumu:</label>
        <input type="text" name="album_number" id="album_number">

        <label for="first_name">Imię:</label>
        <input type="text" name="first_name" id="first_name">

        <label for="last_name">Nazwisko:</label>
        <input type="text" name="last_name" id="last_name">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">

        <label for="phone">Telefon:</label>
        <input type="text" name="phone" id="phone">

        <input type="submit" name="addStudent" value="Dodaj studenta">
    </form>


    <h1>Usuń studenta (kontrolne)</h1>
    <form action="student_management.php" method="post">
        <label for="student_id">ID studenta:</label>
        <input type="text" name="student_id" id="student_id">
        <input type="submit" name="deleteStudent" value="Usuń studenta">
    </form>

    <h1>Wyszukaj studenta</h1>
<form action="student_management.php" method="get">
    <label for="search">Wpisz szukaną frazę (imię, nazwisko, numer albumu, email, telefon):</label>
    <input type="text" name="search" id="search">
    <input type="submit" value="Szukaj">
</form>

    <h1>Lista studentów</h1>
    <?php
    $students = pobierzStudentow();

    if (!empty($students)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Numer albumu</th><th>Imię</th><th>Nazwisko</th><th>Email</th><th>Telefon</th><th>Akcje</th></tr>";

        foreach ($students as $student) {
            echo "<tr>";
            echo "<td>" . $student['id'] . "</td>";
            echo "<td>" . $student['album_number'] . "</td>";
            echo "<td>" . $student['first_name'] . "</td>";
            echo "<td>" . $student['last_name'] . "</td>";
            echo "<td>" . $student['email'] . "</td>";
            echo "<td>" . $student['phone'] . "</td>";
            echo "<td>";
            echo "<a class='edit-btn' href='edit_student.php?id=" . $student['id'] . "'>Edytuj</a>";
            echo "<a class='delete-btn' href='student_management.php?deleteStudent=1&student_id=" . $student['id'] . "'>Usuń</a>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Brak studentów w bazie danych.";
    }
    ?>

</body>
</html>

