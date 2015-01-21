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
    <div class="main">
        <div class="header">
            <a href="/"><?=SITE_LOGO?></a>
            <div class="annotate">
                ATMOS - система контроля и управления климатом в автоматическом и полуавтоматическом режиме.
            </div>
            <?php include('menu.php');?>
        </div>
        <div class="content">
            <b>ATMOS</b> - система контроля состояния окружающей среды различного рода объектов: аквариумы, терариумы,
            домашние растения, инкубаторы, теплицы, огороды, сады и т.п. Позволяет осуществлять контроль в
            автоматическоммрежиме, а также в полуавтоматическом режиме, на удалении.
            <br />
            <br />
            Основные возможности ATMOS
            <ul>
                <li>Установка неограниченного числа контроллеров в системе</li>
                <li>Индивидуальная настройка каждого контроллера под необходимые цели</li>
                <li>Ведение статистики параметров среды для каждого контроллера в отдельности</li>
                <li>Автоматическое поддержание параметров среды в соответствии с заданными настройками контроллерами в автономном режиме</li>
                <li>Централизованное хранение статистики изменений параметров среды</li>
                <li>Базовая станция с web-интерфейсом для управления системой</li>
                <li>Хранение индивидуальной статистики параметров среды в контроллере при отсутсвии связи с базовой станцией</li>
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