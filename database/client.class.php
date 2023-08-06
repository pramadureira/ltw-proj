<?php
    require_once(__DIR__ . '/../database/ticket.class.php');
    class Client {
        public string $username;
        public string $name;
        public string $email;
        public bool $isAgent;
        public bool $isAdmin;
        public array $departments;

        public function __construct(string $username, string $name, string $email, bool $isAgent, bool $isAdmin, array $departments) {
            $this->username = $username;
            $this->name = $name;
            $this->email = $email;
            $this->isAgent = $isAgent;
            $this->isAdmin = $isAdmin;
            $this->departments = $departments;
        }

        static function getAgentDepartments(PDO $db, string $username) {
            $stmt2 = $db->prepare('SELECT username, D.name FROM AgentDepartment JOIN Client using(userId) JOIN Department as D USING(departmentID) WHERE username = ?');
            $stmt2->execute(array($username));
            $dep = $stmt2->fetchAll();
            return $dep;
        }

        static function getAllUsers(PDO $db) : array {
            $stmt = $db->prepare('SELECT c.username, c.name, c.email, COALESCE(a.isAgent, false) as isAgent, COALESCE(ad.isAdmin, false) as isAdmin FROM Client c LEFT JOIN Agent a ON c.userId = a.userId LEFT JOIN Admin ad ON a.userId = ad.userId ORDER BY a.isAgent DESC, ad.isAdmin DESC');
            $stmt->execute();
            $users = array();
            while ($user = $stmt->fetch()) {
                $dep = Client::getAgentDepartments($db, $user['username']);

                $users[] = new Client(
                    $user['username'],
                    $user['name'],
                    $user['email'],
                    (bool) $user['isAgent'],
                    (bool) $user['isAdmin'],
                    $dep
                );
            }

            return $users;
        }

        static function getUser(PDO $db, string $username): Client {
            $stmt = $db->prepare('SELECT c.username, c.name, c.email, COALESCE(a.isAgent, false) as isAgent, COALESCE(ad.isAdmin, false) as isAdmin FROM Client c LEFT JOIN Agent a ON c.userId = a.userId LEFT JOIN Admin ad ON a.userId = ad.userId WHERE c.username = ?');
            $stmt->execute(array($username));
            $user = $stmt->fetch();
            $dep = Client::getAgentDepartments($db, $username);
            $client = new Client(
                $user['username'],
                $user['name'],
                $user['email'],
                (bool) $user['isAgent'],
                (bool) $user['isAdmin'],
                $dep
            );
            return $client;
        }

        static function updateUserRole(PDO $db, string $username, bool $isAgent, bool $isAdmin, string $author) {
            $stmt = $db->prepare('
                UPDATE Admin SET isAdmin = ?
                WHERE userId = (SELECT userId FROM Client WHERE username = ?)
            ');

            $stmt->execute(array($isAdmin ? 1 : 0, $username));
            
            $stmt = $db->prepare('
                UPDATE Agent SET isAgent = ?
                WHERE userId = (SELECT userId FROM Client WHERE username = ?)
            ');
            $stmt->execute(array(($isAdmin || (!$isAdmin && $isAgent)) ? 1 : 0, $username));

            if (!$isAdmin && !$isAgent) {
                $stmt = $db->prepare('SELECT departmentId FROM AgentDepartment WHERE userId=(SELECT userId FROM Client WHERE username = ?)');
                $stmt->execute(array($username));
                while ($dep = $stmt->fetch()) {
                    Ticket::updateTicketStatus($db, Client::getUserId($db, $username), $dep['departmentId'], Client::getUserId($db,$author));
                }

                $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE userId=(SELECT userId FROM Client WHERE username = ?)');
                $stmt->execute(array($username));
            }
        }

        static function getUserId(PDO $db, string $username) {
            $stmt2 = $db->prepare('SELECT userId FROM Client WHERE username = ?');
            $stmt2->execute(array($username));
            $id = $stmt2->fetch();
            if ($id === false) return false;
            else return $id['userId'];
        }

        static function getUsername(PDO $db, int $userId) {
            $stmt2 = $db->prepare('SELECT username FROM Client WHERE userId = ?');
            $stmt2->execute(array($userId));
            return $stmt2->fetch()['username'];
        }

        static function updateUserDepartments(PDO $db, string $username, string $department, bool $add, $author) {
            $stmt = $db->prepare('SELECT departmentId FROM Department WHERE name = ?');
            $stmt->execute(array($department));
            $departmentId = $stmt->fetch();

            if ($departmentId !== false) {
                $departmentId = $departmentId['departmentId'];
            } else return false;
            if ($add) {
                $stmt = $db->prepare('SELECT isAgent FROM Agent JOIN Client using(userId) WHERE username = ?');
                $stmt->execute(array($username));
                $isAgent = $stmt->fetch()['isAgent'];
                
                if (!$isAgent) {
                    return false;
                }
                $userId = Client::getUserId($db, $username);
                $stmt2 = $db->prepare('SELECT * FROM AgentDepartment WHERE userId = ? and departmentID = ?');
                $stmt2->execute(array($userId, $departmentId));
                $exists = $stmt2->fetch();
                if ($exists !== false) return false;

                $stmt = $db->prepare('INSERT INTO AgentDepartment (userId, departmentID) VALUES (?, ?)');
            } else {
                $stmt = $db->prepare('DELETE FROM AgentDepartment WHERE userId=? AND departmentID=?');
                Ticket::updateTicketStatus($db, Client::getUserId($db, $username), $departmentId, Client::getUserId($db,$author));
            }

            $stmt->execute(array(Client::getUserId($db, $username), $departmentId));
        }
    }
?>
