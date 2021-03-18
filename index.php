<?php //SILLOU Justin
spl_autoload_register(function ($className) {
    include ("lib/{$className}.class.php");
});
require('lib/session_start.php');
if (isset($_SESSION['ident'])){
    $personne = $_SESSION['ident'];
}

date_default_timezone_set ('Europe/Paris');
try{
    require('views/pageComplet.php');
} catch (PDOException $e){
    $errorMessage = $e->getMessage();
    require("views/pageErreur.php");
}
?>
