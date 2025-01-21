<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_portal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug the POST data
error_log("POST data received: " . print_r($_POST, true));

try {
    // Insert into Preliminary Information table
    $sql1 = "INSERT INTO preliminary_info (course, semester, source) VALUES (?, ?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param(
        "sss",
        $_POST['course'], 
        $_POST['semester'], 
        implode(", ", $_POST['source'])
    );
    $stmt1->execute();
    $preliminary_id = $stmt1->insert_id;

    // Insert into Personal Information table
    $sql2 = "INSERT INTO personal_info (full_name, place_of_birth, race, dob, marital_status, telephone, email, permanent_address, gender, current_address) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);

    // Check if prepare was successful
    if (!$stmt2) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt2->bind_param(
        "ssssssssss",
        $_POST['full_name'],
        $_POST['place_of_birth'], 
        $_POST['race'], 
        $_POST['dob'], 
        $_POST['marital_status'], 
        $_POST['telephone'], 
        $_POST['email'], 
        $_POST['permanent_address'], 
        $_POST['gender'], 
        $_POST['current_address']
    );

    // Execute and check for errors
    if (!$stmt2->execute()) {
        die("Execute failed: " . $stmt2->error);
    }

    $personal_id = $stmt2->insert_id;
    error_log("Inserted personal_info ID: " . $personal_id);

    // Insert into Parent Information table
    $sql3 = "INSERT INTO parent_info (father_name, mother_name, parent_tel, parent_address, parent_email) VALUES (?, ?, ?, ?, ?)";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param(
        "sssss",
        $_POST['father_name'], 
        $_POST['mother_name'], 
        $_POST['parent_tel'], 
        $_POST['parent_address'], 
        $_POST['parent_email']
    );
    $stmt3->execute();
    $parent_id = $stmt3->insert_id;

    // Insert into Emergency Contact table
    $sql4 = "INSERT INTO emergency_contact (emergency_name, emergency_relation, emergency_email) VALUES (?, ?, ?)";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param(
        "sss",
        $_POST['emergency_name'], 
        $_POST['emergency_relation'], 
        $_POST['emergency_email']
    );
    $stmt4->execute();
    $emergency_id = $stmt4->insert_id;

    // Close statements and connection
    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
    $stmt4->close();
    $conn->close();

    // If everything is successful
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully!']);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
