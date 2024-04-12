<?php

session_start();


try {
    include '../dbConnect.php';
    // print_r($_POST);
    // echo "<br/>";
    // print_r($_SESSION);

    if (isset($_POST['register'])) {

        $rq = $pdo->prepare('SELECT * FROM users WHERE email like :email');
        $rq->bindValue(':email', htmlspecialchars($_POST['mail']));
        $rq->execute();

        if (empty($rq->fetchAll())) {
            $rq = $pdo->prepare('INSERT INTO users(email, password) VALUES (:email, :code)');
            $rq->bindValue(':email', htmlspecialchars($_POST['mail']));
            $rq->bindValue(':code', hash('sha256', htmlspecialchars($_POST['code'])));
            $rq->execute();

            $_SESSION['user'] = $_POST['mail'];
            $_SESSION['verified'] = 0;
        }
    } else if (isset($_POST['login'])) {
        $rq = $pdo->prepare('SELECT * FROM users WHERE email like :email AND password LIKE :code');
        $rq->bindValue(':email', htmlspecialchars($_POST['mail']));
        $rq->bindValue(':code', hash('sha256', htmlspecialchars($_POST['code'])));
        $rq->execute();
        $result = $rq->fetchAll();
        // echo "<br/>";
        // print_r($result);

        if (!empty($result)) {
            $_SESSION['user'] = $result[0]['email'];
            $_SESSION['verified'] = $result[0]['verified'];


            $rq = $pdo->prepare('SELECT * FROM apikeys WHERE api_key=:key');
            $rq->bindValue(':key', $_SESSION['verified']);
            $rq->execute();
            $_SESSION['uses'] = $rq->fetchAll()[0]['uses'];
            // header("Refresh:1");
        } else {
            $badPassword = true;
        }
    } else if (isset($_POST['unlog'])) {
        unset($_SESSION['user']);
        unset($_SESSION['verified']);
        header("Refresh:0");
    } else if (isset($_POST['verify'])) {
        echo "<script>window.sendTo = '" . $_SESSION['user'] . "'</script>";
        echo "<script src='verifyMail.js'></script>";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlfPI</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="left">
        <ul>
            <li><a href="#Api_structure">Api structure</a></li>
            <li><a href="#Using_Fetch">Using Fetch</a></li>
            <li><a href="#Getting_feedback">Getting feedback</a></li>
        </ul>
    </div>



    <div id="content">
        <h1>AlfPI</h1>

        <section id="Api_structure">
            <h2>Api structure</h2>
            <p>This api work around http requests and get arguments. You will have to specify the recipient, the sender
                and the content of the mail.</p>

            <div class="codeReview">
                <div class="head">
                    <p>JS</p>
                </div>
                <div class="body">
                    <pre>
const <strong>apiKey</strong> = 'API KEY'
const <strong>sender</strong> = 'sender@gmail.com'
const <strong>content</strong> = 'content to send'
                    
const request = 'https://alfpi.top/api?key=' + <strong>apiKey</strong> + '&fr=' + <strong>sender</strong> + '&c=' + <strong>content</strong>
</pre>
                </div>
            </div>
        </section>


        <section id="Using_Fetch">
            <h2>Using Fetch</h2>
            <p>The mail api is usable easily using only fetch</p>
            <div class="codeReview">
                <div class="head">
                    <p>JS</p>
                </div>
                <div class="body">
                    <pre>
fetch(request);
</pre>
                </div>
            </div>
        </section>

        <section id="Getting_feedback">
            <h2>Getting feedback using a basic function</h2>
            <p>If you want to have a feedback, like knowing if the mail has been sent you should use a simple function
                like
                this</p>
            <div class="codeReview">
                <div class="head">
                    <p>JS</p>
                </div>
                <div class="body">
                    <pre>
sendMail(request);

async function sendMail(arg) {
    const response = await fetch(arg);
    const json = await response.json(arg);
    console.log(json);
}</pre>
                </div>
            </div>
        </section>
    </div>



    <div id="right">
        <div id="account">
            <?php

            if (isset($_SESSION['user'])) {
                echo "<p>Logged as " . $_SESSION['user'] . "</p>";
                echo "<form action='' method='POST'>";
                echo '<input type="hidden" name="unlog" value="true">';
                echo '<input type="submit" value="Unlog">';
                echo "</form>";

                if ($_SESSION['verified'] == 0) {
                    echo "<p>Verify your email to get your API key</p>";
                    echo "<form action='' method='POST'>";
                    echo '<input type="hidden" name="verify" value="true">';
                    echo '<input type="submit" value="Send Mail">';
                    echo "</form>";
                } else {
                    echo "<button onclick='(function(){ navigator.clipboard.writeText(`" . $_SESSION['verified'] . "`);})();'>Copy API KEY</button>";
                    echo "<p>" . 200 - $_SESSION['uses'] . "/200 uses left for this month</p>";
                }
            } else {
                echo '
                <h4>Login</h4>
                <form action="" method="POST">
                    <input type="hidden" name="login" value="true">
                    <input type="email" name="mail" required placeholder="email">
                    <input type="password" name="code" required placeholder="password">
                    <input type="submit">
                </form>';
                if (isset($badPassword)) {
                    echo 'wrong informations';
                }

                echo '<h4>Register</h4>
                <form action="" method="POST">
                    <input type="hidden" name="register" value="true">
                    <input type="email" name="mail" required placeholder="email">
                    <input type="password" name="code" required placeholder="password">
                    <input type="submit">
                </form>
                ';
            }

            ?>
        </div>
    </div>
</body>

</html>