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
    if(!isset($_SESSION['user_id'])){
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized access. User must be logged in to vote.']);
        die();
    }
    //////
    /// SECTION ENDS HERE
    //////

    $article_id    = $_REQUEST['article_id'];
    $user_id        = $_SESSION['user_id'];
    
    //////
    /// QUESTION 1 SECTION 2 OF 2: Add voting algorithm
    /// SECTION STARTS HERE
    //////
    $query = 
        "SELECT 1 as voted 
        FROM votes 
        WHERE article_id = ? AND user_id = ?
        LIMIT 1";
    $check_query = $connection->prepare($query);
    $check_query->bind_param("ii", $article_id, $user_id);
    $check_query->execute();
    $result = $check_query->get_result();
    $result = $result->fetch_assoc();
    if($result == null){
        $user_has_voted = false;
    }
    else{
        $user_has_voted = true;
    }

    if(!$user_has_voted){
        $query = 
            "INSERT INTO votes (article_id, user_id)
            VALUES (?, ?)";
    }
    else{
        $query = 
            "DELETE FROM votes
            WHERE article_id=? AND user_id=?";
    }
    $voting_query = $connection->prepare($query);
    $voting_query->bind_param("ii", $article_id, $user_id);
    $voting_query->execute();
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