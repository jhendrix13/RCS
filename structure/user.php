<?php

/*
 * @USER
 * ~~~~~~~~~~~~
 * @FILE DESCRIPTION: User related proccess
 * @LAST MODIFIED: June 24, 2012
 */

class user
{
    protected $database;
    
    public function __construct(database $database)
    {
        //ACCESS TO DATABASE CLASS
        $this->database = $database;
        
        if($database->getDBStatus() == false) echo '<b>There is no database connection in the database class</b>';
        
        //VALIDATE THE COOKIE
        //makes sure the user doesn't manually
        //change their cookie to something that doesn't exist
        if(isset($_COOKIE['user']))
        {
            //query to check if their IP is banned
            $this->database->processQuery("SELECT * FROM `banned_ips` WHERE `ip` = ? LIMIT 1", array($_SERVER['REMOTE_ADDR']), false);
            $results = $this->database->getRowCount();
            
            //let's make sure that their account isn't banned AND the lastip matches their current IP
            $d = $this->database->processQuery("SELECT `acc_status`,`lastip` FROM `users` WHERE `cookie` = ? LIMIT 1", array($_COOKIE['user']), true);
            
            if($this->database->getRowCount() == 0 || $this->isDisabled($this->getUsername($_COOKIE['user'], 2)) || $results >= 1 || ($d[0]['lastip'] != $_SERVER['REMOTE_ADDR']))
            {
               setcookie('user', null, time()-2147483648, '/', '.'.$_SERVER['HTTP_HOST']);
            }
        }
    }
    
    /*
     * @METHOD  updateLastActive
     * @DESC    updates the user's online status
     */
    
    public function updateLastActive()
    {
        if(isset($_COOKIE['user']))
        {
            $this->database->processQuery("SELECT `time` FROM `online_users` WHERE `cookie` = ?", array($_COOKIE['user']), false);
        
            //insert the user into online users if they aren't
            if($this->database->getRowCount() == 0)
            {
                $this->database->processQuery("INSERT INTO `online_users` VALUES (?, ?)", array($_COOKIE['user'], time()), false);
            }
            else
            {
                //reset their time
                $this->database->processQuery("UPDATE `online_users` SET `time` = ? WHERE `cookie` = ?", array(time(), $_COOKIE['user']), false);
            }
        }
        
        //now let's remove all inactive users
        $this->database->processQuery("DELETE FROM `online_users` WHERE ". time() ." - `time` > 480", array(), false);
    }
    
    /*
     * @METHOD  smileyC
     * @DESC    checks if smileys are enabled
     */
    
    public function smileyC()
    {
        return ($_COOKIE['smiley'] == 'false') ? false : true;
    }
    
    /*
     * @METHOD  postcount
     * @DESC    gets the postcount of a user
     */
    
    public function postcount($username)
    {
        $this->database->processQuery("SELECT * FROM `threads` WHERE `username` = ?", array($username), false);
        $threads = $this->database->getRowCount();
        
        $this->database->processQuery("SELECT * FROM `posts` WHERE `username` = ?", array($username), false);
        return $this->database->getRowCount()+$threads;
    }
    
    /*
     * @METHOD  getMandR
     * @DESC    get the number of messages and replies they've made in msgcenter
     */
    
    public function getMandR($username)
    {
        $this->database->processQuery("SELECT * FROM `messages` WHERE `creator` = ?", array($username), false);
        $messages = $this->database->getRowCount();
        
        $this->database->processQuery("SELECT * FROM `replies` WHERE `username` = ?", array($username), false);
        return $messages.':'.$this->database->getRowCount();
    }
    
    /*
     * @METHOD  getUserIP
     * @DESC    get's the user's IP registered to their acccount
     */
    
    public function getUserIp($username)
    {
        $ip = $this->database->processQuery("SELECT `ip` FROM `users` WHERE `username` = ? LIMIT 1", array($username), true);
        return $ip[0]['ip'];
    }
    
    /*
     * @METHOD  getLastIp
     * @DESC    get's the user's last known IP registered to their acccount
     */
    
    public function getLastIp($username)
    {
        $ip = $this->database->processQuery("SELECT `lastip` FROM `users` WHERE `username` = ? LIMIT 1", array($username), true);
        return $ip[0]['lastip'];
    }
    
    /*
     * @METHOD  ban
     * @DESC    ban the selected user 
     */
    
    public function ban($username, $ipban = false)
    {
        $this->database->processQuery("UPDATE `users` SET `acc_status` = '0' WHERE `username` = ? LIMIT 1", array($username), false);
        if($ipban)
        {
            //get the selected user's original IP and latest IP, then ipban them
            $ips = $this->database->processQuery("SELECT `ip`,`lastip` FROM `users` WHERE `username` = ? LIMIT 1", array($username), true);
            $this->database->processQuery("INSERT INTO `banned_ips` VALUES (null, ?)", array($ips[0]['ip']), false);
            $this->database->processQuery("INSERT INTO `banned_ips` VALUES (null, ?)", array($ips[0]['lastip']), false);
        }
    }
    
    /*
     * @METHOD  unban
     * @DESC    unban the selected user 
     */
    
    public function unban($username)
    {
        $this->database->processQuery("UPDATE `users` SET `acc_status` = '1' WHERE `username` = ?", array($username), false);
    }
    
    /*
     * @METHOD  isDonator
     * @DESC    checks if the user is a donator
     */
    
    public function isDonator($username)
    {
        $data = $this->database->processQuery("SELECT `donator` FROM `users` WHERE `username` = ?", array($username), true);
        return ($data[0]['donator'] == 1) ? true : false;
    }
    
    /*
     * @METHOD isDisabled
     * @DESC   checks if the account is disabled/banned
     */
    
    public function isDisabled($username)
    {
        //see if they have an infraction level over or equal to ten (auto-ban)
        $infractions = $this->database->processQuery("SELECT `worth` FROM `infractions` WHERE `userid` = ? AND (`expiration` >= CURRENT_DATE())", array($this->getIdByName($username)), true);
        $total = 0;
        if(count($infractions) > 0){
            foreach($infractions as $infraction){
                $total+=$infraction['worth'];
            }
        }
        

        $this->database->processQuery("SELECT * FROM `banned_ips` WHERE `ip` = ?", array($_SERVER['REMOTE_ADDR']), false);
        $x = $this->database->getRowCount();
        
        if($this->getRank($username) == 0 || $x >= 1 || $total >= 10)
            return true;
        else
            return false;
    }
    
    /*
     * @METHOD  dName
     * @DESC    shows the user name according to rank
     */
    
    public function dName($username, $rank = null)
    {
        if($rank == null) $rank = $this->getRank($username);
        
        switch($rank)
        {
            case 3:
                $name = '<img src="../img/title2/icon_crown_green.gif"> <span style="color:white">'. $username .'</span>';
                break;
            case 4:
                $name = '<img src="../img/title2/icon_crown_gold.gif"> <span style="color:white">'. $username .'</span>';
                break;
            default:
                $name = $username;
                break;
        }
        
        return ucwords($name);
    }
    
     /*
     * @METHOD  getUserId
     * @DESC    returns a user ID
     */
    
    public function getUserId($cookie)
    {
        $r = $this->database->processQuery("SELECT `id` FROM `users` WHERE `cookie` = ?", array($cookie), true);
        return $r[0]['id'];
    }
    
    /*
     * @METHOD  getUserId
     * @DESC    returns a user ID
     */
    
    public function getIdByName($username)
    {
        $r = $this->database->processQuery("SELECT `id` FROM `users` WHERE `username` = ?", array($username), true);
        return $r[0]['id'];
    }
	
    public function getNameById($id)
    {
        $r = $this->database->processQuery("SELECT `username` FROM `users` WHERE `id` = ?", array($id), true);
        return $r[0]['username'];
    }
    
    public function getSignature($username)
    {
        $r = $this->database->processQuery("SELECT `signature` FROM `users` WHERE `username` = ?", array($username), true);
        return $r[0]['signature'];
    }
    
    /*
     * @METHOD  doesExist
     * @DESC    checks if the specified user does exist
     */
    
    public function doesExist($username)
    {
        $this->database->processQuery("SELECT * FROM `users` WHERE `username` = ?", array($username), false);
        return ($this->database->getRowCount() == 0) ? false : true;
    }
    
    /*
     * @METHOD  checkMute
     * @DESC    checks if the specified user is muted
     */
    
    public function checkMute($username)
    {
        $data = $this->database->processQuery("SELECT `mute_time`,`forum_mute` FROM `users` WHERE `username` = ?", array($username), true);
	
        if($data[0]['forum_mute'] > 0)
        {
            //time already went by
            $seconds_by = time()-$data[0]['mute_time'];

            //time required to go by
            $seconds_left = $data[0]['forum_mute']*3600;

            //time left in hours
            $time_left = round(($seconds_left-$seconds_by)/3600);

            if($time_left > 0) 
            {
                    return $time_left;
            }
            else
            {
                    //their mute has expired
                    $this->database->processQuery("UPDATE `users` SET `forum_mute` = 0 WHERE `username` = ? LIMIT 1", array($username), false);
                    return false;
            }     
        }
        else
        {
            return false;
        }
    }
    
    /*
     * @METHOD  getUsername
     * @DESC    Retrieves a specified user's username based off
     * @DESC    off of a cookie or ID. If $by == 1, we're finding
     * @DESC    their username via ID, else it'll be by cookie
     */
    
    public function getUsername($value,$by)
    {
        ($by == 1) ? $r = $this->database->processQuery("SELECT `username` FROM `users` WHERE `id` = ?", array($value), true) : $r = $this->database->processQuery("SELECT `username` FROM `users` WHERE `cookie` = ?", array($value), true);
        return $r[0]['username'];
    }
    
    /*
     * @METHOD  getRank
     * @DESC    returns the rank of the user by username
     */
    
    public function getRank($username)
    {
       $r = $this->database->processQuery("SELECT `acc_status` FROM `users` WHERE `username` = ?", array($username), true);
       return $r[0]['acc_status'];
    }
    
    /*
     * @METHOD  hasMail
     * @DESC    checks if the user has mail new mail
     */
    
    public function hasMail($username)
    {
        $this->database->processQuery("SELECT * FROM `messages` WHERE `opened` = '0' AND `receiver` = ?", array($username), false);
        return $this->database->getRowCount();
    }
    
    /*
     * @METHOD  mute
     * @DESC    mute the user
     */
    
    public function mute($username, $length)
    {
        $this->database->processQuery("UPDATE `users` SET `mute_time` = ?, `forum_mute` = ? WHERE `username` = ? LIMIT 1", array(time(), (int)$length, $username), false);
    }
    
    /*
     * @METHOD  unmute
     * @DESC    unmute the user
     */
    
    public function unmute($username)
    {
        $this->database->processQuery("UPDATE `users` SET `mute_time` = 0, `forum_mute` = 0 WHERE `username` = ? LIMIT 1", array($username), false);
    }
    
    /*
     * @METHOD  isLoggedIn
     * @DESC    checks if the user is logged in
     */
    
    public function isLoggedIn()
    {
        if(isset($_COOKIE['user']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>