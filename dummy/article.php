<?php include('helpers/session.php') ?>
<?php include('helpers/redirect.php') ?>
<?php include('database.php') ?>
<?php
    if(!isset($_REQUEST['id'])){
        redirect('index.php');
    }
    //////
    /// QUESTION 4 SECTION 3 OF 3: Preventing SQL Injection on article details
    /// SECTION STARTS HERE
    //////
    $id = $_REQUEST['id'];
    $retrieve_query = 
        "SELECT articles.id, title, content, tags, name 
        FROM articles 
        JOIN users ON articles.user_id = users.id
        WHERE articles.id=$id";
    $result = $connection->query($retrieve_query);
    if($connection->error){
        echo "$connection->error <br/>";
        die();
    }
    //////
    /// SECTION ENDS HERE
    //////

    if($article_data = $result->fetch_assoc()){
        //  Obtain article score
        $article_id = $article_data['id'];
        $score_query = 
            "SELECT COUNT(id) as score
            FROM votes
            WHERE article_id=$article_id";
        $score_result = $connection->query($score_query);
        $score = $score_result->fetch_assoc()['score'];
        if($score == null) $score = 0;

        $article = [
            'id'        => $article_id,
            'name'      => $article_data['name'],
            'title'     => $article_data['title'],
            'content'   => $article_data['content'],
            'tags'      => explode(';', $article_data['tags']),
            'score'     => $score
        ];

        //  Obtain comments
        $comment_query = 
            "SELECT comments.id, users.name, comment
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE article_id=$article_id";

        $comment_result = $connection->query($comment_query);
        $comments = [];
        while($comment = $comment_result->fetch_assoc()){
            $comments[] = [
                'id'        => $comment['id'],
                'name'      => $comment['name'],
                'comment'    => $comment['comment']
            ];
        }
    }
    else{
        redirect('404.php', 404);
    }
?>
<html>
    <head>
        <title> Luora | comments </title>
        <?php include('components/include.php') ?>
    </head>
    <body>
        <?php include('components/header.php'); ?>
        <div class="container">
            <div class="page-header gap">
                <h1><?= $article['title'] ?></h1>
            </div>
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2">
                    <?php
                        $extra_class = '';
                        $score = $article['score'];
                        if($score < 0) $extra_class = 'negative';
                        else if($score > 0) $extra_class = 'positive';
                    ?>
                    <div class="score <?= $extra_class ?>" id="score">
                        <?= $article['score'] ?>
                    </div>
                    <div class="score-edit">
                        <button id="upvote" class="btn btn-warning"> Vote </button>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="inner_panel">
                                <div class="article"><span></span></div>
                                <div class="article_title">Written By <span class="namefield"><?= $article['name'] ?></span></div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?= nl2br($article['content']) ?>
                        </div>
                        <div class="panel-footer">
                        <?php
                            foreach($article['tags'] as $tag){
                        ?>
                                <span class="label label-default"><?= $tag ?></span>
                        <?php
                            }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
            

            <div class="row comment-block">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <?php
                        if(count($comments) == 0){
                    ?>
                        <div class="panel panel-warning">
                            <div class="panel-body">
                                No comment available
                            </div>
                        </div>
                    <?php
                        }
                        foreach($comments as $comment){
                    ?>
                       <div class="panel panel-warning">
                            <div class="panel-body">
                                <?= nl2br($comment['comment']) ?>
                            </div>
                            <div class="panel-footer">
                                commented by <?= $comment['name'] ?>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                </div>
            </div>

             <?php
                if(isset($_SESSION['username'])){
            ?>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <span class="namefield"><?= $_SESSION['name'] ?></span> , Do you want to comment this article ?
                        </div>
                        <div class="panel-body">
                            <form action="article/comment.php" id="submit-comment" method="POST">
                                <!-- article 3 SECTION 1B OF 2: Prevent CSRF on comment submission -->
                                <!-- SECTION STARTS HERE-->
                                
                                <!-- SECTION ENDS HERE -->
                                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                <textarea name="comment" id="comment"  class="form-control" rows="3" placeholder="Write your comment"></textarea>
                                <input type="submit" value="Submit comment" class="btn btn-primary">
                            </form>
                        </div>
                        <?php
                        include('helpers/message.php');
                        if(has_error()){
                        ?>
                            <div class="panel-footer">
                            <?php
                                foreach(get_errors() as $error){
                            ?>
                                    <div class="alert alert-danger">
                                        <strong>Error!</strong> 
                                        <?= $error ?>
                                    </div>
                            <?php
                                }
                            ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>

        </div>

        

        <?php include('components/footer.php') ?>
        <script>
            $('#upvote').click(function(e){
                $.ajax({
                    'url' : "article/score.php?article_id=<?= $article['id'] ?>",
                    'method' : 'GET',
                })
                .done(function(data){
                    data = JSON.parse(data);
                    $("#score").html(data.new_score);
                    if(data.new_score > 0){
                        $("#score").addClass('positive');
                    }
                    else if(data.new_score < 0){
                        $("#score").addClass('negative');
                    }
                })
                .fail(function(data){
                    console.log('fail', data);
                });
            });
        </script>
    </body>
</html>