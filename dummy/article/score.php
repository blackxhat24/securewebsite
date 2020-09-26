<?php include('../helpers/session.php') ?>
<?php include('../database.php') ?>
<?php
    if(!isset($_REQUEST['article_id'])){
        http_response_code(401);
        echo json_encode(['error' => 'No parameter may be empty!']);
        die();
    }

    //////
    /// QUESTION 1 -  Access Control
    /// This is an API end point designed for handling vote
    /// through AJAX requests.
    /// Add a layer of validation to:
    ///  1. Check whether a user has logged in
    ///  2. Add voting algorithm:
    ///     If user has not voted, insert a new vote
    ///     If user has voted, remove user's vote
    //////

    //////
    /// QUESTION 1 SECTION 1 OF 2: Check whether user has logged in
    /// SECTION STARTS HERE
    //////


    //////
    /// SECTION ENDS HERE
    //////

    $article_id     = $_REQUEST['article_id'];
    $user_id        = $_SESSION['user_id'];
    
    //////
    /// QUESTION 1 SECTION 2 OF 2: Add voting algorithm
    /// SECTION STARTS HERE
    //////
    $user_has_voted = false;

    if(!$user_has_voted){
        $query = 
            "INSERT INTO votes (article_id, user_id)
            VALUES ($article_id, $user_id)";
    }
    else{
        
    }
    
    $connection->query($query);
    //////
    /// SECTION ENDS HERE
    //////    

    $score_query = 
        "SELECT COUNT(id) as score
        FROM votes
        WHERE article_id=$article_id";
    $score_result = $connection->query($score_query);
    $score = $score_result->fetch_assoc()['score'];
    if($score == null) $score = 0;

    http_response_code(200);
    echo json_encode(['success' => 'Upvote/Downvote success', 'new_score' => $score]);
    die();