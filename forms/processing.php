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
<form method="post" action="index.php" id="processing-form">
        <div class="container content-card">
                <div class="row">
                        <div class="col-sm-9">
                <p><?php echo htmlspecialchars($messages['uploadedFile'], ENT_QUOTES, 'UTF-8') . ' <strong>' . htmlspecialchars($_SESSION['originalFileName'], ENT_QUOTES, 'UTF-8') . '</strong>' ?></p>
            </div>
        </div>
<?php if (!$simplified_conversion):?>
        <div class="row top-buffer">
            <div class="col-sm-3"><?php echo($messages['pdfaValidateMessage']) ?></div>
            <div class="col-sm-3">
<?php
            createSelectBox('pdfa_level', $messages['pdfaLevel']);
?>
            </div>
            <div class="col-sm-3">
                <input type="submit" class="btn btn-success" name="pdfa_validate"
                        value="<?php echo htmlspecialchars($messages['validateButton'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
<?php endif; ?>

<?php include 'elements/metadata.php'; ?>
    <div class="row top-buffer">
        <div class="col-sm-3"><?php echo($messages['pdfaConvertMessage']) ?></div>
            <div class="col-sm-1">
            <?php
                   createSelectBox('pdfa_convlevel', $messages['pdfaLevel'], true);
            ?>
            </div>
            <div class="col-sm-2">
        <?php
            createSelectBox('pdfa_mode', $messages['pdfaModus'], true);
        ?>
            </div>
                        <div class="col-sm-3">
                                <input type="submit" class="btn btn-info" id="pdfa-convert-button" name="pdfa_convert"
                                                value="<?php echo htmlspecialchars($messages['convertButton'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
        </div>

        <div class="row top-buffer">
                <div class="col-sm-12">
                        <div id="conversion-status" class="alert alert-info conversion-hidden" role="alert"
                                data-in-progress="<?php echo htmlspecialchars($messages['conversionInProgress'], ENT_QUOTES, 'UTF-8') ?>"
                                data-success="<?php echo htmlspecialchars($messages['conversionSuccess'], ENT_QUOTES, 'UTF-8') ?>"
                                data-failed="<?php echo htmlspecialchars($messages['conversionFailed'], ENT_QUOTES, 'UTF-8') ?>">
                                <span id="conversion-status-icon" class="glyphicon glyphicon-refresh conversion-spinner" aria-hidden="true"></span>
                                <span id="conversion-status-text"><?php echo htmlspecialchars($messages['conversionIdle'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div id="conversion-result" class="conversion-hidden"
                                data-download-label="<?php echo htmlspecialchars($messages['downloadButton'], ENT_QUOTES, 'UTF-8') ?>"
                                data-ready-label="<?php echo htmlspecialchars($messages['downloadLabel'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <pre id="conversion-details" class="conversion-hidden"></pre>
                </div>
        </div>

                <div class="row top-buffer">
                        <div class="col-sm-6">
                <p><strong><?php echo($messages['deleteMessage']) ?></strong></p>
            </div>
            <div class="col-sm-3">
                <input type="submit" class="btn btn-primary" name="delete_file"
                        value="<?php echo htmlspecialchars($messages['deleteButton'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

    </div>
</form>
