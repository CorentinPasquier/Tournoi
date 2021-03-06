<?php
session_start();

if (ReadIni::getInstance()->getAttribute('general', 'debug') == true) {
    // DEBUG MODE ON
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');
    // DEBUG MODE ON
}

function __autoload($class_name) {
    if (strpos($class_name, 'Model') !== false) {
        include_once "../model/{$class_name}.php";
    } else if (strpos($class_name, 'Collection') !== false) {
        include_once "../collection/{$class_name}.php";
    } else if (strpos($class_name, 'Controller') !== false) {
        include_once "../controller/{$class_name}.php";
    } else {
        include_once "../tools/{$class_name}.php";
    }
}

$_page = ReadIni::getInstance()->getAttribute('general', 'index_page');
if (isset($_GET['page'])) {
    $_page = $_GET['page'];
}
Access::getInstance()->controlAccess($_page);

if (Access::getInstance()->getErrorController()) {
    $_controller = Access::getInstance()->getErrorController();
} else {
    $controllerName = $_page . 'Controller';
    /**
     * @var $_controller Controller
     */
    $_controller = Controller::getController($controllerName);

    if ($_controller && isset($_GET['action'])) {
        $_action = $_GET['action'] . 'Action';
        if (method_exists($_controller, $_action))
            call_user_func(array($_controller, $_action));
        else {
            /** @var ErrorController $_controller */
            $_controller = Controller::getController('ErrorController');
            $_controller->notFound();
        }
    }
}
$messageManager = new MessageManager();
$messages = $messageManager->getMessages();
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $_controller->getTitle(); ?></title>
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

	<!-- FAVICON -->
	<link rel="shortcut icon" href="images/favicon/favicon.ico" type="image/x-icon">
	<link rel="icon" href="images/favicon/favicon.png" type="image/png">
	<link rel="icon" sizes="32x32" href="images/favicon/favicon-32.png" type="image/png">
	<link rel="icon" sizes="64x64" href="images/favicon/favicon-64.png" type="image/png">
	<link rel="icon" sizes="96x96" href="images/favicon/favicon-96.png" type="image/png">
	<link rel="icon" sizes="196x196" href="images/favicon/favicon-196.png" type="image/png">
	<link rel="apple-touch-icon" sizes="152x152" href="images/favicon/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="60x60" href="images/favicon/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="76x76" href="images/favicon/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/favicon/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="images/favicon/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="images/favicon/apple-touch-icon-144x144.png">
	<meta name="msapplication-TileImage" content="images/favicon/favicon-144.png">
	<meta name="msapplication-TileColor" content="#FFFFFF">


        <link rel="stylesheet" href="<?php echo $_controller->getUrlFile('/css/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo $_controller->getUrlFile('/css/bootstrap-theme.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo $_controller->getUrlFile('/css/style.css'); ?>">
        <?php foreach ($_controller->getCssFile() as $cssFile): ?>
            <link rel="stylesheet" href="<?php echo $_controller->getUrlFile($cssFile); ?>">
        <?php endforeach; ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
        <script src="<?php echo $_controller->getUrlFile('/js/bootstrap.min.js'); ?>"></script>
        <?php foreach ($_controller->getJsFile() as $jsFile): ?>
            <script src="<?php echo $_controller->getUrlFile($jsFile); ?>"></script>
        <?php endforeach; ?>
    </head>
    <body>
    <?php
    if (ReadIni::getInstance()->getAttribute('general', 'google_analytics')): ?>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', '<?php echo ReadIni::getInstance()->getAttribute('general', 'google_analytics'); ?>', 'auto');
            ga('send', 'pageview');

        </script>
    <?php endif; ?>
    <?php echo $_controller->getHeader(); ?>
    <div class="container-fluid body">
        <div class="row">
            <div class="col-lg-4 col-lg-offset-4">
                <?php /** @var Message $message */ ?>
                <?php foreach ($messages as $message): ?>
                    <?php echo $message->getMessageHtml(); ?><br/>
                <?php endforeach; ?>
            </div>
        </div>
        <?php echo $_controller->getHtml(); ?>
    </div>
    <?php echo $_controller->getFooter(); ?>
    </body>
</html>
