<?php
/**
 * (c) 2017 Technische Universität Berlin
 *
 * This software is licensed under GNU General Public License version 3 or later.
 *
 * For the full copyright and license information,
 * please see https://www.gnu.org/licenses/gpl-3.0.html or read
 * the LICENSE.txt file that was distributed with this source code.
 */
?>
<?php
/**
 * Displays the processing return value.
 */
?>

<div class="container content-card top-buffer">
    <?php if ($processor->returnOk($processingReturnValue)) {
        // If there is a processed file, offer it to download
        if (!empty($_SESSION['processedFile']) && file_exists($_SESSION['processedFile'])) {
            include("elements/download.php");
        }
        } else { ?>
        <div class="message-container message-danger">
            <i class="bi bi-x-circle-fill"></i>
            <?= $messages['failMessage'] ?>
        </div>
    <?php } ?>

    <form method="POST" action="index.php" class="top-buffer">
        <p class="fw-bold"><?= $messages['deleteMessage'] ?></p>
        <button type="submit" class="btn btn-tuni" name="delete_file" value="<?= $messages['deleteButton'] ?>">
            <?= $messages['deleteButton'] ?>
        </button>
    </form>
</div>
