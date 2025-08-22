

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SB Admin 2 - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">
      <?php
        if(isset($_POST['login'])){
          $email = $_POST['email'];
          $password = $_POST['password'];
          if(empty($email) OR empty($password)){
            echo "<div class='alert alert-danger'>Please fill all fields</div>";
          }else{
            require_once 'database.php';
            $query = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn,$query);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if($user){
              if(password_verify($password,$user['password'])){
                session_start();
                $_SESSION["user_name"] = $user["first_name"];
                $_SESSION["user"] = "yes";
                header('location: index.php');
                die();
              }else{
                echo "<div class='alert alert-danger'>Wrong Password</div>";
              }
            }else{
              echo "<div class='alert alert-danger'>Email not registered</div>";
            }
          }
        }
      ?>

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" action="login.php" method="post" >
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." name="email">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Password"
                                                 name="password">
                                        </div>
                                           <input type="submit" value="Login" class="btn btn-primary btn-user btn-block" name="login">
                                           <div><p>Don't have an account? <a href="register.php">Sign up</a></p></div>
                                        <hr>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
