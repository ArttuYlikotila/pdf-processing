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

        <div class="row">
            <label class="col-sm-3" for="fileToUpload">
                <?php echo $messages['selectFile'] ?>
            </label>
            <input type="file" name="fileToUpload" id="fileToUpload" class="col-sm-9" />
        </div>

        <input
            type="submit"
            class="btn btn-primary mt-2"
            value="<?php echo htmlspecialchars($messages['uploadFile'], ENT_QUOTES, 'UTF-8') ?>"
            name="submit"
        />
    </div>
</form>
