<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/common_service.php');
require_once('lib/session_start.php');

$args = new RequestParameters();
$args->defineNonEmptyString('userId');

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $getProfile = $data->getProfile($args->userId,$_SESSION['ident']->login);
  if($getProfile){
    produceResult($getProfile);
  } else {
    produceError("Utilisateur Inexistant");
  }
}
?>
