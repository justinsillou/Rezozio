<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/common_service.php');

$args = new RequestParameters();
$args->defineNonEmptyString('userId');
$args->defineEnum('size', ['small','large'], ['default'=>'small']);

if (! $args->isValid()){
 produceError('Argument(s) invalide(s) --> '.implode(', ',$args->getErrorMessages()));
 return;
} else {
  $data = new DataLayer();
  $getAvatar = $data->getAvatar($args->userId,$args->size);
  if($getAvatar){
    $flux = is_null($getAvatar['data']) ? fopen('../images/avatar_def.png','r') : $getAvatar['data'];
    $mimeType = is_null($getAvatar['data']) ? 'image/png' : $getAvatar['mimetype'];

    header("Content-type: $mimeType");
    fpassthru($flux);
    exit();
  } else {
    produceError("L'userId n'existe pas");
  }
}
?>
