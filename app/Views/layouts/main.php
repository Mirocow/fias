<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <!--meta name="viewport" content="width=device-width, initial-scale=1"-->
    <?//= Html::csrfMetaTags() ?>
    <title><?//= Html::encode($this->title) ?></title>
</head>
<body>

<div class="wrap wrap-main">
    <?= $content ?>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left one">бесплатная техподдержка<br><span class="lead">8 800 500-30-79</span></p>
        <div class="pull-left social">
            <h4>мы в соц. сетях</h4>
            <a href="#"><i class="i-facebook"></i></a>
            <a href="#"><i class="i-twitter"></i></a>
        </div>
    </div>
</footer>

<div class="dropdown-menu-all-layout"></div>
<div id="js-alerts-all-messages"></div>
</body>
</html>
