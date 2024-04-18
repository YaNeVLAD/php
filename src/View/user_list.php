<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>User Info</title>
    <style>
        a {
            display: inline-block;
            padding: 15px;
            border: 2px solid black;
            background-color: green;
            text-decoration: none;
            color: white;
        }

        .nav {
            display: inline-block;
            padding: 7px;
            border: 1px solid black;
            background-color: grey;
            text-decoration: none;
            color: white;
        }
    </style>
</head>

<body>
    <a href="index" class="nav">Return To Hub</a>
    <h1>Users List</h1>
    <?php
    foreach ($data as $info) { ?>
        <a href="/php/show_user?userId=<?= $info['user_id'] ?>">View <?= $info['first_name'] ?></a>
        <a href="/php/delete_user?userId=<?= $info['user_id'] ?>" style="background-color: red;">Delete
            <?= $info['first_name'] ?></a>
        <br>
    <?php }

    ?>
</body>