<?php


include_once __DIR__ . '/includes/application_top.php';

$url = 'http://deals.expedia.com/beta/deals/hotels.json';
$session = curl_init( $url );                                           
curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );                            
$json = curl_exec( $session );                                                   
$phpObj = json_decode( $json );

var_dump( $phpObj );
die();


/*
require __DIR__ . '/vendor/autoload.php';
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__.'/views',
));

$app->get('/twig/{name}', function ($name) use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => $name,
    ));
});

$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Herrera\Pdo\PdoServiceProvider(),
  array(
    'pdo.dsn' => 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"],
    'pdo.port' => $dbopts["port"],
    'pdo.username' => $dbopts["user"],
    'pdo.password' => $dbopts["pass"]
  )
);


$app->get('/db/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT name FROM test_table');
  $st->execute();

  $names = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['name']);
    $names[] = $row;
  }

  return $app['twig']->render('database.twig', array(
    'names' => $names
  ));
});
*/
