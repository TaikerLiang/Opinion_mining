<?php
// load Zend classes
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Rest_Client');

// define category
$cat = 'mobile';

try {
  // initialize REST client
  $wikipedia = new Zend_Rest_Client('http://en.wikipedia.org/w/api.php');

  // set query parameters
  $wikipedia->action('query');
  $wikipedia->list('categorymembers');
  $wikipedia->cmtitle('Category:'.$cat);
  $wikipedia->cmlimit('30');
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
    <h2>Search results for pages in category 
      '<?php echo $cat; ?>'</h2>
    <ol>
    <?php foreach ($result->query->categorymembers->cm as $c): ?>
      <li><a href="http://www.wikipedia.org/wiki/
        <?php echo $c['title']; ?>">
        <?php echo $c['title']; ?></a></li>
    <?php endforeach; ?>
    </ol>
  </body>
</html>
