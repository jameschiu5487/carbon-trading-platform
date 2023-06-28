<?php
    include_once 'header.php';
?>

<body>   
    <main class="main-content">
        <section class="left">
            <div class="left-content">
            <section class="changepass-form">
                <h2>Change <br>Password</h2>
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
                                    echo "<li><a href='trade.php'>Profile page</a></li>";
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
                <section class="changepass-form">
                    <div class="changepass-form-form">
                        <form action="includes/changepass.inc.php" method="post">
                            <input type="text" name="name" placeholder="Company name...">
                            <input type="password" name="pwd" placeholder="Old password...">
                            <input type="password" name="pwdnew" placeholder="New password...">
                            <input type="password" name="pwdrepeat" placeholder="Repeat new password...">
                            <button type="submit" name="submit">Change Password</button>
                        </form>
                        <?php
                        if(isset($_GET["error"])){
                            if($_GET["error"] == "emptyinput"){
                                echo "<p>Fill in all fields!</p>";
                            }
                            else if($_GET["error"] == "notauser"){
                                echo "<p>Unregistered username!</p>";
                            }
                            else if($_GET["error"] == "wrongoldpassword"){
                                echo "<p>Wrong old password!</p>";
                            }
                            else if($_GET["error"] == "passwordnotmatch"){
                                echo "<p>Passwords does not match!</p>";
                            }
                            else if($_GET["error"] == "none"){
                                echo "<p>Password successfully changed!</p>";
                            }
                        }
                        ?>  
                    </div>    
                </section>
            </div>
        </section>
    </main>
</body>











