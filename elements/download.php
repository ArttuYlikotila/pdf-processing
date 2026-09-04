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
 * Offers the processed file to download.
 */
?>

    <div class="row top-buffer">
        <div class="col-sm-6">
            <p><?php echo htmlspecialchars($messages['downloadLabel'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="col-sm-3">
                <a href="stream.php" class="btn btn-info">
                    <span class="glyphicon glyphicon-download"></span>
                    <?php echo htmlspecialchars($messages['downloadButton'], ENT_QUOTES, 'UTF-8') ?>
                </a>
        </div>

    </div>
