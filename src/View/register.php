<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <style>
        a {
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
    <a href="/php/index">Return To Hub</a>
    <form action="/php/register_user" enctype="multipart/form-data" method="post">
        <div>
            <label for="first_name">First Name:</label>
            <input name="first_name" id="first_name" type="text" required>
        </div>
        <div>
            <label for="last_name">Last Name:</label>
            <input name="last_name" id="last_name" type="text" required>
        </div>
        <div>
            <label for="middle_name">Middle Name:</label>
            <input name="middle_name" id="middle_name" type="text">
        </div>
        <div>
            <label for="gender">Gender:</label>
            <input name="gender" id="gender" type="text" required> 
        </div>
        <div>
            <label for="birth_date">Birth Date:</label>
            <input name="birth_date" id="birth_date" type="date" required>
        </div>
        <div>
            <label for="email">Email Adress:</label>
            <input name="email" id="email" type="email" required>
        </div>
        <div>
            <label for="phone">Phone Number:</label>
            <input name="phone" id="phone" type="tel">
        </div>
        <div>
            <label for="avatar_path">Avatar Path:</label>
            <input name="avatar_path" id="avatar_path" type="file" accept="image/png, image/jpeg, image/jpg, image/gif">
        </div>
        <button type="submit">Submit</button>
    </form>
</body>

</html>