<?php
  session_start();
  $status = 'tables';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>SB Admin 2 - Tables</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
      .carousel-control-prev,
      .carousel-control-next {
          border: none; /* Remove default border */
          outline: none; /* Remove outline */
      }
      .carousel-control-prev-icon,
      .carousel-control-next-icon {
          background-color: rgba(0, 0, 0, 0.5); /* Dark background for icons */
          border-radius: 50%; /* Make icons circular */
          padding: 5px; /* Add padding for better touch area */
      }
      .carousel-control-prev:hover,
      .carousel-control-next:hover {
          background-color: rgba(255, 255, 255, 0.9); /* Lighter background on hover */
      }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
      <!-- Sidebar -->
        <?php
          include('sidebar.php');
        ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                 <?php include('header.php'); ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                  <?php
                    if(isset($_GET['msg'])){
                      $msg = $_GET['msg'];
                      if($msg == 'Product removed successfully'){
                        echo "<div class='alert alert-danger'>$msg</div>";
                      }else{
                        echo "<div class='alert alert-success'>$msg</div>";
                      }
                    }
                  ?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Tables</h1>
                    <p class="mb-4">Our products are one if not the best in the market.
                        For more information about DataTables, please visit the <a target="_blank"
                            href="https://datatables.net">official DataTables documentation</a>.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Products</h6>
                        </div>
                        <div class="card-body">
                          <a href="addProduct.php" class="btn btn-primary mb-4">Add Product</a>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Image</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Count</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Image</th>
                                            <th>Category</th>
                                            <th>Brand</th>
                                            <th>Count</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                      <?php
                                        require_once 'database.php';
                                        $query = 'SELECT * FROM products';
                                        $r = mysqli_query($conn,$query);
                                        while($row = mysqli_fetch_assoc($r)){
                                          ?>
                                            <tr>
                                              <td><?php echo $row['name']?></td>
                                              <td><?php echo $row['price']?></td>
                                              <td>
                                                <?php
                                                $product_id = $row['id'];
                                                $img_query = "SELECT filename FROM product_images WHERE product_id = $product_id";
                                                $img_result = mysqli_query($conn, $img_query);

                                                if ($img_result) {
                                                    $img_count = mysqli_num_rows($img_result);
                                                    $carouselId = "carouselProduct" . $product_id;

                                                    if ($img_count > 0) {
                                                        ?>
                                                        <div id="<?php echo $carouselId; ?>" class="carousel slide" data-bs-ride="carousel" style="width:70px;">
                                                            <div class="carousel-inner">
                                                                <?php
                                                                $active = 'active';
                                                                mysqli_data_seek($img_result, 0);
                                                                while ($img = mysqli_fetch_assoc($img_result)) {
                                                                    $imgPath = 'uploads/' . $img['filename'];
                                                                    echo "<div class='carousel-item $active'>";
                                                                    echo "<img src='$imgPath' class='d-block w-100' style='height:70px; object-fit:cover; border-radius:4px;'>";
                                                                    echo "</div>";
                                                                    $active = '';
                                                                }
                                                                ?>
                                                            </div>
                                                            <?php if ($img_count > 1) { ?>
                                                                <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="prev">
                                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden"></span>
                                                                </button>
                                                                <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carouselId; ?>" data-bs-slide="next">
                                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                    <span class="visually-hidden"></span>
                                                                </button>
                                                            <?php } ?>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        echo "<span>No Image</span>";
                                                    }
                                                } else {
                                                    echo "<span>Error retrieving images</span>";
                                                }
                                                ?>
                                            </td>

                                              <td><?php
                                                $catId = $row['cat'];
                                                $query = "SELECT * FROM category WHERE id = $catId";
                                                $result = mysqli_query($conn,$query);
                                                $cat = $result->fetch_assoc();
                                                echo $cat['name'];
                                              ?></td>
                                              <td><?php
                                                $brandId= $row['brand'];
                                                $query = "SELECT * FROM brand WHERE id = $brandId";
                                                $result = mysqli_query($conn,$query);
                                                $brand = $result->fetch_assoc();
                                                echo $brand['name'];
                                              ?></td>
                                              <td><?php echo $row['count']?></td>
                                              <td><?php echo $row['des']?></td>
                                              <th>
                                                <a href="edit.php?id=<?php echo $row['id']?>" class="link-dark"><i class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
                                                <a href="delete.php?id=<?php echo $row['id']?>" class="link-dark"><i class="fa-solid fa-trash fs-5"></i></a>
                                              </th>
                                          </tr>
                                          <?php
                                        }
                                      ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
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

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>
