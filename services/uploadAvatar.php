<?php //SILLOU Justin
set_include_path('..'.PATH_SEPARATOR);
spl_autoload_register(function ($className) {
     include ("lib/{$className}.class.php");
 });

require_once('lib/watchdog_service.php');

//ajout de args pas reussi donc json encode et ajout manuel si $uploadAvatar

if (!isset($_FILES['image']) || $_FILES['image']['tmp_name']==''){
  produceError('Fichier image non reçu');   // cas des fichiers trop volumineux
  return;
} else if (strpos($_FILES['image']['type'],'image/') !== 0 ){
  produceError('Le fichier reçu n\'est pas une image');
  return;
}

function createImageFromStream($stream){
   return imagecreatefromstring(stream_get_contents($stream));
}

function createImageFromFile($fileName){
    return imagecreatefromstring(file_get_contents($fileName));
}

$image = createImageFromFile($_FILES['image']['tmp_name']);  // création de l'image source
$largeur = imagesx($image);
$hauteur = imagesy($image);
$c = min($largeur,$hauteur);                         // dimension du plus grand carré
$image48 = imagecreatetruecolor(48,48);              // création de l'image 48x48 (vide)
$image256 = imagecreatetruecolor(256,256);           // création de l'image 256x256 (vide)
imagecopyresampled($image48, $image, 0, 0, ($largeur-$c)/2, ($hauteur-$c)/2, 48, 48, $c, $c);    // génération de l'image après découpage et redimensionnement
imagecopyresampled($image256, $image, 0, 0, ($largeur-$c)/2, ($hauteur-$c)/2, 256, 256, $c, $c); // génération de l'image après découpage et redimensionnement

$fluxTmp256 = fopen("php://temp", "r+");            // création d'un flux de stockage temporaire
$fluxTmp48 = fopen("php://temp", "r+");             // création d'un flux de stockage temporaire
imagepng($image48, $fluxTmp48);                     // écriture de l'image en PNG dans le flux
imagepng($image256, $fluxTmp256);                   // écriture de l'image en PNG dans le flux
rewind($fluxTmp48);                                 // repositionnement en début de flux
rewind($fluxTmp256);                                // repositionnement en début de flux

$data = new DataLayer();
$imagespec48 = ['data'=>$fluxTmp48,"mimetype"=>$_FILES['image']['type']];
$imagespec256 = ['data'=>$fluxTmp256,"mimetype"=>$_FILES['image']['type']];

$uploadAvatar = $data->storeAvatar($imagespec48,$imagespec256,$_SESSION['ident']->login);
if($uploadAvatar){
  unset($_FILES['image']['tmp_name']);
  echo json_encode(['status'=>'ok','result'=>true,'args'=>$_FILES['image'],'time'=>date('d/m/Y H:i:s')]);
}
?>
