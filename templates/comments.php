<?php

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/ticket.class.php');
require_once(__DIR__ . '/../database/misc.php');

function output_comments($id, $status){ 
    $db = getDatabaseConnection();
    $comments = Ticket::getComments($db, $id); ?>
    <section id="comments">
        <?php foreach ($comments as $comment) { ?>
            <article class="comment">
                <?php 
                $db = getDatabaseConnection();
                $filename = $comment['userId'];
                $results = glob(__DIR__ . "/../images/" . $filename . ".*");
                if ($results){
                    $path = "/../images/" . $filename . "." . pathinfo($results[0], PATHINFO_EXTENSION); ?>
                    <img src=<?=$path?> alt="user_image">
                <?php }
                else{ ?>
                    <img src="/../images/default.jpg" alt="user_image">
                <?php }?>
                <span class="comment_username"><?=Client::getUsername($db, $comment['userId'])?></span>
                <time datetime="<?=$comment['date']?>"><?=$comment['date']?></time>
                <p>
                    <?=htmlentities($comment['text'])?>
                </p>
                <?php if ($comment['faqId'] != null) { ?>
                    <a href="faq.php#<?=$comment['faqId']?>""> FAQ - <?=getFaqQuestion($comment['faqId'])?> </a>
                <?php } ?>
            </article>
        <?php }  if ($status != 'Closed') { ?>
    <div> </div>
    <form method="post" action="../actions/action_comment.php">
        <h3>Leave a comment</h3>
        <input type="hidden" name="ticketId" value="<?=$id?>"/>
        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
        <textarea name="text" placeholder="Your thoughts here..."></textarea>
        <span class="error"></span>
        <?php
        $user = getUserInfo($_SESSION['username']);
        if ($user['isAgent']) { ?>
        <label id="faq">
            <select name="faq" autocomplete="off">
                <option value="none" selected> Refer to a FAQ (optional) </option>
                <?php
                $faqs = getFAQs();
                foreach ($faqs as $faq) { ?>
                    <option><?=$faq['question']?></option>
                <?php } ?>
            </select>
        </label>
        <?php } ?>
        <button type="submit">Reply</button>
    </form>
        <?php } ?>
    </section>

<?php } ?>