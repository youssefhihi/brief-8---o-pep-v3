<?php
  session_start();
 $email=  $_SESSION["user_email"] ;

  ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/role.css">
    <title>Role Selection</title>
</head>

<body>

    <form class="container" method="POST" action="../controller/chooserole.php">
    <input type="hidden" name="email" value="<?php echo $email; ?>" >
    
        <div class="radiocont">
            <div class="form-container user">
                <i class="fas fa-user"></i>
                <label for="client">Client</label>
                <input value="1" name="role_id" type="radio">
            </div>
            <div class="form-container admin">
                <i class="fas fa-user-tie"></i>
                <label for="admin">Admin</label>
                <input value="2" name="role_id" type="radio">
            </div>
        </div>
        <button class="confirm" type="submit" name="updateRole">Confirm</button>
    </form>

    <script src="../asset/js/script.js"></script>

</body>

</html>
