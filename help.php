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
 * Prints a static help page.
 */

include_once("environment/init.php");
include_once("elements/header.php");

?>
<div class="container content-card">
    <h1><?php echo $messages['headline'] ?></h1>
    <p><?php echo($messages['helpFileBrowse']) ?></p>

    <div class="info-container mb-3">
        <?php echo($messages['helpIntroAlert']) ?>
    </div>

    <p><?php echo($messages['helpMetadataIntro']) ?></p>
    <p><?php echo($messages['helpMetadataPurpose']) ?></p>
    <p><?php echo($messages['helpMetadataProcess']) ?></p>
    <p><?php echo($messages['helpConversionReview']) ?></p>
    <p><?php echo($messages['helpRememberToDelete']) ?></p>
</div>
<?php

include_once("elements/footer.php");
