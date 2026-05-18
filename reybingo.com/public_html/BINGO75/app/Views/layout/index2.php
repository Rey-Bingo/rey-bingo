<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="description" content="<?= translate('bingo family is an online bingo game designed for the whole family. Enjoy interactive games with virtual cards, automatic hype and the ability to play in real time from any device. Easy to use, no downloads and totally free'); ?>">
    <meta name="keywords" content="<?= translate('bingo family, online bingo game, bingo for the family, online bingo, play free online bingo, family bingo game, HTML and PHP bingo, interactive online bingo, bingo with virtual cards, bingo card generator, personalized bingo for families, bingo game with sound, online multiplayer bingo, real-time bingo, bingo for children and adults, create bingo cards online, bingo hype automatic, bingo number marker, responsive bingo game, PHP MySQL bingo, HTML5 bingo game, bingo with animations, online bingo on mobile devices, bingo for family reunions, free online bingo without download'); ?>">
    <meta name="author" content="IsAppWeb">
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" href="<?= site_url('assets/img/favicon.ico'); ?>?<?= md5(date("Hms")); ?>" type="image/x-icon">
    <link rel="icon" href="<?= site_url('assets/img/favicon-16x16.png'); ?>?<?= md5(date("Hms")); ?>" sizes="16x16" type="image/png">
    <link rel="icon" href="<?= site_url('assets/img/favicon-32x32.png'); ?>?<?= md5(date("Hms")); ?>" sizes="32x32" type="image/png">
    <link rel="icon" href="<?= site_url('assets/img/favicon-96x96.png'); ?>?<?= md5(date("Hms")); ?>" sizes="96x96" type="image/png">
    
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-57x57.png'); ?>?<?= md5(date("Hms")); ?>" sizes="57x57">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-72x72.png'); ?>?<?= md5(date("Hms")); ?>" sizes="72x72">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-76x76.png'); ?>?<?= md5(date("Hms")); ?>" sizes="76x76">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-114x114.png'); ?>?<?= md5(date("Hms")); ?>" sizes="114x114">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-120x120.png'); ?>?<?= md5(date("Hms")); ?>" sizes="120x120">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-144x144.png'); ?>?<?= md5(date("Hms")); ?>" sizes="144x144">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-152x152.png'); ?>?<?= md5(date("Hms")); ?>" sizes="152x152">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-180x180.png'); ?>?<?= md5(date("Hms")); ?>" sizes="180x180">

    <link rel="manifest" href="<?= site_url('assets/img/site.webmanifest.json'); ?>?<?= md5(date("Hms")); ?>">
    <meta name="theme-color" content="#ffffff">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= site_url('assets/img/ms-icon-144x144.png'); ?>?<?= md5(date("Hms")); ?>">

    <link rel="mask-icon" href="<?= site_url('assets/img/safari-pinned-tab.svg'); ?>?<?= md5(date("Hms")); ?>" color="#5bbad5">

    <meta property="og:title" content="<?= APP_NAME; ?>">
    <meta property="og:site_name" content="<?= APP_NAME; ?>">
    <meta property="og:description" content="<?= translate('bingo family is an online bingo game designed for the whole family. Enjoy interactive games with virtual cards, automatic hype and the ability to play in real time from any device. Easy to use, no downloads and totally free'); ?>">
    <meta property="og:image" content="<?= site_url('assets/img/og-image.jpg'); ?>?<?= md5(date("Hms")); ?>">
    <meta property="og:url" content="https://www.bingo.gradialsas.com/">
    <meta property="og:type" content="website">

    <title><?= APP_NAME; ?> · <?= $page['title'] ?></title>

    <link href="<?= site_url('assets/icons/css/all.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link href="<?= site_url('assets/css/sb-admin-2.min.css'); ?>?<?= md5(date("Hms")); ?>" rel="stylesheet">
    <link href="<?= site_url('assets/css/styles.css'); ?>?<?= md5(date("Hms")); ?>" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js?<?= md5(date("Hms")); ?>"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<style>
    .content-bingo {
        width: 100vw;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
</style>
</head>
<body class="bg-gradient-bingo">
    <div class="preloader">
        <div class="canvas">
            <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="img" style="width: 250px;">
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            <div class="loading-percentage">1%</div>
        </div>
    </div>

    <div class="content-bingo" id="content-page">
        <?php
            if (isset($contentPage) && $contentPage != "") {
                echo $contentPage;
            }
        ?>
    </div>

    <?php if (session()->get('logged_in')) : ?>
        <input type="hidden" name="sounds" id="sounds" value="<?= $user['sounds']; ?>">
        <input type="hidden" name="narration" id="narration" value="<?= $user['narration']; ?>">
        <input type="hidden" name="autodial" id="autodial" value="<?= $user['autodial']; ?>">
    <?php else : ?>
        <input type="hidden" name="sounds" id="sounds" value="">
        <input type="hidden" name="narration" id="narration" value="">
        <input type="hidden" name="autodial" id="autodial" value="">
    <?php endif; ?>
            
    <script type="text/javascript">
        var site_url = "<?= site_url(); ?>";
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= site_url('assets/js/app.js'); ?>?<?= md5(date("Hms")); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>