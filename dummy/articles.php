<?php include('helpers/session.php') ?>
<?php include('database.php') ?>
<html>
    <head>
        <title>Luora | Articles</title>
        <?php include('components/include.php') ?>
    </head>
    <body>
        <?php include('components/header.php'); ?>
        <div class="container">
            <div class="page-header gap">
                <h1>See all Articles</h1>
                <br>
                <?php
                    $page = 1;
                    $limit_per_page = 5;

                    if(isset($_REQUEST['page'])){
                        $page = $_REQUEST['page'];
                    }

                    $article_count_query = "SELECT COUNT(id) as count FROM articles";
                    $rs = $connection->query($article_count_query);
                    $article_count = $rs->fetch_assoc()['count'];

                    $page_count = ceil(1.0 * $article_count / $limit_per_page);    
                    $offset = ($page - 1) * $limit_per_page;
                    if($offset < 0) $offset = 0;
                ?>
                
                <?php
                    $query = 
                    "SELECT articles.id, title, content, tags, name 
                    FROM articles 
                    JOIN users on articles.user_id = users.id 
                    ORDER BY id DESC LIMIT $limit_per_page
                    OFFSET $offset";
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
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php
                        if($page == 1){
                            $class = 'disabled';
                            $path = "#";
                        }
                        else{
                            $class = '';
                            $path = 'articles.php?page=' . ($page - 1);
                        }
                        ?>
                        <li class="<?= $class ?>">
                            <a href="<?= $path ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php
                        for($i = 1; $i <= $page_count; $i++){
                            $class = $page == ($i . '') ? 'active' : '';
                        ?>
                        <li class="<?= $class ?>"><a href="articles.php?page=<?= $i ?>"><?= $i ?></a></li>
                        <?php
                        }
                        
                        if($page == $page_count){
                            $class = 'disabled';
                            $path = "#";
                        }
                        else{
                            $class = '';
                            $path = 'articles.php?page=' . ($page + 1);
                        }
                        ?>
                        <li class="<?= $class ?>">
                            <a href="<?= $path ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php include('components/footer.php') ?>
    </body>
</html>