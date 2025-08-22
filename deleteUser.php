<?php
  require_once 'database.php';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM users_entered WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result) {
      header('Location: users.php?msg=User removed successfully');
      exit();
    } else {
      echo "Failed: " . mysqli_error($conn);
    }
  } else {
    echo "No ID provided.";
  }
?>
