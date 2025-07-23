<?php
include '../includes/db.php';
session_start();

// Ensure the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Product ID is missing.";
    exit();
}

$productId = $_GET['id'];

// Fetch the current product details from the database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// If product not found, show an error
if (!$product) {
    echo "Product not found.";
    exit();
}

// Handle form submission to update product details
if (isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // If a new image is uploaded, move it to the images folder
    if ($image) {
        move_uploaded_file($_FILES['image']['tmp_name'], "../images/$image");
    } else {
        // If no new image, retain the old image
        $image = $product['image'];
    }

    // Update the product details in the database
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $price, $description, $image, $productId]);

    // Show success message and redirect
    echo "Product updated successfully!";
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #28a745;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-size: 1.1em;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #218838;
        }

        .btn-back {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            text-align: center;
            background-color: #007bff;
            color: white;
            font-size: 1.1em;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        small {
            display: block;
            margin-top: 10px;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }

        .current-image {
            text-align: center;
            margin: 10px 0;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
        }

        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>

    <?php if (isset($error_message)): ?>
        <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($product['price']); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?= htmlspecialchars($product['description']); ?></textarea>

        <label for="image">Image:</label>
        <input type="file" name="image" id="image">
        
        <div class="current-image">
            <small>Current image: <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="Product Image" width="100"></small>
        </div>

        <button type="submit" name="update_product">Update Product</button>
    </form>

    <a href="manage_products.php" class="btn-back">Back to Manage Products</a>
</div>

</body>
</html>
