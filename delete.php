<?php
  require_once 'database.php';

  if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // make sure it's an integer
    $query = "DELETE FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result) {
      header('Location: tables.php?msg=Product removed successfully');
      exit();
    } else {
      echo "Failed: " . mysqli_error($conn);
    }
  } else {
    echo "No ID provided.";
  }
?>
