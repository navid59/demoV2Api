<?php
include_once '../../vendor/autoload.php';
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
$readMeFile = getenv('PROJECT_DOC_PATH');
$readMeContent = file_get_contents($readMeFile);
?>
<link href="assets/css/markdown.css" rel="stylesheet" type="text/css">
<?php echo $converter->convertToHtml($readMeContent); ?>
