<?php
include '../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Check if phone exists
    $stmt = $conn->prepare("SELECT customer_id, first_name, last_name, email, password FROM customers WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Phone found, now check the password
        $stmt->bind_result($customer_id, $first_name, $last_name, $email, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Set cookies for user details
            setcookie('user_id', $customer_id, time() + (86400 * 30), "/");
            setcookie('user_fname', $first_name, time() + (86400 * 30), "/");
            setcookie('user_lname', $last_name, time() + (86400 * 30), "/");
            setcookie('user_email', $email, time() + (86400 * 30), "/");

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
?>
