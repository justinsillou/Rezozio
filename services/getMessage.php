<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineInt('messageId');

if (! $args->isValid()){
 produceError('argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $getMessage = $data->getMessage($args->messageId);
  if($getMessage){
    produceResult($getMessage);
  } else {
    produceError("le messageId n'existe pas");
  }
}
?>
