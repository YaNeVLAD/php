<?php
/**
 * @var App\Model\Post $post
 */
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= $post->getTitle() ?></title>
    <link rel="stylesheet" href="/css/show_post.css">
</head>
<body>
<div class="post-content">
    <h1><?= htmlentities($post->getTitle()) ?></h1>
    <?php if ($post->getSubtitle()): ?>
        <h2><?= htmlentities($post->getSubtitle()) ?></h2>
    <?php endif; ?>
    <p><em><?= $post->getPostedAt()->format(DateTimeInterface::RSS) ?></em></p>
    <p><?= htmlentities($post->getContent()) ?></p>
</div>
</body>
</html>
