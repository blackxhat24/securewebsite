<?php include('helpers/session.php') ?>
<?php include('database.php') ?>
<html>
    <head>
        <title> Home - Luora </title>
        <?php include('components/include.php') ?>
    </head>
    <body>
        <?php include('components/header.php'); ?>
        <div class="container">
            <div class="page-header gap">
                <h1>Latest Articles</h1>
                <br><br>
                <?php
                    $query = 
                    "SELECT articles.id, title, content, tags, name 
                    FROM articles 
                    JOIN users on articles.user_id = users.id 
                    ORDER BY id DESC LIMIT 5";
                    $rs = $connection->query($query);

                    while($data = $rs->fetch_assoc()){
                ?>
                    <div class="panel panel-default">

                        <div class="panel-body" style="border-bottom: 1px solid #d3d3d3">
                        <div class="inner_panel">
                            <div class="article"><span></span></div>
                            <div class="article_title">Article from <span class="namefield"><?= $data['name'] ?></span></div>
                        </div>
                        </div>
                        <div class="panel-body">
                            <span class="articlefield"><?= $data['title'] ?></span>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                <br>
                                <?php
                                    $tags = explode(';', $data['tags']);
                                    foreach($tags as $tag){
                                ?>
                                        <span class="label label-default">
                                            <?= $tag ?>
                                        </span>&nbsp;
                                <?php
                                    }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <?php 
                                        if(isset($_SESSION['username']) && $_SESSION['username'] != null){ 
                                    ?>
                                        <a class="ansbtn" href="article.php?id=<?=$data['id']?>"><span> Comment </span></a>
                                    <?php
                                        } else {
                                    ?>
                                        <a class="ansbtn viewbtn" href="article.php?id=<?=$data['id']?>"><span> View </span></a>
                                    <?php 
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    }
                ?>
            </div>
        </div>
        <?php include('components/footer.php') ?>
    </body>
</html>