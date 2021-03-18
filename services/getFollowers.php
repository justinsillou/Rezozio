<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/watchdog_service.php');

$data = new DataLayer();
$getFollowers = $data->getFollowers($_SESSION['ident']->login);
if($getFollowers){
  produceResult($getFollowers);
} else {
  produceResult([]); //renvoie tableau vide si l'utilisateur n'est suivi par personne
}
?>
