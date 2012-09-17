<?php
// load Zend classes
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Rest_Client');

// define category prefix
//$prefix = 'samsung mobile phones';
$prefix = "HTC";

try {
  // initialize REST client
  $wikipedia = new Zend_Rest_Client('http://en.wikipedia.org/w/api.php');

  // set query parameters
  $wikipedia->action('query');
  $wikipedia->list('allcategories');
  $wikipedia->acprefix($prefix);
  $wikipedia->format('xml');

  // perform request
  // iterate over XML result set
  $result = $wikipedia->get();
} catch (Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
<html>
  <head></head>
  <body>
    <h2>Search results for categories starting with 
      '<?php echo $prefix; ?>'</h2>
    <ol>
    <?php foreach ($result->query->allcategories->c as $c): ?>
      <li><a href="http://www.wikipedia.org/wiki/Category:
        <?php echo $c; ?>"><?php echo $c; ?></a></li>
    <?php endforeach; ?>
    </ol>
  </body>
</html>