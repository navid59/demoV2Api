<?php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();
$readMeFile = "docs/README.md";
$readMeContent = file_get_contents($readMeFile);
?>
<link href="assets/css/markdown.css" rel="stylesheet" type="text/css">
<?php echo $converter->convertToHtml($readMeContent); ?>
