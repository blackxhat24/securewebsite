<?php include('../helpers/session.php') ?>
<?php include('../database.php') ?>
<?php include('../helpers/message.php') ?>
<?php include('../helpers/redirect.php') ?>
<?php include('../helpers/debug.php') ?>

<?php
    //////
    /// QUESTION 3 SECTION 2A OF 2: Prevent CSRF on comment submission
    /// SECTION STARTS HERE
    //////
    if(!isset($_REQUEST['csrf_token']) || $_REQUEST['csrf_token'] != get_token()){
        $article_id    = $_REQUEST['article_id'];
        add_error('Invalid CSRF token');
        redirect("../article.php?id=$article_id#submit-comment");
    }
    //////
    /// SECTION ENDS HERE
    //////

    $comment         = $_REQUEST['comment'];
    $article_id    = $_REQUEST['article_id'];
    $user_id        = $_SESSION['user_id'];

    if(!$comment || strlen($comment) == 0 || !$article_id || !$user_id){
        add_error('comment may not be blank');
        redirect("../article.php?id=$article_id#submit-comment");
    }

    //////
    /// QUESTION 2 - Cross-Site Scripting (XSS)
    /// Escape user's input before inputting it to the database
    ///
    /// SECTION STARTS HERE
    //////
    $comment = htmlentities($comment);
    $insert_comment_query = 
    "INSERT INTO comments (article_id, user_id, comment) 
    VALUES (?, ?, ?)";
    $insert_comment = $connection->prepare($insert_comment_query);
    $insert_comment->bind_param("iis", $article_id, $user_id, $comment);
    $insert_comment->execute();
    //////
    /// SECTION ENDS HERE
    //////
    $last_id = $connection->insert_id;
    redirect("../article.php?id=$article_id#comment-$last_id");