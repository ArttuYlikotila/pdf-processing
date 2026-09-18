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
 * Form for the pdf processing settings.
 */
$simplified_conversion = $configs['simplified_conversion'];

if ($simplified_conversion) {
    $messages['pdfaLevel'] = array("2b");
    $messages['pdfaModus'] = array(" ,Vakio");
}

?>
<div class="container content-card">
    <h1><?= $messages['headline'] ?></h1>
    <form method="post" action="index.php" id="processing-form">
        <p>
            <?= $messages['uploadedFile'] ?>
            <span class="fw-bold">
                <?php echo htmlspecialchars($_SESSION['originalFileName'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </p>

        <?php include 'elements/metadata.php'; ?>

        <div class="control-buttons">
            <button
                type="submit"
                class="btn btn-tuni"
                id="pdfa-convert-button"
                name="pdfa_convert"
                data-in-progress="<?= $messages['conversionInProgress'] ?>"
                data-text-content="<?= $messages['convertButton'] ?>"
            >
                <?= $messages['convertButton'] ?>
            </button>

            <button
                type="submit"
                class="btn btn-danger"
                name="delete_file"
                value="<?= $messages['deleteButton'] ?>"
            >
                <?= $messages['deleteButton'] ?>
            </button>
        </div>

        <div class="conversion-info mt-3">
            <div
                id="conversion-status"
                class="message-container message-success hidden"
                role="alert"
                data-in-progress="<?= $messages['conversionInProgress'] ?>"
                data-success="<?= $messages['conversionSuccess'] ?>"
                data-failed="<?= $messages['conversionFailed'] ?>"
            >
                <i id="conversion-status-icon" class="bi bi-arrow-repeat conversion-spinner" aria-hidden="true"></i>
                <span id="conversion-status-text">
                    <?= $messages['conversionIdle'] ?>
                </span>
            </div>

            <div
                id="conversion-result"
                class="message-container message-success hidden"
                data-download-label="<?= $messages['downloadButton'] ?>"
                data-ready-label="<?= $messages['downloadLabel'] ?>"
            >
            </div>
        </div>
    </form>
</div>
