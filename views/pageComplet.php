<?php //SILLOU Justin
 /*
  * Attend les variables globales :
  *  - $listeEquipes : liste des équipes
  *  - $listeEtapes : liste des étapes
  *  - $stats : tableau de statistiques
  * Variable optionnelle :
  *  - $personne est définie si on est dans une session identifiée
  */
  require_once(__DIR__.'/lib/fonctionsHTML.php');
  $dataPersonne ="";    // si utilisateur non authentifié, data-personne n'est pas défini

  // dé-commenter pour la question 3 :
  if (isset($personne))
    $dataPersonne = 'data-personne="'.htmlentities(json_encode($personne)).'"'; // l'attribut data-personne contiendra l'objet personne, en JSON
  // l'utilisateur est authentifié

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
 <meta charset="UTF-8" />
 <title>Rézozio</title>
 <link rel="stylesheet" href="style/rezozio.css" />
 <script src="js/gestion_log.js"></script>
 <script src="js/fetchUtils.js"></script>
 <script src="js/action_messages.js"></script>
 <script src="js/action_recherche.js"></script>
 <script src="js/action_profil.js"></script>
 <script src="js/action_abonnements.js"></script>
</head>
<?php
  echo "<body $dataPersonne>";
?>
<header>
  <h1>Rézozio</h1>
</header>
<section id="espace_fixe">
  <section id="section_recherche">
    <fieldset>
     <legend>Recherche Utilisateur</legend>
     <label for="searchedString"></label>
     <input id="rechercheUser" type="text" name="searchedString">
    </fieldset>
   </form>
   <div class="resultrecherche">
   </div>
  </section>

  <section id="section_messages">
    <div id="postMessage"></div>
    <div class="messages"></div>
    <button id="moreMessages">Afficher plus de messages</button>
  </section>

  <section id="section_profil">
   <div class="resultprofil">
   </div>
  </section>
</section>

<section id="espace_variable">

 <section class="deconnecte">
   <form method="POST" action="services/login.php"  id="form_login">
    <fieldset>
     <legend>Connexion</legend>
     <label for="login">Login :</label>
     <input type="text" name="login" id="login" required="" autofocus=""/></br>
     <label for="password">Mot de passe :</label>
     <input type="password" name="password" id="password" required="required" /></br>
     <button type="submit" name="valid">OK</button></br>
     <output  for="login password" name="message"></output>
    </fieldset>
   </form>

   <form method="POST" action="services/createUser.php"  id="form_createUser">
    <fieldset>
     <legend>Création de l'utilisateur</legend>
     <label for="login">Login :</label>
     <input type="text" name="userId" id="loginInscr" required="" autofocus=""/></br>
     <label for="pseudo">Pseudo :</label>
     <input type="text" name="pseudo" id="pseudoInscr" required="" autofocus=""/></br>
     <label for="password">Mot de passe :</label>
     <input type="password" name="password" id="passwordInscr" required="required" /></br>
     <button type="submit" name="valid">OK</button></br>
     <output  for="login pseudo password" name="message"></output>
    </fieldset>
   </form>
   <button id="retourconnexion">Retour Connexion</button>
   <button id="buttoncreateuser">Créer un compte</button>
 </section>

 <section class="connecte">
  <img id="avatar" src=""  alt="mon avatar" />
  <h2 id="titre_connecte"></h2>
  <section id="gestion_avatar">
    <form method="POST" action="services/uploadAvatar.php" enctype="multipart/form-data" id="form_changeavatar">
      <fieldset>
        <legend>Nouvelle photo de profil ?</legend>
        <input type="file" name="image" required="required">
        <button type="submit" name="valid">Envoyer</button>
      </fieldset>
    </form>
  </section>
  <section id="gestion_abonnements">
    <div id="followers"></div>
    <div id="following"></div>
  </section>
  <button id="logout">Déconnexion</button>
 </section>



</section>
</body>
</html>
