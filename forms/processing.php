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

        <div class="control-buttons" data-ready-label="<?= $messages['downloadLabel'] ?>">
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
                class="btn btn-tuni"
                name="delete_file"
                value="<?= $messages['deleteButton'] ?>"
            >
                <?= $messages['deleteButton'] ?>
            </button>
        </div>
    </form>
</div>
