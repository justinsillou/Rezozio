<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/watchdog_service.php');

$data = new DataLayer();
$getSubscriptions = $data->getSubscriptions($_SESSION['ident']->login);
if($getSubscriptions)
  produceResult($getSubscriptions);
else
  produceResult([]); //renvoie tableau vide si l'utilisateur ne suit personne
?>
