<?php
use App\Database\ConnectionProvider;

require_once __DIR__ . '/vendor/autoload.php';
print_r($_FILES);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tmpFile = $_FILES['pic']['tmp_name'];
    $newFile = '' . $_FILES['pic']['name'];
    $result = move_uploaded_file($tmpFile, $newFile);
    $connection = ConnectionProvider::getConnection();
    $query = <<<SQL
    INSERT INTO `user` 
    (`first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`)
    VALUES (:firstName, :lastName, :middleName, :gender, :birthDate, :email, :phone, :avatarPath)
    SQL;
    try {
        $statement = $connection->prepare($query);
        if ($statement === false) {
            throw new \Exception('INVALID DATABASE REQUEST. FAILED TO INSERT DATA INTO DATABASE.');
        } else {
            $statement->execute([
                ':firstName' => 'oleg',
                ':lastName' => 'oleg',
                ':middleName' => '',
                ':gender' => 'female',
                ':birthDate' => '2024-04-10 00:00:00',
                ':email' => 's3uka@mail.sru',
                ':phone' => '4',
                ':avatarPath' => $newFile,
            ]);
        }
        echo $_FILES['pic']['name'] . '<br>';
        if ($result) {
            echo ' was uploaded<br />';
        } else {
            echo ' failed to upload<br />';
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>
<html>

<body>
    <form action="#" enctype="multipart/form-data" method="POST">
        <input type="file" name="pic">
        <input type="submit" value="Upload">
    </form>
    <img src="<?= $newFile ?>">
</body>

</html>