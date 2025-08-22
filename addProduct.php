<?php
  if(isset($_POST['add'])){
    require_once 'database.php';

    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat = $_POST['cat'];
    $brand = $_POST['brand'];
    $count = $_POST['count'];
    $des = $_POST['des'];

    $errors = array();

    // Multiple images
    $files = $_FILES['images'];
    $allowedTypes = ['jpg','jpeg','png','gif'];

    // Check required fields
    if(empty($name) || empty($price) || empty($cat) || empty($brand) || empty($count) || empty($des)){
      array_push($errors,'There is a missing field');
    } elseif (empty($files['name'][0])) {
      array_push($errors, 'At least one image is required');
    } else {
      // Validate all images
      foreach($files['name'] as $key => $fileName){
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if(!in_array($ext, $allowedTypes)){
          array_push($errors, "Invalid file type for $fileName");
        }
      }
    }

    if(empty($errors)){
      // Insert product first
      $stmt = $conn->prepare("INSERT INTO `products` (`name`, `price`, `cat`, `brand`, `count`, `des`) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sdiiis", $name, $price, $cat, $brand, $count, $des);
      if($stmt->execute()){
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Handle images
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }
        $allUploaded = true;
        foreach($files['name'] as $key => $fileName){
          $tmpName = $files['tmp_name'][$key];
          $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
          $newFileName = uniqid('img_').'.'.$ext;
          $targetPath = $uploadDir . $newFileName;
          if(move_uploaded_file($tmpName, $targetPath)){
            $stmtImg = $conn->prepare("INSERT INTO product_images (product_id, filename) VALUES (?, ?)");
            $stmtImg->bind_param("is", $product_id, $newFileName);
            $stmtImg->execute();
            $stmtImg->close();
          } else {
            $allUploaded = false;
            array_push($errors, "Failed to upload $fileName");
          }
        }
        if($allUploaded){
          header('Location: tables.php?msg=New Product added successfully');
          exit();
        }
      } else {
        array_push($errors, "Failed: " . $conn->error);
      }
    }

    if (!empty($errors)) {
      foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .des {
      height: 120px;
    }
    label {
      font-weight: bold;
    }
  </style>
</head>
<body>


  <div class="container mt-4">
    <div class="text-center mb-4">
      <h3>Add New Product</h3>
      <p class="text-muted">complete the form below to add a new product</p>
    </div>
    <div class="container d-flex justify-content-center">
      <form action="" method="post" style="width: 70vw;min-width: 300px;" enctype="multipart/form-data">
        <div class="row">
          <div class="col">
            <label class="form-label" for="name">Name: </label>
            <input type="text" class="form-control" name="name" placeholder="Phone" autocomplete="off">
          </div>
          <div class="col">
            <label class="form-label" for="price">Price: </label>
            <input type="number" class="form-control" name="price" placeholder="1000" autocomplete="off">
          </div>
        </div>
         <div class="row mt-2">
          <div class="col">
            <label class="form-label" for="images">Images: </label>
            <input type="file" class="form-control" name="images[]" multiple>
            <small class="text-muted">You can select multiple images</small>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col">
            <label class="form-label" for="cat">Category: </label>
             <select class="form-control" name="cat">
              <?php
                require_once 'database.php';
                $select_cat = "SELECT * FROM category";
                $result_cat = $conn->query($select_cat);
                while($cat = $result_cat->fetch_assoc()){
              ?>
              <option value="<?php echo $cat['id'] ?>"><?php echo $cat['name'] ?></option>
              <?php } ?>
             </select>
          </div>
          <div class="col">
            <label class="form-label" for="brand">Brand: </label>
             <select class="form-control" name="brand">
              <?php
                require_once 'database.php';
                $select_brand = "SELECT * FROM brand";
                $result_brand = $conn->query($select_brand);
                while($brand = $result_brand->fetch_assoc()){
              ?>
              <option value="<?php echo $brand['id'] ?>"><?php echo $brand['name'] ?></option>
              <?php } ?>
             </select>
          </div>
          <div class="col">
            <label class="form-label" for="count">Count: </label>
            <input type="number" class="form-control" name="count" placeholder="5" autocomplete="off">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col">
            <label class="form-label" for="description" >Description: </label>
            <input type="text" class="form-control des" name="des" placeholder="description...">
          </div>
        </div>
        <div class="d-flex justify-content-center">
          <input type="submit" value="Add" class="btn btn-success mt-4" name="add" style="width: 25%;">
          <a href="tables.php" class="btn btn-danger mt-4" style="width: 25%;margin-left: 20px;">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
