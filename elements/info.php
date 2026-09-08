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
    <div class="row">
        <div class="col-sm-12">
        <?php if ($processor->returnOk($processingReturnValue)) {
            // If there is a processed file, offer it to download
            if (!empty($_SESSION['processedFile']) && file_exists($_SESSION['processedFile'])) {
                include("elements/download.php");
            }
            } else { ?>
            <!-- TODO: this should not be a link or a button, instead should be message container of some kind-->
            <a href="#" class="btn btn-danger btn-lg">
                <i class="bi bi-x-circle-fill"></i>
                <?php echo $messages['failMessage'] ?>
            </a>
        <?php } ?>
        </div>
    </div>

    <div class="row top-buffer">
        <p class="col-sm-6 fw-bold"><?php echo($messages['deleteMessage']) ?></p>
        <div class="col-sm-3">
            <input
                type="submit"
                class="btn btn-primary"
                name="delete_file"
                value="<?php echo $messages['deleteButton'] ?>"
            />
        </div>
    </div>
</div>
