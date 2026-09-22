<?php
/**
 * (c) 2017 Technische Universität Berlin
 * (c) 2025-2026 Tampere University
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
<div class="container content-card">
    <h1><?= $messages['headline'] ?></h1>
    <form method="post" action="index.php" enctype="multipart/form-data">
        <p class="mb-3"><?= $messages['introduction'] ?></p>

        <div class="file-upload">
            <label
                for="fileToUpload"
                data-file-not-pdf="<?= $messages['uploadNoPdf'] ?>"
                data-uploaded-file="<?= $messages['uploadedFile'] ?>"
            >
                <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                <?= $messages['selectFile'] ?>
            </label>
            <input type="file" name="fileToUpload" id="fileToUpload" accept=".pdf" />
        </div>

        <button type="submit" id="start-conversion-btn" class="btn btn-tuni hidden" name="submit">
            <?= $messages['uploadFile'] ?>
        </button>
    </form>
</div>
