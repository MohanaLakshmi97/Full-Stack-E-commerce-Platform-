<?php
include('../includes/db.php');
// Include the database connection

// Admin user details
$username = 'admin'; 
$email = 'admin@example.com';
$password = 'adminpassword';  // Plain text password (this will be hashed)

// Hash the password using bcrypt
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the admin user into the database
$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $email, $hashed_password, 'admin']);

echo "Admin user created successfully!";
?>
