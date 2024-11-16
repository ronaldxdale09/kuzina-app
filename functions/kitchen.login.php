<?php
include '../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Check if phone exists in the kitchens table
    $stmt = $conn->prepare("SELECT kitchen_id, fname,lname, email, password FROM kitchens WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Phone found, now check the password
        $stmt->bind_result($kitchen_id, $fname,$lname, $email, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Set cookies for kitchen user details
            setcookie('kitchen_id', $kitchen_id, time() + (86400 * 30), "/");
            setcookie('kitchen_fname', $fname, time() + (86400 * 30), "/");
            setcookie('kitchen_lname', $lname, time() + (86400 * 30), "/");

            setcookie('kitchen_user_email', $email, time() + (86400 * 30), "/");
            $_SESSION['kitchen_id'] = $kitchen_id;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid login credentials']);
    }

    $stmt->close();
    $conn->close();
}