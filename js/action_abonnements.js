// SILLOU Justin

function sendFollowers(){
  let url='services/getFollowers.php';
  fetchFromJson(url,{method:'POST',credentials:'same-origin'})
  .then(processAnswer)
  .then(displayFollowers);
}

function sendTarget(){
  let url='services/getSubscriptions.php';
  fetchFromJson(url,{method:'POST',credentials:'same-origin'})
  .then(processAnswer)
  .then(displaySubscriptions);
}

function follow(){
  
}

function unfollow(){

}

function displayFollowers(abonnes){
  let cible = document.getElementById('followers');
  cible.innerHTML = "";
  let titre = document.createElement('p')
  titre.textContent = "Followers";
  cible.appendChild(titre);
  for (let abonne of abonnes){
    let div = document.createElement('div');
    let avatar = document.createElement('img');
    avatar.src =  "services/getAvatar?userId="+abonne['userId'];
    let infoUsers = document.createElement('div');
    infoUsers.className = "infoUsers";
    let pseudo = document.createElement('div');
    pseudo.className = "pseudo";
    let author = document.createElement('div');
    author.className = "userId";
    pseudo.innerHTML = abonne['pseudo'];
    author.innerHTML = abonne['userId'];

    infoUsers.appendChild(pseudo);
    infoUsers.appendChild(author);
    div.appendChild(avatar); //ajout avatar à la div
    div.appendChild(infoUsers); //ajout pseudo à la div
    cible.appendChild(div); //ajout du user recherché au bloc cible
  }
  initProfil();
}

function displaySubscriptions(abonnes){
  let cible = document.getElementById('following');
  cible.innerHTML = "";
  let titre = document.createElement('p')
  titre.textContent = "Following";
  cible.appendChild(titre);
  for (let abonne of abonnes){
    let div = document.createElement('div');
    let avatar = document.createElement('img');
    avatar.src =  "services/getAvatar?userId="+abonne['userId'];
    let infoUsers = document.createElement('div');
    infoUsers.className = "infoUsers";
    let pseudo = document.createElement('div');
    pseudo.className = "pseudo";
    let author = document.createElement('div');
    author.className = "userId";
    pseudo.innerHTML = abonne['pseudo'];
    author.innerHTML = abonne['userId'];

    infoUsers.appendChild(pseudo);
    infoUsers.appendChild(author);
    div.appendChild(avatar); //ajout avatar à la div
    div.appendChild(infoUsers); //ajout pseudo à la div
    cible.appendChild(div); //ajout du user recherché au bloc cible
  }
  initProfil();
}
