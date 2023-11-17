<?php
header('Access-Control-Allow-Origin: *'); // Corrected "Access-Control-Allow-Origin"
$result = array();
$result["error"] = false;
$result["message"] = "";

$host_name = "localhost"; // Change to your MySQL server hostname
$host_user = "root"; // Change to your MySQL username
$host_pass = ""; // Change to your MySQL password
$host_db = "vuedash"; // Change to the name of your MySQL database

$mysqli = new mysqli($host_name, $host_user, $host_pass, $host_db);

if ($mysqli->connect_error) {
    $result["is_db_connected"] = false;
    $result["connection_msg"] = "Error: " . $mysqli->connect_error;
} else {
    $result["is_db_connected"] = true;
    $result["connection_msg"] = "Successfully Connected!";
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == "read") {
    $sql = $mysqli->query("SELECT * FROM students");

    if ($sql) {
        $stdArr = array();
        while ($rows = $sql->fetch_assoc()) {
            $stdArr[] = $rows;
        }
        $result["students"] = $stdArr;
    } else {
        $result["error"] = true;
        $result["message"] = "Error executing the query: " . $mysqli->error;
    }
}

if ($action == "create") {
    // Handle the "create" action here
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    // Use prepared statements to prevent SQL injection
    $stmt_verify = $mysqli->prepare("SELECT * FROM `students` WHERE email = ?");
    $stmt_verify->bind_param("s", $email);
    $stmt_verify->execute();
    $stmt_verify->store_result();
    $num_rows = $stmt_verify->num_rows;

    if ($num_rows <= 0) {
        // Use prepared statements for the insert query
        $stmt_insert = $mysqli->prepare("INSERT INTO `students` (name, email, phone) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sss", $name, $email, $phone);
        $stmt_insert->execute();

        if ($stmt_insert->affected_rows > 0) {
            $result['message'] = "Successfully created student with name $name!";
        } else {
            $result["error"] = true;
            $result['message'] = "Failed to create student with name: $name";
        }

        $stmt_insert->close();
    } else {
        $result["error"] = true;
        $result['message'] = "Email $email already exists. Please choose another email.";
    }

    $stmt_verify->close();
}


if ($action == "update") {
    // Handle the "update" action here
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    // Use prepared statements to prevent SQL injection
    $stmt_verify = $mysqli->prepare("SELECT * FROM `students` WHERE email = ? AND id != ?");
    $stmt_verify->bind_param("si", $email, $id);
    $stmt_verify->execute();
    $stmt_verify->store_result();
    $num_rows = $stmt_verify->num_rows;

    if ($num_rows <= 0) {
        // Use prepared statements for the update query
        $stmt_update = $mysqli->prepare("UPDATE `students` SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt_update->bind_param("sssi", $name, $email, $phone, $id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            $result['message'] = "Successfully updated student with name $name!";
        } else {
            $result["error"] = true;
            $result['message'] = "Failed to update student with name: $name";
        }

    } else {
        $result["error"] = true;
        $result['message'] = "Email $email already exists. Please choose another email.";
    }

    $stmt_verify->close();
    $stmt_update->close();
}

if ($action == "deleteAll") {
    // Handle the "delete All" action here
    $sql = $mysqli->query("DELETE FROM students");

    if ($sql) {
        $result['message'] = "Successfully Deleted All Students";
    } else {
        $result["error"] = true;
        $result['message'] = "Failed to Delete All Students";

    }
}
if ($action == "delete") {
    $id = $_POST["id"];
    
    $sql_verify = $mysqli->query("SELECT * FROM `students` WHERE id = '$id'");
    $num_rows = $sql_verify->num_rows;

    if ($num_rows >= 1) {
        $delete_sql = $mysqli->query("DELETE FROM `students` WHERE id = '$id'");
        if ($delete_sql) {
            $result['message'] = "Successfully Deleted This Student with ID: $id";
        } else {
            $result["error"] = true;
            $result['message'] = "Failed to Delete This Student with ID: $id";
        }
    } else {
        $result["error"] = true;
        $result['message'] = "This Record Does Not Exist";
    }
}


// Return the result as JSON
echo json_encode($result);
?>
