<?php
    declare(strict_types = 1);
    require_once(__DIR__ . '/../database/client.class.php');
    require_once(__DIR__ . '/../database/department.php');
    require_once(__DIR__ . '/../database/misc.php');

    class Ticket {
        public int $ticketId;
        public string $title;
        public string $body;
        public ?string $department;
        public array $hashtags;
        public int $priority;
        public string $status;
        public DateTime $date;
        public int $client;
        public ?int $agent;


        public function __construct(int $ticketId, string $title, string $body, ?int $department, string $hashtags, int $priority,
                                        string $status, string $date, int $client, ?int $agent) {

            $this->ticketId = $ticketId;
            $this->title = $title;
            $this->body = $body;
            $this->department = getDepartment($department);
            $this->hashtags = json_decode($hashtags, true);
            $this->priority = $priority;
            $this->status = $status;
            $this->date = new DateTime($date);
            $this->client = $client;
            $this->agent = $agent;
        }

        static function getAllTickets(PDO $db, string $username) {
            $stmt = $db->prepare('SELECT * FROM Ticket ORDER BY date DESC');
            $stmt->execute();
            $filtered_tickets = array();
            while ($ticket = $stmt->fetch()) {
                if ($ticket['client'] == Client::getUserId($db, $username)) {
                    array_push($filtered_tickets,$ticket);
                    continue;
                }
                $user = Client::getUser($db, $username);
                if ($user->isAdmin) {
                    array_push($filtered_tickets,$ticket);
                    continue;
                }
                if ($user->isAgent) {
                    foreach ($user->departments as $dep) {
                        if (getDepartmentId($dep['name']) === $ticket['department']) {
                            array_push($filtered_tickets,$ticket);
                        }
                    }
                }
            }
            return $filtered_tickets;
        }

        static function getTicket(PDO $db, int $ticketId) {
            $stmt = $db->prepare('SELECT * FROM Ticket WHERE ticketId=?');
            $stmt->execute(array($ticketId));
        
            $ticket = $stmt->fetch();

            if ($ticket){
                return new Ticket(
                    $ticket['ticketId'],
                    $ticket['title'],
                    $ticket['body'],
                    $ticket['department'],
                    $ticket['hashtags'],
                    $ticket['priority'],
                    $ticket['status'],
                    $ticket['date'],
                    $ticket['client'],
                    $ticket['agent']
                );
            }
        }

        static function getTicketsFiltered(PDO $db, string $search, string $agent, string $department, string $status, string $priority, Client $user) {
            $stmt = $db->prepare('SELECT * FROM (Ticket LEFT JOIN Client ON Ticket.Agent=Client.userId)
                                            WHERE title LIKE ? AND ifnull(username, "") LIKE ?');
            $stmt->execute(array('%' . $search . '%', '%' . $agent . '%'));

            $agent = Client::getUserId($db, $agent);
            if (strlen($priority) === 0) $priority = -1;
            else $priority = intval($priority, 10);

            $tickets = array();

            while ($ticket = $stmt->fetch()) {
                if ((!empty($department) && getDepartment($ticket['department']) !== $department)
                    || ($priority !== -1 && $ticket['priority'] !== $priority) 
                    || (!empty($status) && $ticket['status'] !== $status)) continue;

                    if ($ticket['client'] == Client::getUserId($db, $user->username) || $user->isAdmin) {
                        $tickets[] = new Ticket(
                            $ticket['ticketId'],
                            $ticket['title'],
                            $ticket['body'],
                            $ticket['department'],
                            $ticket['hashtags'],
                            $ticket['priority'],
                            $ticket['status'],
                            $ticket['date'],
                            $ticket['client'],
                            $ticket['agent']
                        );
                        continue;
                    }
                    if ($user->isAgent) {
                        foreach ($user->departments as $dep) {
                            if (getDepartmentId($dep['name']) === $ticket['department']) {
                                $tickets[] = new Ticket(
                                    $ticket['ticketId'],
                                    $ticket['title'],
                                    $ticket['body'],
                                    $ticket['department'],
                                    $ticket['hashtags'],
                                    $ticket['priority'],
                                    $ticket['status'],
                                    $ticket['date'],
                                    $ticket['client'],
                                    $ticket['agent']
                                );
                            }
                        }
                    }
            }

            return $tickets;
        }

        static function getComments(PDO $db, int $ticketId) {
            $stmt = $db->prepare('SELECT * FROM Comment WHERE ticketId=?');
            $stmt->execute(array($ticketId));
        
            return $stmt->fetchAll();
        }

        static function addComment(PDO $db, int $ticketId, string $username, string $text, string $faq) {
            $date = date('Y-m-d');
            $userId = Client::getUserId($db, $username);
            $stmt = $db->prepare('INSERT INTO Comment (ticketId, userId, date, text, faqId) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute(array($ticketId, $userId, $date, $text, getFaqId($faq)));
        }

        static function createTicket(PDO $db, $title, $body, $department, $hashtags, $priority, $client) {
            $stmt = $db->prepare('INSERT INTO Ticket (title, body, department, hashtags, priority, status, date, client) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

            $stmt->execute(array(
                $title,
                $body,
                getDepartmentId($department),
                $hashtags,
                $priority,
                'Open',
                date('Y-m-d'),
                $client
            ));
        }

        static function removeHashtag(PDO $db, int $ticketId, string $hashtag, int $userId) {
            $ticket = Ticket::getTicket($db, $ticketId);

            $user = Client::getUser($db, Client::getUsername($db, $userId));
            if (!($user->isAgent || $user->isAdmin || $userId == $ticket->client)) return null;
            
            $hashtags = $ticket->hashtags;

            $key = array_search($hashtag, $hashtags);
            if ($key === false) return false;

            array_splice($hashtags, $key, 1);

            $hashtags = json_encode($hashtags);

            $stmt = $db->prepare('INSERT INTO Modification (field, old, new, date, ticketID, userId) VALUES 
                ("Hashtag", ?, "", ?, ?, ?)');
            $stmt->execute(array($hashtag, date('Y-m-d'), $ticketId, $userId));

            $stmt = $db->prepare('UPDATE Ticket SET hashtags=? WHERE ticketId=?');
            $stmt->execute(array($hashtags, $ticketId));

            return true;
        }

        static function addHashtag(PDO $db, int $ticketId, string $hashtag, int $userId) {
            $ticket = Ticket::getTicket($db, $ticketId);

            $user = Client::getUser($db, Client::getUsername($db, $userId));
            if (!($user->isAgent || $user->isAdmin || $userId == $ticket->client)) return null;

            $stmt = $db->prepare('SELECT name FROM Hashtag');
            $stmt->execute();
            $global_hashtags = $stmt->fetchAll();
            
            $hashtags = $ticket->hashtags;

            $found = false;
            foreach ($global_hashtags as $global_hashtag){
                if ($global_hashtag['name'] === $hashtag)
                    $found = true;
            }
            if (!$found)
                return false;
            
            foreach ($hashtags as $tag){
                if ($tag === $hashtag) return false;
            }
            
            array_push($hashtags, $hashtag);
            
            $hashtags = json_encode($hashtags);

            $stmt = $db->prepare('INSERT INTO Modification (field, old, new, date, ticketID, userId) VALUES 
                ("Hashtag", "", ?, ?, ?, ?)');
            $stmt->execute(array($hashtag, date('Y-m-d'), $ticketId, $userId));

            $stmt = $db->prepare('UPDATE Ticket SET hashtags=? WHERE ticketId=?');
            $stmt->execute(array($hashtags, $ticketId));

            return true;
        }

        static function changeDepartment(PDO $db, int $ticketId, string $department, int $userId) {
            $user = Client::getUser($db, Client::getUsername($db, $userId));
            if (!($user->isAgent || $user->isAdmin)) return false;

            $departmentId = getDepartmentId($department);

            $stmt = $db->prepare('INSERT INTO Modification (field, old, new, date, ticketID, userId)
                        SELECT ?, ifnull(department, ""), ?, ?, ?, ? FROM Ticket WHERE ticketId=?');
            $stmt->execute(array("Department", $departmentId, date('Y-m-d'), $ticketId, $userId, $ticketId));

            $stmt = $db->prepare('UPDATE Ticket SET department=?, agent=NULL WHERE ticketId=?');
            $stmt->execute(array($departmentId, $ticketId));
        }

        static function changeAgent(PDO $db, int $ticketId, string $agent, int $userId) {
            $user = Client::getUser($db, Client::getUsername($db, $userId));
            if (!($user->isAgent || $user->isAdmin)) return false;

            $stmt = $db->prepare('INSERT INTO Modification (field, old, new, date, ticketID, userId) 
                        SELECT ?, ifnull(agent, ""), ?, ?, ?, ? FROM Ticket WHERE ticketId = ?');
            $stmt->execute(array("Agent", Client::getUserId($db, $agent), date('Y-m-d'), $ticketId, $userId, $ticketId));

            $stmt = $db->prepare('UPDATE Ticket SET agent=(SELECT userId FROM Client WHERE username=?) WHERE ticketId=?');
            $stmt->execute(array($agent, $ticketId));
        }

        static function getAgent(PDO $db, int $ticketId) {
            $stmt = $db->prepare('SELECT username FROM Client WHERE userId = (SELECT agent FROM Ticket WHERE ticketId=?)');
            $stmt->execute(array($ticketId));

            return $stmt->fetch();
        }

        static function updateTicketStatus(PDO $db, int $userId, int $departmentId, $author) {
            $stmt = $db->prepare("SELECT * FROM Ticket WHERE agent = ? AND department = ?");
            $stmt->execute(array($userId, $departmentId));
            while ($ticket = $stmt->fetch()) {
                if ($ticket['status'] != 'Closed') {
                    Ticket::changeStatus($db, $ticket['ticketId'], $ticket['status'], 'Open', $author);
                }
            }
        }

        static function changeStatus(PDO $db, int $ticketId, string $oldStatus, string $newStatus, int $userId) {
            $user = Client::getUser($db, Client::getUsername($db, $userId));
            if (!($user->isAgent || $user->isAdmin)) return false;

            if ($oldStatus === $newStatus) return;
            
            $stmt = $db->prepare('INSERT INTO Modification (field, old, new, date, ticketID, userId) VALUES
                        ("Status", ?, ?, ?, ?, ?)');
            $stmt->execute(array($oldStatus, $newStatus, date('Y-m-d'), $ticketId, $userId));

            $stmt = $db->prepare('UPDATE Ticket SET status=? WHERE ticketId=?');
            $stmt->execute(array($newStatus, $ticketId));

            if ($newStatus == 'Open') {
                $stmt = $db->prepare(" UPDATE Ticket SET agent=NULL, department=NULL WHERE ticketId = ?");
                $stmt->execute(array($ticketId));
            }
        }

        static function getModifications(PDO $db, int $ticketId) {
            $stmt = $db->prepare('SELECT * FROM Modification WHERE ticketId=?');
            $stmt->execute(array($ticketId));

            return $stmt->fetchAll();
        }
    }
?>