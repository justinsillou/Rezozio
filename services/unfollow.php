<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/watchdog_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('target');

if($args->target == $_SESSION['ident']->login){
  produceError('Vous ne pouvez pas vous unfollow !');
  return;
}

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $unfollow = $data->unfollow($_SESSION['ident']->login,$args->target);
  if($unfollow)
    produceResult($unfollow);
  else
    produceError("Utilisateur non suivi ou inexistant");
}
?>
