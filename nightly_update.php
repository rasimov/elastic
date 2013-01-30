<?php

function __autoload_elastica ($class) {
 $path = str_replace('_', '/', $class);
 if (file_exists(__DIR__ .'/'. $path . '.php')) {
  require_once(__DIR__ . '/'.$path . '.php');
 }
}

spl_autoload_register('__autoload_elastica');

require_once('Activerecord/ActiveRecord.php');

$cfg = ActiveRecord\Config::instance();
$cfg->set_model_directory('Model');
$cfg->set_connections(array('development' => 'mysql://test:test@localhost/example'));

$documents = Document::all(array('select'=>'documents.*,users.username','joins' => array(
                                                "INNER JOIN queue ON(documents.id = queue.id)",
                                                "INNER JOIN users ON(documents.user_id = users.id)"
                                            )
                                )
                          );

if(count($documents)>0){
   
   $elasticaClient = new \Elastica\Client();
   
   $elasticaIndex = $elasticaClient->getIndex('test');
   
   $elasticaIndex->create(array(
   'number_of_shards' => 4,
   'number_of_replicas' => 1,
   'analysis' => array(
                    'analyzer' => array( 
                           'indexAnalyzer' => array(
                                'type' => 'custom',
                                'tokenizer' => 'standard', 
                                'filter' => array('lowercase', 'test')
                            ),
                           'searchAnalyzer' => array(
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => array('standard', 'lowercase', 'test')
                           )
                    ),
                    'filter' => array(
                           'test' => array(
                                'type' => 'snowball',
                                'language' => 'English'
                           )
                    )
                 )
    ), true);
   
    $elasticaType = $elasticaIndex->getType('doc');
   
    $mapping = new \Elastica\Type\Mapping();
    $mapping->setType($elasticaType);
    $mapping->setParam('index_analyzer', 'indexAnalyzer');
    $mapping->setParam('search_analyzer', 'searchAnalyzer');
    
    $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));
   
    $mapping->setProperties(array(
     'id'      => array('type' => 'integer', 'include_in_all' => TRUE),
     'name'     => array('type' => 'string', 'include_in_all' => TRUE),
     'content'     => array('type' => 'string', 'include_in_all' => TRUE),
     'publish_date'  => array('type' => 'date', 'include_in_all' => TRUE),
     'short_description'=> array('type' => 'string', 'include_in_all' => TRUE),
     'author'=> array('type' => 'string', 'include_in_all' => TRUE),
     '_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
    ));
   
    $mapping->send();

    $queueDelete=array(); 

    array_walk($documents,function(&$doc) use(&$elasticaType,&$queueDelete){
      $queueDelete[] = $doc->id;
      $el_doc = array(
       'id'        => $doc->id,
       'name'      => $doc->name,
       'content'   => $doc->content,
       'publish_date' => strtotime($doc->publish_date->format('Y-m-d H:i:s')),
       'short_description'  => $doc->short_description,
       'author'    => $doc->username,
       '_boost'    => 1.0
      );

      $newDocument = new \Elastica\Document($doc->id, $el_doc);
      $elasticaType->addDocument($newDocument);

    });     

    $elasticaType->getIndex()->refresh();

    Queue::table()->delete(array('id' => $queueDelete));
}
   