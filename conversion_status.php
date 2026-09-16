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
 * Returns the current conversion status so the browser can react immediately when the
 * converted file is ready.
 */

include_once("environment/init.php");

header('Content-Type: application/json; charset=utf-8');

$response = [
    'status' => 'processing',
    'message' => $messages['conversionInProgress'] ?? 'Conversion is still running.',
];

if (empty($_SESSION['uploadFile']) || !file_exists($_SESSION['uploadFile'])) {
    $response['status'] = 'error';
    $response['message'] = $messages['fileNotFound'] ?? 'Uploaded file not found.';
    echo json_encode($response);
    exit;
}

// If the lock file exists, inspect its status.
$lockFile = $processor->getLockFilePath($_SESSION['uploadFile']);
if (!empty($lockFile) && file_exists($lockFile)) {
    $lockStatus = trim((string) file_get_contents($lockFile));

    if ($lockStatus === 'running') {
        echo json_encode($response);
        exit;
    }

    if ($lockStatus === 'failed') {
        $response['status'] = 'error';
        $response['message'] = $messages['conversionFailed'] ?? 'Conversion failed.';
        echo json_encode($response);
        exit;
    }
}

if (!empty($_SESSION['processedFile']) && file_exists($_SESSION['processedFile'])) {
    $response['status'] = 'success';
    $response['message'] = $messages['conversionSuccess'] ?? 'Conversion finished.';
    $response['downloadUrl'] = 'stream.php';
    $response['displayName'] = $_SESSION['processedDisplayName'] ?? basename($_SESSION['processedFile']);
}

echo json_encode($response);
exit;
