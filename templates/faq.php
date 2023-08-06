<?php

require_once(__DIR__ . '/../database/misc.php');
function output_FAQ($faq) { ?>
    <div class="question" data-faq-id=<?=$faq['faqId']?>>
        <div class="dropdown-button">
            <h3><?=$faq['question']?></h3>
            <span class="open">+</span>
        </div>
        <div class="dropdown-content">
            <p><?= $faq['answer']?></p>
            <?php $user = getUserInfo($_SESSION['username']);
            if ($user['isAdmin'] || $user['isAgent']) { ?>
                <div class="delete-faq">
                    <a href="../actions/action_delete_faq.php?faq_id=<?= $faq['faqId'] ?>">Delete</a>
                </div>
            <?php } ?>
        </div>
    </div>
<?php }
?>

<?php function output_FAQs() { ?>
    <section id="questions">
        <?php $user = getUserInfo($_SESSION['username']);
        if ($user['isAdmin'] || $user['isAgent']) { ?>
            <a href="new_faq.php">Create a new FAQ</a>
        <?php }

        $faqs = getFAQs();
        foreach ($faqs as $faq) {
            output_FAQ($faq);
        } ?>
    </section>
<?php }

function new_faq_form(){ ?>
    <section class="create_faq">
        <h2>Create a New FAQ</h2>
        <form method="post" action="../actions/action_create_faq.php">
            <label id="faq_question">
                Question
                <input type="text" name="faq_question" autocomplete="off">
                <span class="error"></span>
            </label>
            <label id="faq_answer">
                Answer
                <textarea name="faq_answer" id="" cols="30" rows="10" autocomplete="off"></textarea>
                <span class="error"></span>
            </label>
            <button>Submit</button>
            <span class="error"><?=$_SESSION['new_faq_error']?? ''?></span>
        </form>
    </section>
<?php }?>