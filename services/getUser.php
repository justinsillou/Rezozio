<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('userId');

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $getUser = $data->getUser($args->userId);
  if($getUser){
    produceResult($getUser);
  } else {
    produceError("Utilisateur Inexistant");
  }
}
?>
