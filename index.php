<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>carbon trading website</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>   
    <main class="home">
        <section class="login-form">
            <div class="container">
                <nav>
                    <ul>
                        <?php
                            echo "<li><a href='login.php'>Carbon Trading Platform</a></li>";
                        ?>
                    </ul>
                </nav>
            </div>
            <a>Click to LOGIN / SIGNUP</a>
        </section>
    </main>
</body>

    