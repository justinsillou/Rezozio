<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/common_service.php');

$args = new RequestParameters("post");
$args->defineNonEmptyString('userId');
$args->defineNonEmptyString('password');
$args->defineNonEmptyString('pseudo');

if(strlen($args->userId)>25 || strlen($args->pseudo)>25){
  produceError('La taille du login et du pseudo doivent etre inferieurs à 25 caractères');
  return;
}

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $userCreated = $data->createUser($args->userId,$args->pseudo,$args->password);
  if($userCreated){
    produceResult($userCreated);
  } else {
    produceError("Login déjà existant ou saisie non valide");
  }
}
?>
