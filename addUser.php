<?php
  if(isset($_POST['add'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $pr = $_POST['pr'];

    $hashedPassword = password_hash($password,PASSWORD_DEFAULT);
    $errors = array();
    if(empty($username) OR empty($email) OR empty($password) OR empty($gender) OR empty($pr)){
      array_push($errors,'There is a messing field');
    }else{
      if(count($errors)>0){
        foreach($errors as $err){
          echo "<div class='alert alert-danger'>$err</div>";
        }
      }
      require_once 'database.php';
      $stmt = mysqli_stmt_init($conn);
      $query = 'INSERT INTO users_entered(username,email,password,gender,pr) VALUES(?,?,?,?,?)';
      if(mysqli_stmt_prepare($stmt,$query)){
        mysqli_stmt_bind_param($stmt,'sssii',$username,$email,$hashedPassword,$gender,$pr);
        mysqli_stmt_execute($stmt);
        header('location: users.php?msg=User added successfully');
        exit();
      }else {
        echo "Something went wrong";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add User</title>
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
      <h3>Add User</h3>
      <p class="text-muted">complete the form below to add user</p>
    </div>
    <div class="container d-flex justify-content-center">
      <form action="" method="post" style="width: 70vw;min-width: 300px;">
        <div class="row">
          <div class="col">
            <label class="form-label" for="username">Username: </label>
            <input type="text" class="form-control" name="username" placeholder="Albert" autocomplete="off">
          </div>
          <div class="col">
            <label class="form-label" for="email">Email: </label>
            <input type="email" class="form-control" name="email" placeholder="mo@example.com" autocomplete="off">
          </div>
        </div>
         <div class="row mt-2">
          <div class="col">
            <label class="form-label" for="password">Password: </label>
            <input type="password" class="form-control" name="password">
          </div>
        </div>
        <div class="col">
            <label class="form-label" for="pr">PR: </label>
             <select class="form-control" name="pr">
              <?php
                require_once 'database.php';
                $select_pr = "SELECT * FROM pr";
                $result_pr = $conn->query($select_pr);
                while($pr = $result_pr->fetch_assoc()){
              ?>
              <option value="<?php echo $pr['id'] ?>"><?php echo $pr['name'] ?></option>
              <?php } ?>
             </select>
          </div>
        <div class="row mt-4">
          <div class="col">
            <label class="form-label" for="cat">Gender: </label>
            <?php
              require_once 'database.php';
              $query = 'SELECT * FROM gender';
              $result = $conn->query($query);
              while($row=mysqli_fetch_assoc($result)){
                $genderId = $row['id'];
                $genderName = $row['name'];
            ?>
            <input type="radio" value="<?php echo $genderId?>" name="gender" class="form-check-input" >
            <label for="male" class="form-check-label"><?php echo $genderName?></label>
            <?php
              }
            ?>
          </div>
        </div>
        <div class="d-flex justify-content-center">
          <input type="submit" value="Add" class="btn btn-success mt-4" name="add" style="width: 25%;">
          <a href="users.php" class="btn btn-danger mt-4" style="width: 25%;margin-left: 20px;">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
