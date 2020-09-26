<?php 
    include('helpers/session.php');
    include('helpers/redirect.php');
    if(isset($_SESSION['username']))
        redirect('index.php');
?>
<html>
    <head>
        <title>Log In | Luora</title>
        <?php include('components/include.php') ?>
    </head>
    <body>
        <?php include('components/header.php'); ?>
        <div class="container">
            <br>
            <div class="col-lg-12 gap">
                <h1>Log In</h1>
            </div>
            <br>
            <div class="col-lg-12">
            <form action="auth/auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <!-- QUESTION 3 SECTION 1B OF 2: Prevent CSRF on authentication -->
                <!-- SECTION STARTS HERE-->
                
                <!-- SECTION ENDS HERE -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" placeholder="Username" name="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                </div>
                <?php include ('helpers/message.php');
                    if(has_error()){ 
                        foreach(get_errors() as $error){
                ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Error!</strong> 
                            <?= $error ?>
                        </div>
                <?php 
                        }
                    } 
                    if(has_success()){
                        foreach(get_successes() as $success){
                ?>
                    <div class="alert alert-success" role="alert">
                            <strong>Success!</strong> 
                            <?= $success ?>
                        </div>
                <?php
                        }
                    }
                ?>
                <button type="submit" class="btn btn-danger">Log In</button>
            </form>
            </div>
        </div>
        <?php include('components/footer.php') ?>
    </body>
</html>