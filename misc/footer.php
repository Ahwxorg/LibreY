<div class="footer-container">
    <a class="hide" href="./">LibreY</a>
    <a href="https://github.com/Ahwxorg/librey/" target="_blank">Source</a>
    <a href="./instances.php" target="_blank">Instances</a>
    <a href="./settings.php">Settings</a>
    <a href="./api.php" target="_blank">API</a>
    <a href="./donate.php">Donate ❤️</a>
</div>
<div class="git-container">
    <?php
        $hash = file_get_contents(".git/refs/heads/main");
        echo "<a href=\"https://github.com/Ahwxorg/librey/commit/$hash\" target=\"_blank\">Latest commit: $hash</a>";
    ?>
</div>
</body>
</html>
