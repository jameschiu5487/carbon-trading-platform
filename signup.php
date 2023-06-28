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
            <section class="signup-form">
                <h2>Sign <br>Up</h2>
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
                                    // echo "<li><a href='home.php'>home</a></li>";
                                    echo "<li><a href='trade.php'>trading page</a></li>";
                                    echo "<li><a href='includes/logout.inc.php'>Log out</a></li>";
                                }
                                else{
                                    echo "<li><a href='home.php'>Home</a></li>";
                                    echo "<li><a href='signup.php'>Sign up</a></li>";
                                    echo "<li><a href='login.php'>Log in</a></li>";
                                }
                            ?>
                        </ul>
                    </nav>
                </div>
                <section class="signup-form">
                    <div class="signup-form-form">
                        <form action="includes/signup.inc.php" method="post">
                            <input type="text" name="name" placeholder="Full company name...">
                            <input type="number" name="emission" min="0" oninput="validity.valid||(value='');" placeholder="Avg.CO2 emission in past 3 years(mt)...">
                            <select name="industry">
                                <option value="" disabled selected>Industry...</option>
                                <option value="manufacturing">Manufacturing</option>
                                <option value="technology">Technology</option>
                                <option value="finance">Finance</option>
                            </select>
                            <input type="password" name="pwd" placeholder="Password...">
                            <input type="password" name="pwdrepeat" placeholder="Repeat password...">
                            <button type="submit" name="submit">Sign Up</button>
                        </form>
                        <?php
                        if(isset($_GET["error"])){
                            if($_GET["error"] == "emptyinput"){
                                echo "<p>Fill in all fields!</p>";
                            }
                            else if($_GET["error"] == "invaliduid"){
                                echo "<p>Username should only contains NUMBERS and ENGLISH!</p>";
                            }
                            else if($_GET["error"] == "passwordnotmatch"){
                                echo "<p>Passwords does not match!</p>";
                            }
                            else if($_GET["error"] == "usernametaken"){
                                echo "<p>Username already used!</p>";
                            }
                            else if($_GET["error"] == "none"){
                                echo "<p>Successfully signed up!</p>";
                            }
                        }
                        ?>  
                    </div>    
                </section>
            </div>
        </section>
    </main>
</body>











