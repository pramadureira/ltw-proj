<?php
    session_start();

    if(!isset($_SESSION['username'])){
        die(header("Location: /pages/login.php"));
    }

    require_once(__DIR__ . '/../templates/common.php');
    require_once(__DIR__ . '/../templates/comments.php');
    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/client.class.php');
    require_once(__DIR__ . '/../database/ticket.class.php');
    require_once(__DIR__ . '/../database/misc.php');
    require_once(__DIR__ . '/../database/department.php');

    output_header(true);

    $db = getDatabaseConnection();
    $ticket = Ticket::getTicket($db, $_GET['id']);

    $show = false;
    $user = Client::getUser($db, $_SESSION['username']);
    if ($user->isAdmin) {
        $show = true;
    }
    if ($ticket->client == Client::getUserId($db, $user->username)) {
        $show = true;
    }
    if ($user->isAgent) {
        foreach ($user->departments as $dep) {
            if (getDepartmentId($dep['name']) === $ticket->department) {
                $show = true;
            }
        }
    }

    if ($ticket === null || !$show) die(header('Location: /'));
?>

<section id="ticket" data-id="<?=$_GET['id']?>">
    <h2><?=htmlentities($ticket->title)?></h2>
    <aside>
        <?php if ($ticket->status != 'Closed') {?>
            <div id="close_ticket">Close Ticket</div>
        <?php } ?>
        <div id="author">
            <?=Client::getUsername($db, $ticket->client)?>
        </div>
        <?php 
        $user = getUserInfo($_SESSION['username']);
        ?>
        <time datetime="<?=$ticket->date->format('Y-m-d')?>"><?=$ticket->date->format('Y-m-d')?></time>
        <div id="department">
            <select>
                <?php
                $departments = [];
                if ($user['isAdmin'] || $user['isAgent']) 
                $departments = getDepartments();
                else if ($ticket->department != "") $departments[] = ['name' => $ticket->department];
                if (empty($departments)) ?> <option disabled selected>choose a department</option>
                <?php foreach ($departments as $department) {
                    if ($department['name'] === $ticket->department) { ?>
                        <option selected><?=$department['name']?></option>
                    <?php } else if ($ticket->status != 'Closed'){?> 
                        <option><?=$department['name']?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>
        <?php
            $department_agents = [];
            ?>
            <div id="agent">
            <select>
                <?php 
                $selected_agent = Ticket::getAgent($db, $_GET['id']);
                if (is_null($selected_agent['username'])) { ?>
                    <option disabled selected>assign an agent</option>
                <?php } else { ?>
                    <option selected><?=$selected_agent['username']?></option>
                <?php }
                if ($user['isAdmin'] || $user['isAgent']) {
                    $department_agents = getDepartmentAgents($ticket->department);
                }
                foreach ($department_agents as $agent) { 
                    if ($ticket->status != 'Closed' && $selected_agent['username'] !== $agent['username']) {?>
                    <option><?=$agent['username']?></option>
                <?php } } ?>
            </select>
        </div>
        <div id="status">
            <select>
                <option><?=$ticket->status?></option>
                <?php if ($ticket->status != 'Closed') {
                    $statuses = [];
                    if ($user['isAdmin'] || $user['isAgent']) {
                        $statuses = getStatuses();
                    }
                    foreach ($statuses as $status) { 
                        if ($status['name'] !== $ticket->status) {?>
                            <option><?=$status['name']?></option>
                <?php }
                    }
                } ?>
            </select>
        </div>
        <div id="tags">
            <ul>
                <?php foreach ($ticket->hashtags as $hashtag) { ?>
                    <li class="tag"><?=$hashtag?></li>
                <?php } ?>
            </ul>

            <?php if ($ticket->status != 'Closed') {?>
                <input list="hashtags" placeholder="Add more tags">
                <datalist id="hashtags">
                    <?php 
                        $tags = getHashtags();
                        foreach ($tags as $tag) { ?>
                            <option><?=$tag['name']?></option>
                    <?php } ?>
                </datalist>
                <img src="../images/icons/add.png" alt="add a new tag">
            <?php } ?>
        </div>
    </aside>
    <article id="ticket_description">
        <p>
            <?=htmlentities($ticket->body)?>
        </p>
    </article>
    <div id="changes_menu">
    <?php $modifications = Ticket::getModifications($db, $_GET['id']); if (sizeof($modifications) > 0) { ?>
        <div id="toggle_show_changes">
            <img src="../images/icons/history.png" alt="changes">
            Show all changes (<span id="change_count"><?=sizeof($modifications)?></span>)
        </div>
        <div id="ticket_changes">
            <ol>
                <?php foreach ($modifications as $modification) { ?>
                    <li>
                        <time datetime="<?=$modification['date']?>"><?=$modification['date']?></time>
                        <strong><?=Client::getUsername($db, $modification['userId'])?></strong>
                        <?php if ($modification['field'] === 'Hashtag') {
                            if ($modification['old'] === ''){ ?>
                                added the tag <strong><?=$modification['new']?></strong>
                            <?php } else if ($modification['new'] === '') { ?>
                                removed the tag <strong><?=$modification['old']?></strong>
                            <?php }
                            } else if ($modification['field'] === 'Agent') { 
                            if ($modification['old'] === '') {?>
                                assigned the ticket to <strong><?=Client::getUsername($db, intval($modification['new'], 10))?></strong>
                            <?php } else { ?>
                                reassigned the ticket from <strong><?=Client::getUsername($db, intval($modification['old'], 10))?></strong>
                                    to <strong><?=Client::getUsername($db, intval($modification['new'],10))?></strong>
                            <?php } ?> 
                        <?php } else if ($modification['field'] === 'Department') {
                            if ($modification['old'] === '') {?>
                                changed the department to <strong><?=getDepartment($modification['new'])?></strong>
                            <?php } else { ?>
                                changed the department from <strong><?=getDepartment($modification['old'])?></strong>
                                    to <strong><?=getDepartment($modification['new'])?></strong>
                            <?php } ?> 
                        <?php } else if ($modification['field'] === 'Status') { ?>
                                changed the ticket from <strong><?=$modification['old']?></strong> to
                                    <strong><?=$modification['new']?></strong>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ol>
        </div>
    <?php } ?>
    </div>
    <?php output_comments($ticket->ticketId, $ticket->status); ?>
</section>


<?php
    output_footer();
?>