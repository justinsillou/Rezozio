//SILLOU Justin
window.addEventListener('load',initAll);

var currentUser = null; //objet "personne" de l'utilisateiur connecté

function initAll(){
  initState();
  initLog();
}

function initState(){ // initialise l'état de la page
  let personne = document.body.dataset.personne;
  if (typeof personne == "undefined") etatDeconnecte();
  else etatConnecte(JSON.parse(personne));
}

function initLog(){ // mise en place des gestionnaires sur le formulaire de login et le bouton logout
  document.forms.form_login.addEventListener('submit',sendLogin); // envoi
  document.forms.form_login.addEventListener('input',function(){this.message.value='';}); // effacement auto du message
  document.forms.form_createUser.addEventListener('submit',sendInscription); // envoi form inscription
  document.forms.form_changeavatar.addEventListener('submit',sendAvatar);
  document.querySelector('#buttoncreateuser').addEventListener('click',etatInscription);
  document.querySelector('#logout').addEventListener('click',sendLogout);
}

function etatDeconnecte() { // passe dans l'état 'déconnecté'
    // cache ou montre les éléments
    for (let elt of document.querySelectorAll('.connecte'))
       elt.hidden=true;
    for (let elt of document.querySelectorAll('.deconnecte'))
       elt.hidden=false;
    // nettoie la partie personnalisée :
    currentUser = null;
    delete(document.body.dataset.personne);
    document.querySelector('#retourconnexion').hidden=true;
    document.querySelector('#form_createUser').hidden=true;
    document.querySelector('#buttoncreateuser').hidden=false;
    document.querySelector('#form_login').hidden=false;
    document.querySelector('#titre_connecte').textContent='';
    document.querySelector('#avatar').src='';

    document.querySelector('#postMessage').textContent = "";
}

function etatInscription(){
  document.getElementById('form_login').hidden = true;
  document.getElementById('buttoncreateuser').hidden = true;
  let button = document.getElementById('retourconnexion');
  button.hidden = false;
  button.addEventListener('click',etatDeconnecte);
  document.getElementById('form_createUser').hidden = false;
}

function etatConnecte(personne){ // passe dans l'état 'connecté'
  currentUser = personne;
  if(typeof currentUser.login == "undefined") currentUser.login = currentUser.userId;
  // cache ou montre les éléments
  for (let elt of document.querySelectorAll('.deconnecte'))
    elt.hidden=true;
  for (let elt of document.querySelectorAll('.connecte'))
    elt.hidden=false;


  //personnaliser le contenu

  document.querySelector('#titre_connecte').innerHTML = `<b>${currentUser.pseudo}</b>`;

  updateAvatar(); //affiche avatar profil user connecte
  displaypostmessage();  //affiche le post message
  //gestion des abonnements -------
  sendFollowers();
  sendTarget();
  //--------------
}

function updateAvatar() {
    let changeAvatar = function(blob) {
      if (blob.type.startsWith('image/')){ // le mimetype est celui d'une image
        let img = document.getElementById('avatar');
        img.src = URL.createObjectURL(blob);
      }
    };
  fetchBlob('services/getAvatar.php?size=large&userId='+currentUser.login)
    .then(changeAvatar);
}

function processAnswer(answer){
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
}

function sendLogin(ev){ // gestionnaire de l'événement submit sur le formulaire de login
  ev.preventDefault();
  let args = new FormData(this);
  let url = 'services/login.php';
  fetchFromJson(url, {method: 'post', body: args, credentials: 'same-origin'})
  .then(processAnswer)
  .then(recupcompte, errorLogin);
}

function recupcompte(personne) {
  let url = 'services/getUser.php?userId='+personne;
  fetchFromJson(url)
  .then(processAnswer)
  .then(etatConnecte, errorLogin);
}

function sendInscription(ev){ // gestionnaire de l'événement submit sur le formulaire de login
  ev.preventDefault();
  let args = new FormData(this);
  let url = 'services/createUser.php';
  fetchFromJson(url, {method: 'post', body: args, credentials: 'same-origin'})
  .then(processAnswer)
  .then(etatDeconnecte, errorCreation);

  document.querySelector('#section_profil').textContent = "";
}

function sendAvatar(ev){ // gestionnaire de l'événement submit sur le formulaire de login
  ev.preventDefault();
  let args = new FormData(this);
  console.log(args);
  let url = 'services/uploadAvatar.php';
  fetchFromJson(url, {method: 'post', body: args, credentials: 'same-origin'})
  .then(processAnswer)
  .then(updateAvatar, errorAvatar);
}

function sendLogout(ev){ // gestionnaire de l'événement submit sur le formulaire de logout
  ev.preventDefault();
  let url = 'services/logout.php';
  fetchFromJson(url, {method: 'post', credentials: 'same-origin'})
  .then(processAnswer)
  .then(etatDeconnecte,errorLogin);

  document.querySelector('#section_profil').textContent = "";
}

function errorLogin(error) {
   // affiche error.message dans l'élément OUTPUT.
  document.forms.form_login.message.value = 'Echec : ' + error.message;
}

function errorCreation(error) {
   // affiche error.message dans l'élément OUTPUT.
  document.forms.form_createUser.message.value = 'Echec : ' + error.message;
}

function errorAvatar(error) {
   // affiche error.message dans l'élément OUTPUT.
  document.forms.form_changeavatar.message.value = 'Echec : ' + error.message;
}
