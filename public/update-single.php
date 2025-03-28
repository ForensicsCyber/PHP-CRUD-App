<?php
require "../common.php";
require_once '../src/DBconnect.php';

// Define the escape function early
function escape($data) { 
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"); 
    $data = trim($data); 
    $data = stripslashes($data); 
    return $data; 
}

$user = []; // Initialize the $user variable to prevent errors

if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE id = :id";
        $statement = $connection->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "User not found.";
            exit;
        }
    } catch(PDOException $error) {
        echo "Database error: " . $error->getMessage();
        exit;
    }
} else {
    echo "Something went wrong!";
    exit;
}

if (isset($_POST['submit'])) {
    try {
        $user = [
            "id" => escape($_POST['id']),
            "firstname" => escape($_POST['firstname']),
            "lastname" => escape($_POST['lastname']),
            "email" => escape($_POST['email']),
            "age" => escape($_POST['age']),
            "location" => escape($_POST['location']),
        ];

        $sql = "UPDATE users
                SET firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    age = :age,
                    location = :location
                WHERE id = :id";

        $statement = $connection->prepare($sql);
        $statement->execute($user);

        $message = "User successfully updated.";
    } catch(PDOException $error) {
        $message = "Error updating user: " . $error->getMessage();
    }
}
?>

<?php require "templates/header.php"; ?>

<?php if (!empty($message)) : ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<h2>Edit a user</h2>
<form method="post">
    <?php if (!empty($user)) : ?>
        <?php foreach ($user as $key => $value) : ?>
            <label for="<?php echo $key; ?>"><?php echo ucfirst($key); ?></label>
            <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>"
            value="<?php echo escape($value); ?>" <?php echo ($key === 'id' ? 'readonly' : null); ?>>
        <?php endforeach; ?>
    <?php else : ?>
        <p>User not found or invalid ID.</p>
    <?php endif; ?>

    <input type="submit" name="submit" value="Submit">
</form>

<a href="index.php">Back to home</a>

<?php require "templates/footer.php"; ?>