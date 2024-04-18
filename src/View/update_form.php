<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>Update User Info</title>
    <style>
        .link {
            display: inline-block;
            padding: 15px;
            background-color: red;
            color: white;
            text-decoration: none;
            border: 1px solid black;
            margin: 3px;
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
            position: relative;
        }

        .image {
            height: 150px;
            width: 150px;
            border-radius: 100px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        input+label {
            border: 1px solid grey;
            background-color: lightgray;
            padding: 3px;
            display: inline-block;
            width: fit-content;
            position: absolute;
            top: 95px;
            left: 170px;
        }
    </style>
</head>

<body>
    <a href="/php/index" class="nav">Return To Hub</a>
    <a href="/php/view_all_users" class="nav">Return To Users List</a>
    <h1> User Info </h1>
    <form action="/php/update_user?userId=<?= $_GET['userId'] ?>" method="post" enctype="multipart/form-data">
        <label for="first_name">User First Name:</label>
        <input name="first_name" id="first_name" type="text" required
            value="<?= htmlentities($user->getFirstName(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>">
        <br>
        <label for="last_name">User Last Name:</label>
        <input name="last_name" id="last_name" type="text" required
            value="<?= htmlentities($user->getLastName(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>">
        <br>
        <label for="middle_name">User Middle Name:</label>
        <input name="middle_name" id="middle_name" type="text" value="<?php
        ($user->getMiddleName() === null)
            ? $middleName = null
            : $middleName = htmlentities($user->getMiddleName(), ENT_QUOTES | ENT_IGNORE, "UTF-8");
        echo $middleName;
        ?>">
        <br>
        <label for="gender">User Gender:</label>
        <input name="gender" id="gender" type="text" required
            value="<?= htmlentities($user->getGender(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>">
        <br>
        <label for="birth_date">User Birth Date:</label>
        <input name="birth_date" id="birth_date" type="date" required value="<?php
        $timestamp = strtotime($user->getBirthDate());
        echo date("Y-m-d", $timestamp);
        ?>">
        <br>
        <label for="email">User Email Adress:</label>
        <input name="email" id="email" type="email" required
            value="<?= htmlentities($user->getEmail(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?? '' ?>">
        <br>
        <label for="phone">User Phone Number:</label>
        <input name="phone" id="phone" type="tel" value="<?php
        ($user->getPhone() === null)
            ? $phone = null
            : $phone = htmlentities($user->getPhone(), ENT_QUOTES | ENT_IGNORE, "UTF-8");
        echo $phone;
        ?>">
        <br>
        <div class="imageInput">
            <span>User Avatar:</span>
            <img src="uploads/<?= htmlentities($user->getAvatarPath(), ENT_QUOTES | ENT_IGNORE, "UTF-8") ?>"
                class="image">
            <br>
            <input name="avatar_path" id="avatar_path" type="file" style="display: none;">
            <label for="avatar_path">Upload Image</label>
        </div>
        <button type="submit" class="link" style="background-color: green">Submit</button>
        <a href="show_user?userId=<?= $_GET['userId'] ?>" class="link" style="background-color: grey;">Cancel</a>
        <br><br>
        <a href="delete_user?userId=<?= $_GET['userId'] ?>" class="link">Delete User</a>
    </form>
</body>