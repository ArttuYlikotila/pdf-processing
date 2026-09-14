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
 * Input fields for additional metadata.
 */
?>
<div>
    <div class="info-text mb-3">
        <?php echo $messages['pdfaMetadataMessage'] ?>
    </div>
</div>

<?php foreach ($configs['metadataField'] as $field) { ?>
<div class="row top-buffer">
    <label for="<?php echo $field ?>" class="col-form-label col-md-4 col-lg-3">
        <?php echo $messages[$field . 'Label'] ?>
    </label>
    <div class="col-md-8 col-lg-9 pb-3">
        <?php if ($field !== 'description'): ?>
            <input id="<?php echo $field ?>" name="<?php echo $field ?>" type="text" class="form-control" />
        <?php else: ?>
            <textarea id="<?php echo $field ?>" name="<?php echo $field ?>" type="text" class="form-control"></textarea>
        <?php endif; ?>
    </div>
</div>
<?php } ?>
