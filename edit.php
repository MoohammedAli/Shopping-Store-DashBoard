<?php
  require_once 'database.php';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM products WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
  } else {
    echo "No product selected.";
    exit();
  }

  // Fetch existing images for this product
  $images = [];
  $img_query = "SELECT * FROM product_images WHERE product_id = $id";
  $img_result = mysqli_query($conn, $img_query);
  if ($img_result && mysqli_num_rows($img_result) > 0) {
    while ($img = mysqli_fetch_assoc($img_result)) {
      $images[] = $img;
    }
  }

  if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat = $_POST['cat'];
    $brand = $_POST['brand'];
    $count = $_POST['count'];
    $des = $_POST['des'];

    $errors = [];

    // Validate required fields
    if (
      empty($name) || empty($price) ||
      empty($cat) || empty($brand) || empty($count) || empty($des)
    ) {
      $errors[] = "Please fill in all fields.";
    }
    $allowedTypes = ['jpg','jpeg','png','gif'];
    $files = $_FILES['images'];
    $newImages = [];
    if (!empty($files['name'][0])) {
      foreach($files['name'] as $key => $fileName){
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if(!in_array($ext, $allowedTypes)){
          $errors[] = "Invalid file type for $fileName";
        } else {
          $newImages[] = $key;
        }
      }
    }

    if (empty($errors)) {
      // Update product info
      $query = "UPDATE products SET name=?, price=?, cat=?, brand=?, count=?, des=? WHERE id=?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, 'sdiiisi', $name, $price, $cat, $brand, $count, $des, $id);

      if (mysqli_stmt_execute($stmt)) {
        // Handle new images upload
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }
        foreach($newImages as $key){
          $fileName = $files['name'][$key];
          $tmpName = $files['tmp_name'][$key];
          $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
          $newFileName = uniqid('img_').'.'.$ext;
          $targetPath = $uploadDir . $newFileName;
          if(move_uploaded_file($tmpName, $targetPath)){
            $stmtImg = $conn->prepare("INSERT INTO product_images (product_id, filename) VALUES (?, ?)");
            $stmtImg->bind_param("is", $id, $newFileName);
            $stmtImg->execute();
            $stmtImg->close();
          } else {
            $errors[] = "Failed to upload $fileName";
          }
        }
        if (empty($errors)) {
          header('location: tables.php?msg=Product updated successfully');
          exit();
        }
      } else {
        $errors[] = "Error updating product.";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
  <style>
    .des {
      height: 120px;
    }
    label {
      font-weight: bold;
    }
    .edit-img-thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 4px;
      margin: 2px;
    }
    .img-delete-btn {
      position: absolute;
      top: 2px;
      right: 2px;
      background: rgba(255,255,255,0.8);
      border: none;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 14px;
      cursor: pointer;
    }
    .img-thumb-wrapper {
      display: inline-block;
      position: relative;
    }
  </style>
</head>
<body>
  <div class="container mt-4">
    <div class="text-center mb-4">
      <h3>Edit Product</h3>
      <p class="text-muted">Click update after changing any information</p>
    </div>

    <?php
      if (!empty($errors)) {
        foreach ($errors as $error) {
          echo "<div class='alert alert-warning'>$error</div>";
        }
      }
    ?>

    <div class="container d-flex justify-content-center">
      <form action="" method="post" style="width: 70vw;min-width: 300px;" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="row">
          <div class="col">
            <label class="form-label" for="name">Name: </label>
            <input type="text" class="form-control" name="name" placeholder="Phone" autocomplete="off" value="<?php echo htmlspecialchars($row['name'])?>">
          </div>
          <div class="col">
            <label class="form-label" for="price">Price: </label>
            <input type="number" class="form-control" name="price" placeholder="1000" autocomplete="off" value="<?php echo htmlspecialchars($row['price'])?>">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col">
            <label class="form-label" for="images">Images: </label>
            <input type="file" class="form-control" name="images[]" multiple>
            <small class="text-muted">You can select and add more images. Existing images are shown below.</small>
            <div class="mt-2">
              <?php foreach ($images as $img): ?>
                <div class="img-thumb-wrapper">
                  <img src="uploads/<?php echo htmlspecialchars($img['filename']); ?>" class="edit-img-thumb">
                  <!-- Optional: Add delete button for each image -->
                  <!--
                  <form method="post" action="delete_image.php" style="display:inline;">
                    <input type="hidden" name="img_id" value="<?php echo $img['id']; ?>">
                    <button type="submit" class="img-delete-btn" title="Delete">&times;</button>
                  </form>
                  -->
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col">
            <label class="form-label" for="cat">Category: </label>
             <select class="form-control" name="cat">
              <?php
                $select_cat = "SELECT * FROM category";
                $result_cat = $conn->query($select_cat);
                while($cat = $result_cat->fetch_assoc()){
              ?>
              <option value="<?php echo $cat['id'] ?>" <?php if($cat['id'] == $row['cat']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat['name']); ?>
              </option>
              <?php } ?>
             </select>
          </div>
          <div class="col">
            <label class="form-label" for="brand">Brand: </label>
             <select class="form-control" name="brand">
              <?php
                $select_brand = "SELECT * FROM brand";
                $result_brand = $conn->query($select_brand);
                while($brand = $result_brand->fetch_assoc()){
              ?>
              <option value="<?php echo $brand['id'] ?>" <?php if($brand['id'] == $row['brand']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($brand['name']); ?>
              </option>
              <?php } ?>
             </select>
          </div>
          <div class="col">
            <label class="form-label" for="count">Count: </label>
            <input type="number" class="form-control" name="count" placeholder="5" autocomplete="off" value="<?php echo htmlspecialchars($row['count'])?>">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col">
            <label class="form-label" for="description" >Description: </label>
            <input type="text" class="form-control des" name="des" placeholder="description..." value="<?php echo htmlspecialchars($row['des'])?>">
          </div>
        </div>
        <div class="d-flex justify-content-center">
          <input type="submit" value="Update" class="btn btn-success mt-4" name="update" style="width: 25%;">
          <a href="tables.php" class="btn btn-danger mt-4" style="width: 25%;margin-left: 20px;">Cancel</a>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
