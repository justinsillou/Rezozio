<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('searchedString');

if(strlen($args->searchedString)<3){
  produceError('La taille de la recherche doit etre supérieure ou égale à 3 caracteres');
  return;
}

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $findUsers = $data->findUsers($args->searchedString);
  if($findUsers)
    produceResult($findUsers);
  else
    produceResult([]);
}
?>
