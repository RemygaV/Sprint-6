<?php

declare(strict_types=1);
require_once "functions.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>My project</title>
</head>

<?php

session_start();

if (isset($_GET['action']) and $_GET['action'] == 'logout') {
    session_destroy();
    session_start();
}

$errorMessage = '';

if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) { 
    if ($_POST['username'] == 'User1' && $_POST['password'] == '12345') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $_POST['username']; 
    } else {
        $errorMessage = '<p class="error_message">Wrong username or password !</p>';
    }
}

$currentPath = isset($_GET['cwd']) ? $_GET['cwd'] : getcwd();


switch (true) {
    case (isset($_POST['doUpload'])):
        $fullPath = $currentPath .  '/' . $_FILES['upload_file']['name'];
        if (file_exists($fullPath)) {
            $errorMessage = '<p class="error_message">File exists, choose other name</p>';
        } else {
            $uploadSuccessful = move_uploaded_file($_FILES['upload_file']['tmp_name'], $fullPath);
            if ($uploadSuccessful) {
            } else {
                $errorMessage  = '<p class="error_message">upload failed, please try tomorrow</p>';
            }
        }
        break;
    case isset($_POST['nDir']):
        makeDirectoryAction($currentPath .  '/' . $_POST['nDir']);
        break;
    case isset($_POST['deleteFile']):
        deleteAction($currentPath .  '/' . $_POST['deleteFile']);
        break;
    case isset($_POST['oldName']) && isset($_POST['newName']):
        renameAction($currentPath .  '/' . $_POST['oldName'], $currentPath .  '/' . $_POST['newName']);
        break;
    case (isset($_POST['downloadFile'])):
        $fileToDownloadEscaped = $currentPath .  '/' . $_POST['downloadFile'];
        ob_clean();
        ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf'); 
        header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileToDownloadEscaped)); 
        ob_end_flush();

        readfile($fileToDownloadEscaped);
        exit;
}
?>

<body>
    <?php
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    ?>
        <div class="main_block">

            <button class="logout_btn"><a id="BtnA" href="index.php?action=logout">Logout!</a> </button>

            <div class="header">
                <h1 class="text_h1">File manager tool</h1>
                <h2 class="text_h2"><?= $currentPath ?> </h2>
            </div>

            <table class="topTable">
                <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>

                <?php

                foreach (scandir($currentPath) as $file) {
                    if (in_array($file, ['.', '..'])) {
                        continue;
                    }
                    $path = "$currentPath/$file";
                    $type = is_dir($path) ? "Directory" : "File";
                ?>
                    <tr>
                        <td><?= $type ?></td>
                        <td class="actual-file">
                            <?php
                            if ($type == "Directory") {
                                echo "<a href=\"?cwd=$path\">$file</a>";
                            } else {
                                echo $file;
                            }
                            ?>
                        </td>

                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="oldName" value="<?= $file ?>" /> 
                                <input type="text" name="newName" class="rename_input" value="" />
                                <button class=rename_btn type="submit">Rename</button>
                            </form>

                            <form action="" method="POST">
                                <input type="hidden" name="deleteFile" value="<?= $file ?>" />
                                <button class=delete_btn type="submit">Delete</button>
                            </form>

                            <form action="" method="POST">
                                <input type="hidden" name="downloadFile" value="<?= $file ?>" />
                                <button class="download_btn" type="submit">Download</button>
                            </form>
                        </td>
                    </tr>

                <?php
                }


                ?>
            </table>

            <button class=back_btn><a id=BtnA href="?cwd=<?= dirname($currentPath); ?>"> back to parent directory </a></button>

            <footer class="myFooter">

                <form action="" method="POST">
                    <label for="nDir">Please create a new directory:</label>
                    <input type="text" id="nDir" name="nDir" value="" /><input class="submit" type="submit" value="Submit" />
                </form>

                <form action="" method="POST" enctype="multipart/form-data" class="uploadForm">
                    <input type="file" name="upload_file" />
                    <input class="submit" type="submit" name="doUpload" />
                </form>

                <?php
                if (!empty($errorMessage)) {
                    echo $errorMessage;
                } ?>
            </footer>

        </div>

    <?php
    } else {         
        ?>
        <div class="login_div">

            <form class="login-form" action="./index.php" method="POST">
                <h2 class="log_form-head">Login here:</h2>
                <input class="log_input" type="text" name="username" placeholder="username =User1" required autofocus></br>
                <input class="log_input" type="password" name="password" placeholder="password=12345" required>

                <div><button class="log-submit" type="submit" name="login">Login</button></div>
            </form>

        </div>
        <?php
        if (!empty($errorMessage)) {
            echo $errorMessage;
        }
        ?>
    <?php
    } ?>
</body>

</html>