<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/watchdog_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('target');

if($args->target == $_SESSION['ident']->login){
  produceError('Vous ne pouvez pas vous suivre !');
  return;
}

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $follow = $data->follow($_SESSION['ident']->login,$args->target);
  if($follow)
    produceResult($follow);
  else
    produceError("Utilisateur déjà suivi ou non existant");
}
?>
