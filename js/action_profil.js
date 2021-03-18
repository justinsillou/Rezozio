// SILLOU Justin
window.addEventListener('load',initProfil);

function initProfil(){
  let elements = document.getElementsByClassName("infoUsers");
  for (var i=0; i< elements.length; i++){
    elements[i].addEventListener('click',loadProfil);
  }
}

function loadProfil(){
  //nettoyage avant recherche
  document.querySelector('section#section_profil>div.resultprofil').textContent = "";
  let userId = this.getElementsByTagName('div')[1].textContent;
  if (!currentUser){
    getProfile(userId);
  } else if (currentUser.login !== userId) {
    getProfile(userId);
  }
}

function getProfile(userId){
  let url = 'services/getProfile.php?userId='+userId;
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayProfil)
  .then(etatProfil);
}

function etatProfil(){
  document.getElementById('section_messages').hidden = true;
}

function displayProfil(profil){ //affichage du profil (usernormal)
  let cible = document.querySelector('section#section_profil>div.resultprofil');
  let divcontenu = document.createElement('div');
  let avatar = document.createElement('img');
  avatar.src =  "services/getAvatar.php?size=large&userId="+profil['userId'];
  let pseudo = document.createElement('span');
  let userid = document.createElement('span');
  let description = document.createElement('p');
  pseudo.innerHTML = profil['pseudo'];
  userid.innerHTML = profil['userId'];
  description.innerHTML = profil['description'];
  let button = document.createElement('button');
  button.id = "retourmessages";
  let textButton = document.createTextNode("Retour aux messages");

  divcontenu.appendChild(avatar); //ajout avatar à la div
  divcontenu.appendChild(pseudo); //ajout author à la div
  divcontenu.appendChild(userid); //ajout pseudo à la div
  divcontenu.appendChild(description); //ajout content à la div
  divcontenu.appendChild(button);
  button.appendChild(textButton);
  button.addEventListener('click',etatMessages);

  cible.appendChild(divcontenu); //ajout du message au bloc cible

}

function etatMessages(){
  document.getElementById('section_messages').hidden = false;
  document.getElementById('section_profil').hidden = true;
}

function displayErrorMessages(error){ //Message d'erreur message
  let p = document.createElement('p');
  p.innerHTML = error.message;
  let cible  = document.querySelector('section#section_messages>div.resultat');
  cible.textContent=''; // effacement
  cible.appendChild(p);
}
