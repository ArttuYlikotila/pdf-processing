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
 * Form for the file upload.
 */
?>
<form method="post" action="index.php" enctype="multipart/form-data">
    <div class="container content-card">
        <p class="mb-3"><?php echo $messages['introduction'] ?></p>

        <div class="mb-1">
            <label class="form-label" for="fileToUpload">
                <?php echo $messages['selectFile'] ?>
            </label>
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" accept=".pdf" />
        </div>

        <button type="submit" class="btn btn-tuni mt-2" name="submit">
            <?php echo $messages['uploadFile'] ?>
        </button>
    </div>
</form>
