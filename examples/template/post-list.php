<?php
//Post::setSorting(SORT_ASC);
echo "Posts: <br>";
$posts = Post::listPosts();
foreach ($posts as $post) {
    echo "<a href='/post/".$post['name']."'><h3>".$post['name']."</h3></a> created at " .date("d.m.Y H:i:s", $post['created_at'])."<br>";
}