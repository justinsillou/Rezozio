<?php  // SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/watchdog_service.php');

$args = new RequestParameters("post");
$args->defineString('password', ['default'=>""]);
$args->defineString('pseudo', ['default'=>""]);
$args->defineString('description', ['default'=>""]);

if(strlen($args->pseudo)>25 or strlen($args->description)>1024){
  produceError('Pseudo ou description trop long');
  return;
}
if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $setProfile = $data->setProfile($_SESSION['ident']->login,$password, $pseudo, $description);
  if($setProfile){
    produceResult($setProfile);
  } else {
    produceError('Profil non mis à jour');
  }
}
?>
