<nav class="navbar navbar-default" id="headbar">
    <div class="container">
        <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" id="title" href="index.php">Luora</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li class="headitem"><a  href="index.php"> Home </a></li>
            <li class="headitem"><a  href="articles.php"> Articles </a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <?php 
                if(isset($_SESSION['username']) && $_SESSION['username'] != null){ 
            ?>
                <li><a class="namefield"><?= $_SESSION['name'] ?></a></li>
                <li><a href="auth/auth.php?action=logout">Log Out</a></li>
            <?php 
                } 
                else {
            ?>
                <li><a href="login.php">Log In</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            <?php
                }
            ?>
        </ul>
        </div>
    </div>
</nav>