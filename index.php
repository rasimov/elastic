<?php

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

function __autoload_elastica ($class) {
 $path = str_replace('_', '/', $class);
 if (file_exists(__DIR__ .'/'. $path . '.php')) {
  require_once(__DIR__ . '/'.$path . '.php');
 }
}
spl_autoload_register('__autoload_elastica');


$app = new \Slim\Slim();

$app->get('/search', function () {

 $search = isset($_REQUEST['q'])?$_REQUEST['q']:'';
 $search = '*'.$search.'*';


$elasticaClient = new \Elastica\Client();
$elasticaIndex = $elasticaClient->getIndex('test');

$elasticaQueryString = new Elastica\Query\QueryString();
$elasticaQueryString->setDefaultOperator('AND');
$elasticaQueryString->setQuery($search);

$elasticaQuery = new Elastica\Query();
$elasticaQuery->setQuery($elasticaQueryString);

$page = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;

$pagesize = isset($_REQUEST['pagesize'])?intval($_REQUEST['pagesize']):10;

$elasticaQuery->setFrom($page);
$elasticaQuery->setLimit($pagesize);

$elasticaResultSet 	= $elasticaIndex->search($elasticaQuery);

$elasticaResults 	= $elasticaResultSet->getResults();

$totalResults 		= $elasticaResultSet->getTotalHits();

 foreach ($elasticaResults as $elasticaResult) {
   ?>
   <table width='100%' border='1'>
    <tr>
     <td>Author: <?php echo $elasticaResult->author?></td><td>Name: <?php echo $elasticaResult->name?></td><td>Name: <?php echo date('m/d/Y H:i',$elasticaResult->publish_date)?></td>
    </tr>
    <tr>
     <td colspan='3'>short description: <?php echo $elasticaResult->short_description?></td>
    </tr>
    <tr>
     <td colspan='3'>text: <?php echo $elasticaResult->content?></td>
    </tr>
   <?php
 }

});

// GET route

$app->get('/', function () {
   ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>test</title> 
  <script type="text/javascript" src="/js/jquery.js"></script>
 </head>
 <body>
     <input name="search" value="" id="search"><input type="button" value="search!" id="searchbtn"><br>
     <hr>
     Results:
     <div style="border:1px solid red;min-height:50px;" id="data">
     <script>
       function getData(){
        $.get('/search?q='+$('#search').val(), function(data) {
            $('#data').html(data);
        });
       }
       $('#searchbtn').click(function(){getData();});
       getData();
     </script>
     </div>
 </body>
</html>
 <?php
});

// POST route
$app->post('/post', function () {
    echo 'This is a POST route';
});

// PUT route
$app->put('/put', function () {
    echo 'This is a PUT route';
});

// DELETE route
$app->delete('/delete', function () {
    echo 'This is a DELETE route';
});

$app->run();
