<?php

date_default_timezone_set('UTC');

require '../tmhOAuth.php';
require '../tmhUtilities.php';
$tmhOAuth = new tmhOAuth(array());

$params = array(
  'q'        => 'food',  //query string
  'since_id'  => '',  //from this ID
  'pages'   => '',  //how many pages 
  'rpp'      => '',     //how many result per page
  'max_id'   => '',  //?
  'geocode'  => '', //?
  'lang'     => 'en' // language
);

foreach ($params as $k => $v) { 
  //$p[$k] = tmhUtilities::read_input($v.":");
  $p[$k] = $v.':';
 // echo $p[$k].'</br>';
  if (empty($p[$k]))
    unset($p[$k]);
}

$pages = intval($p['pages']);
//$pages = 10;
$pages = $pages > 0 ? $pages : 1;
$results = array();

for ($i=2; $i < $pages; $i++) {
  $args = array_intersect_key(
    $p, array(
      'q'        => '',
      'since_id' => '',
      'rpp'      => '',
      'geocode'  => '',
      'lang'     => ''
  ));
  $args['page'] = $i;

  $tmhOAuth->request(
    'GET',
    'http://search.twitter.com/search.json',
    $args,
    false
  );

  echo "Received page {$i}\t{$tmhOAuth->url}" . PHP_EOL;
  echo '</br>';

  if ($tmhOAuth->response['code'] == 200) {
    $data = json_decode($tmhOAuth->response['response'], true);
    foreach ($data['results'] as $tweet) {
      $results[$tweet['id_str']] = $tweet;
    }
  } else {
    $data = htmlentities($tmhOAuth->response['response']);
    echo 'There was an error.' . PHP_EOL;
    var_dump($data);
    break;
  }
}

$save = json_encode($results);
file_put_contents('results.json', $save);

echo count($results) . ' results' . PHP_EOL;
foreach ($results as $result) {
  $date = strtotime($result['created_at']);
  $result['from_user'] = str_pad($result['from_user'], 15, ' ');
  $result['text'] = str_replace(PHP_EOL, '', $result['text']);
  echo "{$result['id_str']}\t{$date}\t{$result['from_user']}\t\t{$result['text']}" . PHP_EOL;
  echo '</br>';
}

?>