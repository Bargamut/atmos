<?php include('top.php');?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="css/default.css" />
    <link rel="shortcut icon" href="<?=SITE_ICON?>" type="image/x-icon">
    <title><?=SITE_TITLE?></title>
    <script type="text/javascript" src="js/jquery/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="js/site/common.js"></script>
</head>

<body>
    <div class="main ">
        <div class="header">
            <a href="/"><?=SITE_LOGO?></a>
            <div class="annotate">
                Разработка, сборка и установка систем контроля доступа, видеонаблюдения и пожароохранных систем.
            </div>
            <?php include('menu.php');?>
        </div>
        <div class="content">
            <b>ООО "Big Brother"</b> - российский производитель электронного оборудования,
            образована в августе 1998 года и в течение 14 лет успешно работает на
            рынке технических средств безопасности. Основная сфера деятельности нашей компании - разработка
            и производство оборудования для решения задач, связанных с проектированием,
            монтажом и реализацией комплексных систем безопасности.
            <br />
            <br />
            Все оборудование и программное обеспечение разрабатывается и производится специалистами фирмы Big Brother
            <ul>
                <li>Оборудование для передачи видеосигнала на большие расстояния по витой паре - видеотрансмиттеры AVT</li>
                <li>Системы передачи видеоизображения при наличии высокого уровня электромагнитных помех</li>
                <li>Системы тревожного оповещения</li>
                <li>Системы проводной двусторонней передачи звука на большие расстояния</li>
                <li>Системы контроля и управления доступом - сетевые и автономные</li>
                <li>Системы платного доступа (билетные системы)</li>
                <li>Системы автоматизированного проката</li>
            </ul>
        </div>
        <div class="push"></div>
    </div>
    <div class="footer">
        <hr />
        <?=DEVELOPERS?>
    </div>
</body>
</html>
<?php include('bottom.php');?>