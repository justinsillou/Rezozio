// SILLOU Justin
window.addEventListener('load',initUsers);

function initUsers(){
  document.getElementById('rechercheUser').addEventListener('input',loadUsers);
}

//affichage des messages au chargement de la page (deconnecte)
function loadUsers(){
  //nettoyage avant recherche
  document.querySelector('section#section_recherche>div.resultrecherche').innerHTML = "";
  let chaine = document.getElementById('rechercheUser').value;
  let urlMessages = 'services/findUsers.php?searchedString='+chaine;
  fetchFromJson(urlMessages)
  .then(processAnswer)
  .then(displayUtilisateurs)
  .then(etatRecherche,finrecherche);
}

function etatRecherche(){
  document.getElementById('espace_variable').hidden = true;
}

function finrecherche(){
  document.getElementById('espace_variable').hidden = false;
}

//affichage des utilisateurs d'après la recherche
function displayUtilisateurs(users){
  let cible = document.querySelector('section#section_recherche>div.resultrecherche');
  for (let authors of users){
    let div = document.createElement('div');
    let avatar = document.createElement('img');
    avatar.src =  "services/getAvatar.php?userId="+authors['userId'];
    let infoUsers = document.createElement('div');
    infoUsers.className = "infoUsers";
    let pseudo = document.createElement('div');
    pseudo.className = "pseudo";
    let author = document.createElement('div');
    author.className = "userId";
    pseudo.innerHTML = authors['pseudo'];
    author.innerHTML = authors['userId'];

    infoUsers.appendChild(pseudo);
    infoUsers.appendChild(author);
    div.appendChild(avatar); //ajout avatar à la div
    div.appendChild(infoUsers); //ajout pseudo à la div

    cible.appendChild(div); //ajout du user recherché au bloc cible
  }
  initProfil();
}
