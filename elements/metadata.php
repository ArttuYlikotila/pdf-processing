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
 * Input fields for additional metadata.
 */
?>
<div class="info-container mb-3">
    <?= $messages['pdfaMetadataMessage'] ?>
</div>

<?php foreach ($configs['metadataField'] as $field): ?>
    <div class="row top-buffer">
        <label for="<?= $field ?>" class="col-form-label col-md-4 col-lg-3">
            <?= $messages[$field . 'Label'] ?>
        </label>
        <div class="col-md-8 col-lg-9 pb-3">
            <?php if ($field !== 'description'): ?>
                <input id="<?= $field ?>" name="<?= $field ?>" type="text" class="form-control" />
            <?php else: ?>
                <textarea
                    id="<?= $field ?>"
                    name="<?= $field ?>"
                    type="text"
                    class="form-control"
                    maxlength="<?= $configs['descriptionMaxLenght'] ?>"
                    rows="3"></textarea>
                <p class="char-counter">0/2000</p>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
