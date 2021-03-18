<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineString('author', ['default'=>""]);
$args->defineInt('before', ['default'=>0]);
$args->defineInt('count', ['min_range'=>0, 'default'=>15]);

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $findMessages = $data->findMessages($args->author,$args->before,$args->count);
  if($findMessages){
    produceResult($findMessages);
  } else {
    produceResult([]);
  }
}
?>
