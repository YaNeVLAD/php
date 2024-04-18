<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>User Info</title>
    <style>
        a {
            display: inline-block;
            padding: 15px;
            background-color: red;
            color: white;
            text-decoration: none;
            border: 1px solid black;
            text-align: center;
        }

        .nav {
            display: inline-block;
            padding: 7px;
            border: 1px solid black;
            background-color: grey;
            text-decoration: none;
            color: white;
        }

        .imageInput {
            display: flex;
            flex-direction: column;
        }

        .update {
            display: inline-block;
            padding: 15px;
            background-color: grey;
            color: white;
            text-decoration: none;
            border: 1px solid black;
        }

        .image {
            height: 150px;
            width: 150px;
            border-radius: 100px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <a href="/php/index" class="nav">Return To Hub</a>
    <a href="/php/view_all_users" class="nav">Return To Users List</a>
    <h1> User Info </h1>
    <span>User First Name:
        <?= htmlentities($user->getFirstName(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>
    </span>
    <br>
    <span>User Last Name:
        <?= htmlentities($user->getLastName(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>
    </span>
    <br>
    <span>User Middle Name:
        <?= ($user->getMiddleName()) ? htmlentities($user->getMiddleName(), ENT_QUOTES | ENT_IGNORE, "UTF-8") : '' ?>
    </span>
    <br>
    <span>User Gender:
        <?= htmlentities($user->getGender(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>
    </span>
    <br>
    <span>User Birth Date:
        <?php
        $timestamp = strtotime($user->getBirthDate());
        echo date("d.m.Y", $timestamp); ?>
    </span>
    <br>
    <span>User Email Adress:
        <?= htmlentities($user->getEmail(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>
    </span>
    <br>
    <span>User Phone Number:
        <?= ($user->getPhone()) ? htmlentities($user->getPhone(), ENT_QUOTES | ENT_IGNORE, "UTF-8") : '' ?>
    </span>
    <br>
    <div class="imageInput">
        <span>User Avatar:</span>
        <img src="uploads/<?= htmlentities($user->getAvatarPath(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?>" class="image">
    </div>
    <form action="/php/show_update_user?userId=<?= $_GET['userId'] ?>" method="post" enctype="multipart/form-data">
        <button type="submit" class="update">Update User</button>
    </form>
    <br>
    <a href="delete_user?userId=<?= $_GET['userId'] ?>">Delete User</a>
</body>