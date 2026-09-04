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
 * Displays info and error messages.
 */

if (!empty($errorMessage)) {
?>
<div class="container content-card">
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
</div>

<?php
}

if (!empty($infoMessage)) {
?>
<div class="container content-card">
    <div class="alert alert-info">
        <?php echo htmlspecialchars($infoMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
</div>

<?php
}
