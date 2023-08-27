<?php require "misc/header.php"; ?>

    <title>LibreY</title>
    </head>
    <body>
        <div class="centered">
            <form class="search-container" action="search.php" method="get" autocomplete="off">
                <h1>Libre<span class="Y">Y</span></h1>
                <input type="text" placeholder="Search Privately" name="q" autofocus/>
                <input type="hidden" name="p" value="0"/>
                <input type="hidden" name="t" value="0"/>
                <input type="submit" class="hide"/>
                <div class="search-button-wrapper hide">
                    <button name="t" value="0" type="submit">Search with LibreY</button>
                    <button name="t" value="3" type="submit">Search torrents with LibreY</button>
                </div>
        </form>
        </div>

<?php require "misc/footer.php"; ?>
