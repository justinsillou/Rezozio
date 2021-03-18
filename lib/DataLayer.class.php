<?php // SILLOU Justin
require_once("lib/db_parms.php");

Class DataLayer{
    private $connexion;
    public function __construct(){

            $this->connexion = new PDO(
                       DB_DSN, DB_USER, DB_PASSWORD,
                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                       ]
                     );
            $this->connexion->query("SET search_path = rezozio");

    }

    //authentification
    function authentifier($login,$password){
      $sql = <<< EOD
      select
      login, pseudo, password
      from "users"
      where login = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt ->bindValue(':login',$login);
      $stmt ->execute();
      $info = $stmt->fetch();
      if ($info && crypt($password, $info['password']) == $info['password']) {
        return new Identite($info['login'], $info['pseudo']);
      } else {
        return NULL;
      }
    }

    //creation d'utilisateur
    function createUser($login,$pseudo,$password){
      $sql = <<<EOD
      insert into users (login, pseudo, password)
      values (:login, :pseudo, :password)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login',$login);
      $stmt->bindValue(':password', password_hash($password, CRYPT_BLOWFISH));
      $stmt->bindValue(':pseudo',$pseudo);
      try{
        $stmt->execute();
        return ['userId'=>$login,'pseudo'=>$pseudo];
      } catch (PDOException $e){
          return false;
      }
    }

    //get
    function getUser($login){
      $sql = <<<EOD
      select login as "userId", pseudo
      from users
      where login = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login',$login);
      try {
        $stmt->execute();
        return $stmt->fetch();
      } catch (PDOException $e) {
        return false;
      }
    }

    function getProfile($userId,$current){
      $sql = <<<EOD
      select users.login as "userId", users.pseudo, users.description,
      s1.target is not null as "followed", s2.target is not null as "isFollower"
      from users
      left join subscriptions as s1 on users.login = s1.target and s1.follower = :current

      left join subscriptions as s2 on users.login = s2.follower and s2.target = :current
      where users.login = :userId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':userId',$userId);
      $stmt->bindValue(':current',$current);
      try {
        $stmt->execute();
        return $stmt->fetch();
      } catch (PDOException $e){
        return false;
      }

    }

    function getMessage($messageId){
      $sql = <<<EOD
      select messages.id as "messageId", messages.author, users.pseudo, messages.content, messages.datetime
      from messages
      join users on messages.author = users.login
      where messages.id = :messageId
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':messageId',$messageId);
      try {
        $stmt->execute();
        return $stmt->fetch();
      } catch (PDOException $e){
        return false;
      }
    }

    function getAvatar($login, $size){
      $sql = <<<EOD
      select login, avatar_small, avatar_large, avatar_type
      from users
      where login = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
        if($size == 'small')
          $stmt->bindColumn('avatar_small', $avatar, PDO::PARAM_LOB);
        else
          $stmt->bindColumn('avatar_large', $avatar, PDO::PARAM_LOB);
      $stmt->bindColumn('avatar_type', $avatar_type);
      $stmt->execute();
      $res = $stmt->fetch();
      if ($res)
        return ['mimetype'=>$avatar_type,'data'=>$avatar];
      else
        return false;
    }

    function getSubscriptions($login){
      $sql = <<<EOD
      select users.login as "userId", users.pseudo
      from users
      left join subscriptions on users.login = subscriptions.target
      where subscriptions.follower = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    function getFollowers($target){
    $sql = <<<EOD
    select users.login as "userId", users.pseudo, t2.follower is not null as "mutual"
    from subscriptions as t1
    left join subscriptions as t2 on t1.follower = t2.target and t2.follower = :target
    join users on login = t1.follower
    where t1.target = :target
EOD;
    $stmt = $this->connexion->prepare($sql);
    $stmt->bindValue(':target', $target);
    $stmt->execute();
    return $stmt->fetchAll();
    }

    //find
    function findUsers($searchedString){
      $sql = <<<EOD
      select login, pseudo
      from users
      where login like :searchedString ||'%' or pseudo like :searchedString ||'%'
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':searchedString', $searchedString);
      $stmt->bindColumn('login',$userId);
      $stmt->bindColumn('pseudo',$pseudo);
      $res = [];
      $stmt->execute();
      while ($stmt->fetch()) {
          $res[]= ['userId'=>$userId,'pseudo'=>$pseudo];
      }
      if($res)
        return $res;
    }

    function findMessages($author,$before,$count){
      $sql = <<<EOD
      select messages.id as "messageId", messages.author, users.pseudo, messages.content, messages.datetime
      from messages
      join users on messages.author = users.login
EOD;
      if ($author != "" and $before == 0)
        $sql .= " where messages.author = :author";
      if ($before != 0 and $author == "")
        $sql .= " where messages.id < :before";
      if ($author != "" and $before != 0)
        $sql .= " where messages.author = :author and messages.id < :before";
      $sql .= " order by messages.id desc";
      $stmt = $this->connexion->prepare($sql);
      if ($author != "")
        $stmt->bindValue(':author',$author);
      if ($before != 0)
        $stmt->bindValue(':before',$before);

      $stmt->execute();
      while ($count > 0) {
        $content = $stmt->fetch();
        if($content)
          $res[]= $content;
        $count--;
      }
      return $res;
    }

    function findFollowedMessages($login,$before, $count){
      $sql = <<<EOD
      select messages.id as "messageId", subscriptions.target as "author" , users.pseudo, messages.content, messages.datetime
      from messages
      inner join users on messages.author = users.login
      inner join subscriptions on messages.author = subscriptions.target
      where subscriptions.follower = :login
EOD;
      if ($before != 0)
        $sql .= " and messages.id < :before";
      $sql .= " order by messages.id desc";
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login',$login);
      if ($before != 0)
        $stmt->bindValue(':before',$before);

      $stmt->execute();
      while ($count > 0) {
        $content = $stmt->fetch();
        if($content)
          $res[]= $content;
        $count--;
      }
      return $res;
    }

/*
    //cette fonction récupére le dernier id en fonction d'un user
    //soit le dernier message posté par l'user
    function lastMessageId($author){
      $sql = <<<EOD
      select max(id) as "id"
      from messages
      where author = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $author);
      $stmt->execute();
      return $stmt->fetch();
    }
*/
    //post
    function postMessage($source, $login){
      $sql = <<<EOD
      insert into
      messages (author, content)
      values (:login, :source)
      returning id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      $stmt->bindValue(':source', $source);
      try{
        $stmt->execute();
        return $stmt->fetch();
      } catch (PDOException $e){
        return false;
      }
    }

    //follow
    function follow($login, $target){
      $sql = <<<EOD
      insert into
      subscriptions (follower, target)
      values (:login, :target)
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      $stmt->bindValue(':target', $target);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      } catch (PDOException $e){
        return false;
      }
    }

    //unfollow
    function unfollow($login, $target){
      $sql = <<<EOD
      delete from subscriptions
      where follower=:login and target=:target
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      $stmt->bindValue(':target', $target);
      $stmt->execute();
      return $stmt->rowCount() == 1;
    }

    function setProfile($login, $password, $pseudo, $description){
      $sql = <<<EOD
      update users
      set password = :password or pseudo = :pseudo or description = :description
      where login = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':login', $login);
      if($password != "")
        $stmt->bindValue(':password', $password);
      if($pseudo != "")
        $stmt->bindValue(':pseudo', $pseudo);
      if($description != "")
        $stmt->bindValue(':description', $description);

      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      } catch (PDOException $e){
        return false;
      }
    }

    /*public function setProfile($userId, $password, $pseudo, $description)
    {
        if ($password != "")
        {
            $request = "UPDATE users
                        SET password = :password
                        WHERE users.login = :userId";
            $stmt = $this->connexion->prepare($request);
            $stmt->bindValue(':userId', $userId);
            $encrypted_password = password_hash($password, CRYPT_BLOWFISH);
            $stmt->bindValue(':password', $encrypted_password);
            $stmt->execute();
        }
        if ($pseudo != "")
        {
            $request = "UPDATE users
                        SET pseudo = :pseudo
                        WHERE users.login = :userId";
            $stmt = $this->connexion->prepare($request);
            $stmt->bindValue(':pseudo', $pseudo);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
        }
        if ($description != "")
        {
            $request = "UPDATE users
                        SET description = :description
                        WHERE users.login = :userId";
            $stmt = $this->connexion->prepare($request);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
        }

        $request = 'SELECT users.login AS "userId", users.pseudo FROM users WHERE users.login = :userId';
        $stmt = $this->connexion->prepare($request);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        $res = $stmt->fetch();
        return $res;
    }*/

    //upload avatar
    function storeAvatar($imagespec48, $imagespec256, $login){
      $sql = <<<EOD
      update users
      set (avatar_type, avatar_small, avatar_large) = (:avatar_type, :avatar_small, :avatar_large)
      where login = :login
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(":login", $login);
      $stmt->bindValue(":avatar_small", $imagespec48['data'], PDO::PARAM_LOB);
      $stmt->bindValue(":avatar_large", $imagespec256['data'], PDO::PARAM_LOB);
      $stmt->bindValue(":avatar_type", $imagespec48['mimetype']);
      try{
        $stmt->execute();
        return $stmt->rowCount() == 1;
      } catch (PDOException $e){
        return false;
      }
    }
}
?>
