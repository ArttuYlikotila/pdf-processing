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
 * The main entry building the page together.
 *
 */

    include_once("environment/init.php");
    include_once("environment/functions.php");

    // The handler manages the data from the http request
    include_once("environment/handler.php");

    include_once("elements/header.php");
    include_once("elements/alerts.php");

    // If there is no target file, show upload form
    if (empty($_SESSION['uploadFile']) || !file_exists($_SESSION['uploadFile'])) {
        include_once("forms/upload.php");
    } else {
        include_once("forms/processing.php");
    }

    // If there are arguments of a processing return value, show them
    if (!empty($processingReturnValue) || !empty($args)) {
        include_once("elements/info.php");
    }

    include_once("elements/footer.php");
