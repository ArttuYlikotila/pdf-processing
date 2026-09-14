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
    <h1><?php echo $messages['headline'] ?></h1>
    <form method="post" action="index.php" id="processing-form">
        <p>
            <?php echo $messages['uploadedFile'] ?>
            <span class="fw-bold">
                <?php echo htmlspecialchars($_SESSION['originalFileName'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </p>

        <?php include 'elements/metadata.php'; ?>

        <div class="row top-buffer control-buttons">
            <div class="col-md-4 col-lg-3">
                <?php echo($messages['pdfaConvertMessage']) ?>
            </div>
            <div class="col-2">
                <button
                    type="submit"
                    class="btn btn-info"
                    id="pdfa-convert-button"
                    name="pdfa_convert"
                    data-in-progress="<?php echo $messages['conversionInProgress'] ?>"
                    data-text-content="<?php echo $messages['convertButton'] ?>"
                >
                    <?php echo $messages['convertButton'] ?>
                </button>
            </div>

            <div class="col-md-4 col-lg-3">
                <p class="fw-bold"><?php echo($messages['deleteMessage']) ?></p>
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-tuni" name="delete_file" value="<?php echo $messages['deleteButton'] ?>">
                    <?php echo $messages['deleteButton'] ?>
                </button>
            </div>
        </div>

        <div class="conversion-info mt-3">
            <div
                id="conversion-status"
                class="message-container message-success conversion-hidden"
                role="alert"
                data-in-progress="<?php echo $messages['conversionInProgress'] ?>"
                data-success="<?php echo $messages['conversionSuccess'] ?>"
                data-failed="<?php echo $messages['conversionFailed'] ?>"
            >
                <i id="conversion-status-icon" class="bi bi-arrow-repeat conversion-spinner" aria-hidden="true"></i>
                <span id="conversion-status-text">
                    <?php echo $messages['conversionIdle'] ?>
                </span>
            </div>

            <div
                id="conversion-result"
                class="message-container message-success conversion-hidden"
                data-download-label="<?php echo $messages['downloadButton'] ?>"
                data-ready-label="<?php echo $messages['downloadLabel'] ?>"
            >
            </div>
            <pre id="conversion-details" class="conversion-hidden"></pre>
        </div>
    </form>
</div>
