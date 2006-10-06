<?php
    if (!function_exists('file_put_contents')) {
        function file_put_contents($filename, $content) {
            if (!($file = fopen($filename, 'w'))) {
                return false;
            }
            $n = fwrite($file, $content);
            fclose($file);
            return $n ? $n : false;
        }
    }

    // Start PHP session support
    session_start();

    $ok = false;

    $msg = 'Please enter the text in the image in the field below!';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['phrase']) && isset($_SESSION['phrase']) &&
            strlen($_POST['phrase']) > 0 && strlen($_SESSION['phrase']) > 0 &&
            $_POST['phrase'] == $_SESSION['phrase']) {
            $msg = 'OK!';
            $ok = true;
            unset($_SESSION['phrase']);
        } else {
            $msg = 'Please try again!';
        }

        unlink(md5(session_id()) . '.png');

    }

    print "<p>$msg</p>";

    if (!$ok) {
    
        require_once 'Text/CAPTCHA.php';

        // Set CAPTCHA options (font must exist!)
        $options = array(
            'font_size' => 24,
            'font_path' => './',
            'font_file' => 'COUR.TTF'
        );
                   
        // Generate a new Text_CAPTCHA object, Image driver
        $c = Text_CAPTCHA::factory('Image');
        $retval = $c->init(200, 80, null, $options);
        if (PEAR::isError($retval)) {
            echo 'Error generating CAPTCHA!';
            exit;
        }
    
        // Get CAPTCHA secret passphrase
        $_SESSION['phrase'] = $c->getPhrase();
    
        // Get CAPTCHA image (as PNG)
        $png = $c->getCAPTCHAAsPNG();
        if (PEAR::isError($png)) {
            echo 'Error generating CAPTCHA!';
            exit;
        }
        file_put_contents(md5(session_id()) . '.png', $png);
    
        echo '<form method="post">' . 
             '<img src="' . md5(session_id()) . '.png?' . time() . '" />' . 
             '<input type="text" name="phrase" />' .
             '<input type="submit" /></form>';
    }
?>