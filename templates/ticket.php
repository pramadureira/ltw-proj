<?php 
    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/ticket.class.php');
    require_once(__DIR__ . '/../database/misc.php');
    require_once(__DIR__ . '/../database/department.php');

    function output_ticket_preview(int $ticket_id) {
        $db = getDatabaseConnection();
        $ticket = Ticket::getTicket($db, $ticket_id); ?>
        <a class="ticketpreview" href="../pages/ticket.php?id=<?=$ticket->ticketId?>">
            <h3><?=htmlentities($ticket->title)?></h3>
            <p><?php 
            if (strlen($ticket->body) > 200)
                $body = substr($ticket->body, 0, 200) . '...';
            else $body = $ticket->body;
            echo htmlentities($body);?></p>
            <div id="tags">
                <ul>
                    <?php $tags = $ticket->hashtags;
                        foreach ($tags as $tag) { ?>
                            <li class="tag"><?=$tag?></li>
                    <?php } ?>
                </ul>
            </div>
            <div id="status">Status: <?=$ticket->status?></div>
            <time datetime="<?=$ticket->date->format('Y-m-d')?>">Date: <?=$ticket->date->format('Y-m-d')?></time>
        </a>
    <?php }

    function output_main_content($username){ 
        $db = getDatabaseConnection();
        ?>
        <section id="tickets">
            <a href="new_ticket.php">Create a new Ticket</a>
            <input id="searchticket" type="text" placeholder="SEARCH">
            <img src="../images/icons/filter.png" alt="filter search option">
            <section id="search_filters">
                <div id="tags">
                    <ul></ul>

                    <input list="hashtags" placeholder="Add more tags">
                    <datalist id="hashtags">
                        <?php 
                            $tags = getHashtags();
                            foreach ($tags as $tag) { ?>
                                <option><?=$tag['name']?></option>
                        <?php } ?>
                    </datalist>
                    <img src="../images/icons/add.png" alt="add a new tag">
                </div>
                <div id="date_filter">
                    <label> Start: 
                        <input type="date" min="1970-01-01" id="start_date">
                    </label>
                    <label> End: 
                        <input type="date" min="1970-01-01" id="end_date">
                    </label>
                </div>
                <?php
                $user = getUserInfo($_SESSION['username']);
                if ($user['isAdmin'] || $user['isAgent']) { ?>
                <label id="agent_filter"> Agent: 
                    <input list="agents" type="text">
                    <datalist id="agents">
                        <?php $agents = getAgents();
                        foreach($agents as $agent) {?>
                            <option><?=$agent['username']?></option>
                        <?php } ?>
                    </datalist>
                </label>
                <?php } ?>
                
                <label id="department_filter"> Department: 
                    <select>
                        <option></option>
                        <?php $departments = getDepartments();
                        foreach($departments as $department) {?>
                            <option><?=$department['name']?></option>
                        <?php } ?>
                    </select>
                </label>
                <label id="status_filter"> Status: 
                    <select>
                        <option></option>
                        <?php $statuses = getStatuses();
                        foreach($statuses as $status) {?>
                            <option><?=$status['name']?></option>
                        <?php } ?>
                    </select>
                </label>
                <label id="priority_filter"> Priority: 
                    <select>
                        <option></option>
                        <option value="0">Low</option>
                        <option value="1">Medium</option>
                        <option value="2">High</option>
                    </select>
                </label>
            </section>
            <section id="previews">
                <?php 
                $tickets = Ticket::getAllTickets($db, $username);
                foreach ($tickets as $ticket) {
                    output_ticket_preview($ticket['ticketId']);
                } ?>
            </section>
        </section>
    <?php }

    function new_ticket_form(){ ?>
        <section class="create_ticket">
            <h2>Create a New Ticket</h2>
            <form method="post" action="../actions/action_create_ticket.php">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <label id="ticket_title">
                    Title
                    <input type="text" name="ticket_title" autocomplete="off">
                    <span class="error"></span>
                </label>
                <label id="department">
                    Department (optional)
                    <select name="department" autocomplete="off">
                        <option value="unspecified" selected> - </option>
                        <?php
                        $departments = getDepartments();
                        foreach ($departments as $department) { ?>
                            <option><?=$department['name']?></option>
                        <?php } ?>
                    </select>
                </label>
                <label id="ticket_priority">
                    Priority
                    <input id="low_priority" name="ticket_priority" type="range" value="0" min="0" max="2" step="1" list="ticket_priority_list" autocomplete="off">
                    <datalist id="ticket_priority_list">
                        <option value="0" label="Low"></option>
                        <option value="1" label="Medium"></option>
                        <option value="2" label="High"></option>
                    </datalist>
                </label>
                <div id="tags">
                    <ul></ul>

                    <input list="hashtags" placeholder="Add more tags">
                    <input type="hidden" value="[]" name="tags">
                    <datalist id="hashtags">
                        <?php
                            $tags = getHashtags();
                            foreach ($tags as $tag) { ?>
                                <option><?=$tag['name']?></option>
                        <?php } ?>
                    </datalist>
                    <img src="../images/icons/add.png" alt="add a new tag">
                </div>
                <label id="ticket_description">
                    Description
                    <textarea name="ticket_description" id="" cols="30" rows="10" placeholder="Write a description of your ticket here..." autocomplete="off"></textarea>
                    <span class="error"></span>
                </label>
                <button>Submit</button>
                <span class="error"><?=$_SESSION['new_ticket_error']?? ''?></span>
            </form>
        </section>
    <?php }
?>