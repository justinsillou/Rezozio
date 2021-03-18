// SILLOU Justin
window.addEventListener('load',initMessages);
window.addEventListener('load',loadMessages);

function initMessages(){
  document.getElementById('moreMessages').addEventListener('click',moreMessages);
}

//affichage des messages au chargement de la page (deconnecte)
function loadMessages(){
  let urlMessages = 'services/findMessages.php';
  fetchFromJson(urlMessages)
  .then(processAnswer)
  .then(displayMessages);
}

function postMessage(ev){
  ev.preventDefault()
  let url = 'services/postMessage.php';
  let args = new FormData(this);
  fetchFromJson(url, {method:'POST',body:args, credentials:'same-origin'})
  .then(processAnswer)
  .then(afficheNouveauMessage, displayErrorMessages);
}

function afficheNouveauMessage(){
  let cible  = document.querySelector('section#section_messages>div.messages');
  cible.textContent=''; // effacement
  loadMessages();
}

function moreMessages(){
  //recupération du dernier id du message
  let node = document.querySelectorAll('div.messages>div');
  let before = node[node.length - 1].id;
  let url = 'services/findMessages?count=10&before='+before;
  fetchFromJson(url)
  .then(processAnswer)
  .then(displayMessages);
}

//affichage formulaire post message
function displaypostmessage(){
  let cible = document.getElementById('postMessage');
  let formulaire = document.createElement("form");
  formulaire.id="form_message";
  formulaire.action="services/postMessage.php"
  formulaire.method="post";
  let text = document.createElement("textarea");
  text.maxlength = "280";
  text.name ="source";
  text.id = "newmessage";
  text.placeholder="Nouveau message";
  text.required = "required";
  let button = document.createElement("button");
  button.type = "submit";
  button.name = "valid";
  let textButton = document.createTextNode("Envoyer");

  formulaire.appendChild(text);
  button.appendChild(textButton);
  formulaire.appendChild(button);
  formulaire.addEventListener("submit",postMessage);
  cible.appendChild(formulaire);
}

function displayMessages(messages){ //affichage du message
  let cible = document.querySelector('section#section_messages>div.messages');
  for (let message of messages){
    let divMessage = document.createElement('div');
    divMessage.id = message['messageId'];
    divMessage.className = "message";
    let avatarMessage = document.createElement('img');
    avatarMessage.src =  "services/getAvatar.php?userId="+message['author'];
    let contentMessage = document.createElement('p');
    let infoUsers = document.createElement('div');
    infoUsers.className = "infoUsers";
    let pseudoMessage = document.createElement('div');
    pseudoMessage.className = "pseudo";
    let authorMessage = document.createElement('div');
    authorMessage.className = "userId";
    let dateMessage = document.createElement('time');
    //------- AFFICHAGE DE LA DATE FORMAT FIREFOX & AUTRE ----------
    let date = message['datetime'];
    if (/.*[+-][0-9]{2}$/.exec(date)) // le time-shift ne comporte pas les minutes
     date+=':00';
    d = new Date(date);
    //---------------------------------------------------------------

    pseudoMessage.innerHTML = message['pseudo'];
    authorMessage.innerHTML = message['author'];
    contentMessage.innerHTML = message['content'];
    dateMessage.innerHTML = d.toLocaleString();

    infoUsers.appendChild(pseudoMessage);
    infoUsers.appendChild(authorMessage);
    divMessage.appendChild(avatarMessage); //ajout avatar à la div
    divMessage.appendChild(infoUsers);
    divMessage.appendChild(contentMessage); //ajout content à la div
    divMessage.appendChild(dateMessage); //ajout date à la div

    cible.appendChild(divMessage); //ajout du message au bloc cible
  }
  initProfil();
}

function displayErrorMessages(error){ //Message d'erreur message
  let p = document.createElement('p');
  p.innerHTML = error.message;
  let cible  = document.querySelector('section#section_messages>div.resultat');
  cible.textContent=''; // effacement
  cible.appendChild(p);
}
