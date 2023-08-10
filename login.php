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
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>   
    <main class="main-content">
        <section class="left">
            <div class="left-content">
            <section class="login-title">
                <h2>Log <br>in</h2>
            </section>
            </div>
        </section>
        <section class="right">
            <div class="right-content">
                <div class="container">
                    <nav>
                        <ul>
                            <?php
                                if(isset($_SESSION["useruid"])){
                                    echo "<li><a href='trade.php'>Trading page</a></li>";
                                    echo "<li><a href='includes/logout.inc.php'>Log out</a></li>";
                                }
                                else{
                                    echo "<li><a href='index.php'>Home</a></li>";
                                    echo "<li><a href='signup.php'>Sign up</a></li>";
                                    echo "<li><a href='login.php'>Log in</a></li>";
                                }
                            ?>
                        </ul>
                    </nav>
                </div>
                <section class="login-form">
                    <div class="login-form-form">
                        <form action="includes/login.inc.php" method="post">
                            <input type="text" name="name" placeholder="Company name...">
                            <input type="password" name="pwd" placeholder="Password...">
                            <button type="submit" name="submit">Log In</button>
                        </form>
                        <?php
                            if(isset($_GET["error"])){
                                if($_GET["error"] == "emptyinput"){
                                    echo "<p>Fill in all fields!</p>";
                                }
                                else if($_GET["error"] == "wronglogin"){
                                    echo "<p>Please try again!</p>";
                                }
                            }
                        ?>
                    </div>
                </section>
            </div>
        </section>
    </main>
</body>


    