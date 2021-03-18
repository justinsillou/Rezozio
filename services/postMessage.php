<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/watchdog_service.php');

$args = new RequestParameters("post");
$args->defineNonEmptyString('source');

if(strlen($args->source)>280){
  produceError('La taille du message doit etre inférieure ou égale à 280 caracteres');
  return;
}
if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $postMessage = $data->postMessage($args->source,$_SESSION['ident']->login);
  if($postMessage){
    produceResult($postMessage['id']);
  }
}
?>
