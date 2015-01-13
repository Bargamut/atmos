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
            <b>Почтовый адрес</b>
            <ul class="contacts">
                <li>
                    Россия<br />
                    190000, Санкт-Петербург, 29-я линия д.45
                </li>
                <li>
                    <table border="0" cellspadding="0" cellspacing="0">
                        <tr>
                            <td>Тел:</td><td>+7 (812)</td><td>321-01-23</td>
                        </tr>
                        <tr>
                            <td>Тел:</td><td>8 (800)</td><td>550-33-11 (бесплатный)</td>
                        </tr>
                        <tr>
                            <td>Тел/Fax:</td><td>+7 (812)</td><td>622-06-22</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td><td>622-02-26</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td><td>622-00-00</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td><td>622-01-23</td>
                        </tr>
                    </table>
                </li>
                <li>
                    E-mail: <a href="mailto:system@homerule.ru">system@homerule.ru</a>
                </li>
            </ul>
            <b>Консультации:</b>
            <ul class="consult">
                <li>
                    Михайлов Михаил Анатольевич<br />
                    Начальник отдела продаж<br />
                    <a href="mailto:grabov@homerule.ru">op@homerule.ru</a><br />
                    555-777-111<br />
                    Skype: <a href="http://skype:otdel.prodazh?call">otdel.prodazh</a>
                </li>
                <li>
                    Иванов Игорь Анатольевич<br />
                    Начальник отдела ВЭД<br />
                    <a href="mailto:26@homerule.ru">ii@homerule.ru</a><br />
                    111-222-333
                </li>
                <li>
                    Волонский Олег Владимирович<br />
                    Тел. +7 (921) 921-01-92<br />
                    <a href="mailto:region1@homerule.ru">vol@homerule.ru</a>
                </li>
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