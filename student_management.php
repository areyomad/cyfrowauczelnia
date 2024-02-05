<?php
$host = 'db'; 
$database = 'cyfrowauczelnia';
$username = 'studentadmin';
$password = 'mypassword';


$connect = mysqli_connect($host, $username, $password, $database);


if (!$connect) {
    die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
}


function dodajStudenta($studentData) {
    global $connect;

    $albumNumber = $studentData['album_number'];
    $firstName = $studentData['first_name'];
    $lastName = $studentData['last_name'];
    $email = $studentData['email'];
    $phone = $studentData['phone'];

    $query = "INSERT INTO students (album_number, first_name, last_name, email, phone) VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $albumNumber, $firstName, $lastName, $email, $phone);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Student został pomyślnie dodany.";
        echo "<a href='student_management.php' class='edit-btn'>Wróć do listy studentów</a>";
    } else {
        echo "Błąd podczas dodawania studenta: " . mysqli_error($connect);
    }
    mysqli_stmt_close($stmt);
}

function usunStudenta($studentId) {
    global $connect;

    $query = "DELETE FROM students WHERE id = ?";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Student został pomyślnie usunięty.";
        echo "<a href='student_management.php' class='edit-btn'>Wróć do listy studentów</a>";
    } else {
        echo "Błąd podczas usuwania studenta: " . mysqli_error($connect);
    }
    mysqli_stmt_close($stmt);
}


function pobierzStudentow() {
    global $connect;

    $query = "SELECT * FROM students";
    $result = mysqli_query($connect, $query);
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);

    return $students;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["addStudent"])) {
        if (!empty($_POST['album_number']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['phone'])) {
            $studentData = array(
                'album_number' => $_POST['album_number'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone']
            );

            dodajStudenta($studentData);
            exit;
        }
    } elseif (isset($_POST["deleteStudent"])) {
        $studentId = $_POST['student_id'];
        usunStudenta($studentId);
        exit;
    }
}

$students = pobierzStudentow();
?>


<!DOCTYPE html>
<html>
<head>
<title>Cyfrowa Uczelnia - Zarządzanie Studentami</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="submit"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 20px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: auto;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .edit-btn, .delete-btn {
    text-decoration: none;
    padding: 6px 12px;
    color: white;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    display: inline-block;
    margin: 0 2px;
    font-size: 14px;
}

.edit-btn {
    background-color: #4CAF50;
}

.delete-btn {
    background-color: #d9534f;
}


form.inline {
    display: inline-block;
    margin: 0;
    padding: 0;
}


form.inline:before {
    content: " ";
    display: inline-block;
}


.edit-btn:hover, .delete-btn:hover {
    opacity: 0.8;
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

    <h1>Dodaj studenta</h1>
<input type="text" id="searchQuery" onkeyup="searchStudents()" placeholder="Wpisz dane do wyszukiwania...">


    <h1>Lista studentów</h1>
    <?php
    $students = pobierzStudentow();

    if (!empty($students)) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Numer albumu</th><th>Imię</th><th>Nazwisko</th><th>Email</th><th>Telefon</th><th>Akcje</th></tr>";

        foreach ($students as $student) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($student['id']) . "</td>";
            echo "<td>" . htmlspecialchars($student['album_number']) . "</td>";
            echo "<td>" . htmlspecialchars($student['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($student['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($student['email']) . "</td>";
            echo "<td>" . htmlspecialchars($student['phone']) . "</td>";
            echo "<td>";

            echo "<a class='edit-btn' href='edit_student.php?id=" . ($student['id']) . "'>Edytuj</a>";
            echo "<form class='inline' method='post' action='student_management.php' onsubmit='return confirm(\"Czy na pewno chcesz usunąć tego studenta?\");'>";
            echo "<input type='hidden' name='student_id' value='" . ($student['id']) . "'>";
            echo "<input type='submit' name='deleteStudent' value='Usuń' class='delete-btn'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Brak studentów w bazie danych.";
    }
    ?>

<script>
function searchStudents() {
    var input = document.getElementById("searchQuery");
    var filter = input.value.toLowerCase();
    var nodes = document.getElementsByTagName('tr');

    for (i = 1; i < nodes.length; i++) { 
        if (nodes[i].textContent.toLowerCase().includes(filter)) {
            nodes[i].style.display = "";
        } else {
            nodes[i].style.display = "none";
        }
    }
}
</script>


</body>
</html>