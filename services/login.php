<?php // SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);

require_once('lib/common_service.php');
require_once('lib/session_start.php');

if ( ! isset($_SESSION['ident'])) {
  $args = new RequestParameters("post"); //requete en mode post uniquement
  $args->defineNonEmptyString('login');
  $args->defineNonEmptyString('password');

  if (! $args->isValid()){
   produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
   return;
  } else {
   $data = new DataLayer();
   $person = $data->authentifier($args->login,$args->password);
   if ($person !== NULL){
     $_SESSION['ident'] = $person ;
     produceResult($person->login);
   } else{
     produceError("Identifiant ou Mot de passe incorrects");
     return;
   }
  }
} else {
   produceError("déjà authentifié");
   return;
}
?>
