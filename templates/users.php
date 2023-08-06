<?php
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/department.php');
require_once(__DIR__ . '/../database/misc.php');

function output_control() { ?>
    <aside>
        <div class="dropdown-button">
            <h2>Dashboard</h2>
        </div>
        <div class = "dropdown-content">
            <button id="activeTicketsBtn">Active Tickets per Day</button>
            <button id="ticketStatusBtn">Ticket Status Distribution</button>
            <button id="userStatsBtn">User Statistics</button>
            <div id="chartContainer"></div>
        </div>

        <div class="dropdown-button">
            <h2>Departments</h2>
        </div>
        <div class = "dropdown-content">
            <ul>
                <?php
                $departments = getDepartments();
                foreach ($departments as $department) { ?>
                    <li><?=$department['name']?></li>
                <?php } ?>
            </ul>

            <div id="add-department">
                <input type="text" placeholder="New department" name="" id="">
                <img src="../images/icons/add.png" alt="add a new department">
            </div>
        </div>

        <div class="dropdown-button">
            <h2>Statuses</h2>
        </div>
        <div class = "dropdown-content">
            <ul>
                <?php
                $statuses = getStatuses();
                foreach ($statuses as $status) { ?>
                    <li><?=$status['name']?></li>
                <?php } ?>
            </ul>

            <div id="add-status">
                <input type="text" placeholder="New status" name="" id="">
                <img src="../images/icons/add.png" alt="add a new status">
            </div>
        </div>

        <div class = "dropdown-button">
            <h2>Hashtags</h2>
        </div>
        <div class = "dropdown-content">
            <ul>
                <?php
                $tags = getHashtags();
                foreach ($tags as $tag) { ?>
                    <li><?=$tag['name']?></li>
                <?php } ?>
            </ul>

            <div id="add-htag">
                <input type="text" placeholder="New hashtag" name="" id="">
                <img src="../images/icons/add.png" alt="add a new hashtag">
            </div>
        </div>
    </aside>
<?php }

function output_users() { ?>

    <section id="form-manage-users">
        <table id="manage-users">
            <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Department</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = getDatabaseConnection();
            $users = Client::getAllUsers($db);
            foreach ($users as $user) {
                output_user($user);
            }
            ?>
            </tbody>
        </table>
    </section>
<?php }

function output_user($user) { ?>
    <tr data-id=<?=$user->username?>>
        <td>
            <div class="user_info">
                <?php
                $db = getDatabaseConnection();
                $filename = Client::getUserId($db, $user->username);
                $results = glob(__DIR__ . "/../images/" . $filename . ".*");
                if ($results){
                    $path = "/../images/" . $filename . "." . pathinfo($results[0], PATHINFO_EXTENSION); ?>
                    <img src=<?=$path?> alt="user_image">
                <?php }
                else{ ?>
                    <img src="/../images/default.jpg" alt="user_image">
                <?php }?>
                <div class="username">
                    <p><?=$user->name?></p>
                    <p>@<?=$user->username?></p>
                </div>
            </div>
        </td>
        <td>
            <select>
                <option value="client">Client</option>
                <option value="agent" <?= (!$user->isAdmin && $user->isAgent) ? 'selected' : '' ?>>Agent</option>
                <option value="admin" <?= $user->isAdmin ? 'selected' : '' ?>>Admin</option>
            </select>
        </td>
        <td>
            <div class="departments">
                <ul>
                    <?php
                    $departmentsUser = $user->departments;
                    foreach ($departmentsUser as $departmentUser) { ?>
                        <li><?=$departmentUser['name']?></li>
                    <?php } ?>
                </ul>

                <select>
                    <option value="unspecified" selected>+</option>
                    <?php
                    $departments = getDepartments();
                    foreach ($departments as $department) { ?>
                        <option><?=$department['name']?></option>
                    <?php } ?>
                </select>
            </div>
        </td>
    </tr>
<?php }
?>