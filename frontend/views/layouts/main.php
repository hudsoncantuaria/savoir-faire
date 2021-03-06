<?php
/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
if (!in_array($action, ['login', 'signup', 'request-password-reset', 'reset-password'])) {
    echo $this->render('/layouts/header');
}
echo '<div class="page-wrap">';
echo $content;
echo '</div>';
echo $this->render('/layouts/footer');
echo $this->render('/layouts/_comingSoon');
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
