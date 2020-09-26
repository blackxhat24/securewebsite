<?php include('helpers/session.php') ?>
<html>
    <head>
        <title> Luora | Sign Up </title>
        <?php include('components/include.php') ?>
    </head>
    <body>
        <?php include('components/header.php'); ?>
        <div class="container">
            <div class="col-lg-12 gap">
                <h1>Sign Up</h1>
            </div>
            <div class="col-lg-12">
            <form action="auth/auth.php" method="POST">
                <input type="hidden" name="action" value="signup">
                <!-- QUESTION 3 SECTION 1B OF 2: Prevent CSRF on authentication -->
                <!-- SECTION STARTS HERE-->
                
                
                <!-- SECTION ENDS HERE -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" placeholder="Username" name="username">
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Name" name="name">
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" placeholder="Email" name="email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" 
                    name="password" placeholder="Password">
                </div>
                <div class="form-group">
                    <label for="conf-password">Confirm Password</label>
                    <input type="password" class="form-control" id="conf-password" 
                    name="conf-password" 
                    placeholder="Confirm Password">
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
                ?>
                <button type="submit" class="btn btn-danger">Submit</button>
            </form>
            </div>
        </div>
    </body>
    <?php include('components/footer.php') ?>
</html>