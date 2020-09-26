<?php include('../helpers/session.php') ?>
<?php include('../helpers/redirect.php') ?>
<?php include('../helpers/debug.php') ?>
<?php include('../helpers/message.php') ?>
<?php include('../database.php') ?>

<?php
    $request_method = strtoupper($_SERVER['REQUEST_METHOD']);

    //  Handles Login and Sign Up (form POST)
    if($request_method == 'POST'){
        $action = strtolower($_REQUEST['action']);

        //////
        /// QUESTION 3 - Cross-Site Request Forgery (CSRF)
        /// The following section contains the authentication method.
        /// Validate the form so that no CSRF attack can be done.
        /// Use CSRF token technique to accomplish that.
        ///
        /// The A part is the backend token validation
        /// The B part is the front end form token placement
        ///
        /// QUESTION 3 SECTION 1A OF 2: Prevent CSRF on authentication
        /// SECTION STARTS HERE
        //////
       

        //////
        /// SECTION ENDS HERE
        //////

        //  Login Method
        if($action == 'login'){
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];

            //////
            /// QUESTION 4 - SQL Injection
            /// The following sections contain unsafe SQL statements.
            /// You are free to modify the code inside the section to
            /// make is safe while keeping the result exactly the same 
            /// as the initial, given code.
            ///
            /// QUESTION 4 SECTION 1 OF 3: Prevent SQL Injection on login form
            /// SECTION STARTS HERE
            //////
            $query = "SELECT * FROM users WHERE username='$username' AND password=SHA1('$password') LIMIT 1";
            $result = $connection->query($query);
            //////
            /// SECTION ENDS HERE
            //////

            if($user_data = $result->fetch_assoc()){
                $_SESSION['user_id']    = $user_data['id'];
                $_SESSION['username']   = $user_data['username'];
                $_SESSION['name']       = $user_data['name'];
                redirect('../index.php');
            }
            else{
                add_error('Username or password is invalid!');
                redirect('../login.php');
            }
        }

        //  Signup Method
        else if($action == 'signup'){
            $username = $_REQUEST['username'];
            $name     = $_REQUEST['name'];
            $email    = $_REQUEST['email'];
            $password = $_REQUEST['password'];
            $confpass = $_REQUEST['conf-password'];
            
            if(!$username || !$name || !$email || !$password || !$confpass){
                add_error('No field may be blank!');
                redirect('../signup.php');
            }

            if($password != $confpass){
                add_error('Password and confirmation does not match!');
                redirect('../signup.php');
            }

            //////
            /// QUESTION 4 SECTION 2 OF 3: Prevent SQL Injection on sign up form
            /// SECTION STARTS HERE
            //////            
            $check_user_query = 
                "SELECT 1 FROM users WHERE username='$username'";
            $result = $connection->query($check_user_query);

            if($user_data = $result->fetch_assoc()){
                add_error("Username $username has already taken. Please enter another username.");
                redirect('../signup.php');
            }

            $insert_user_query = 
                "INSERT INTO users (username, name, password, email) VALUES ('$username', '$name', SHA1('$password'), '$email')";
            $connection->query($insert_user_query);
            //////
            /// SECTION ENDS HERE
            //////

            add_success("User $username successfully created!");
            redirect('../login.php');
        }
        else{
            redirect('../index.php');
        }
    }

    //  Handles Log Out (GET)
    else if($request_method == 'GET'){
        $action = strtolower($_REQUEST['action']);
        if($action == 'logout'){
            session_destroy();
            redirect('../index.php');
        }
        else{
            redirect('../index.php');
        }
    }
