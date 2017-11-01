<?php

    /**
     * Minimal class autoloader
     *
     * @param string $class Full qualified name of the class
     */
    function miniAutoloader($class)
    {
        require __DIR__ . '/../src/' . $class . '.php';
    }

    // If the Composer autoloader exists, use it. If not, use our own as fallback.
    $composerAutoloader = __DIR__.'/../vendor/autoload.php';
    if (is_readable($composerAutoloader)) {
        require $composerAutoloader;
    } else {
        spl_autoload_register('miniAutoloader');
    }

    $text = isset($_POST['text']) ? $_POST['text'] : null;
    $to = isset($_POST['to']) ? $_POST['to'] : 'DE';
    $from = isset($_POST['from']) ? $_POST['from'] : 'auto';

    $deepLy = new ChrisKonnertz\DeepLy\DeepLy();

    /**
     * Prints HTML code for a select element. Does not use htmlspecialchars() or whatsoever.
     *
     * @param string $name
     * @param array  $options The options - key => option value, value => option name
     * @param null   $default
     */
    function createSelect($name, array $options, $default = null)
    {
        echo '<select class="form-field" name="'.$name.'">';

        foreach ($options as $optionValue => $optionName) {
            $defaultAttr = ($default !== $optionValue) ? '' : 'selected="selected"';
            echo '<option value="'.$optionValue.'" '.$defaultAttr.'>'.$optionName.'</option>';
        }

        echo '</select>';
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DeepLy Demo - Translation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/framy/latest/css/framy.min.css">
    <style>
        body { padding: 20px }
        h1 { margin-bottom: 40px }
        h4 { margin-top: 40px }
        textarea { resize: vertical; }
        blockquote { margin-left: 0; margin-right: 0; }
        footer { color: #aaa }
        div.success { border: 1px solid #4ce276; margin-top: 20px; padding: 10px; border-top-width: 10px }
        div.error { border: 1px solid #f36362; margin-top: 20px; padding: 10px; border-top-width: 10px }
        .form-select { max-width: 100px }
        .button-group { margin-bottom: 20px }
        .content { margin-bottom: 20px; padding: 20px; box-shadow: 0 1px 3px 0 #c8c8c8; }
    </style>
</head>
<body>
    <h1>DeepLy Demo</h1>

    <div class="button-group">
        <a class="button " href="demo_translate.php">Translate</a>
        <a class="button border" href="demo_detect.php">Detect</a>
        <a class="button border" href="demo_split.php">Split</a>
        <a class="button border" href="demo_ping.php">Ping</a>
    </div>

    <div class="content">
        <form method="POST">
            <div class="form-element">
                <label for="text">Text:</label>
                <textarea id="text" class="form-field" name="text" rows="4"><?php echo $text !== null ? $text : 'Hello world!' ?></textarea>
            </div>

            <div class="form-element">
                <label for="from">From:</label>
                <div class="form-select">
                    <?php createSelect('from', array_combine($deepLy->getLangCodes(), $deepLy->getLangCodes()), $from) ?>
                </div>
            </div>

            <div class="form-element">
                <label for="to">To:</label>
                <div class="form-select">
                    <?php createSelect('to', array_combine($deepLy->getLangCodes(false), $deepLy->getLangCodes(false)), $to) ?>
                </div>
            </div>

            <div id="ping-result"></div>

            <input type="submit" value="Translate" class="button">
        </form>

        <div class="block result">
            <?php

                if ($text !== null and $to !== null) {
                    try {
                        $result = $deepLy->translate($text, $to, $from);

                        echo '<div class="success">Translation: <pre><b>' . $result . '</b></pre></div>';
                    } catch (\Exception $exception) {
                        echo '<div class="error">'.$exception->getMessage().'</div>';
                    }
                }

            ?>
        </div>
    </div>

    <footer class="block">
        <small>
            This is not an official package.
            It is 100% open source and non-commercial.
            DeepL is a product from DeepL GmbH. More info:
            <a href="https://www.deepl.com/publisher.html">www.deepl.com/publisher.html</a>
        </small>
    </footer>

    <script>
        (
            // Use DeepLy's ping method to check if the API server is reachable
            function()
            {
                var request = new XMLHttpRequest();

                request.addEventListener('readystatechange', function() {
                    if (request.readyState === XMLHttpRequest.DONE) {
                        if (request.status !== 200 || request.responseText !== '1') {
                            document.getElementById('ping-result').innerHTML =
                                '<div class="error"><b>WARNING:</b> API seems unreachable.</div>';
                        }
                    }
                });

                request.open('GET', 'demo_ping.php?simple=1', true);
                request.send();
            }
        )();
    </script>
</body>
</html>
