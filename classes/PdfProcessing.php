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
 * This class contains all functions needed for the pdf processing interface.
 * On construction it loads the configurations from the config.ini file.
 */
class PdfProcessing
{
    /**
     * The array with configurations.
     */
    private $configs = NULL;

    /**
     * The array with messages.
     */
    private $messages = NULL;

    /**
     * Contructor loading the configuration.
     */
    function __construct ($configs, $messages)
    {
        $this->configs = $configs;
        $this->messages = $messages;
    }

    /**
     * Renames a file for security reasons.
     *
     * @param string $filename
     * @return string
     */
    public function renameFile($filename, $fileExt)
    {
        $cleanExt = preg_replace('/[^a-zA-Z0-9.]/', '', $fileExt);  // Basic sanitization
        $hashAlgo = $this->configs['hash'];

        if (!is_string($hashAlgo) || !in_array($hashAlgo, hash_algos(), true)) {
            error_log("Invalid hash algorithm '{$hashAlgo}' configured. Falling back to sha256.");
            $hashAlgo = 'sha256';
        }

        return hash($hashAlgo, $filename . microtime(true)) . $cleanExt;
    }

    /**
     * Saves a file and stores the filename and the original name in the
     * session.
     *
     * @param File $file
     * @param string $saveFileName
     * @return boolean
     */
    public function saveFile($file, $saveFileName)
    {
        if (!file_exists($this->configs['uploadPath'])) {
            mkdir($this->configs['uploadPath'], 0755, true);
        }

        $targetFile = realpath($this->configs['uploadPath']) . DIRECTORY_SEPARATOR . basename($saveFileName);

        // Check MIME type.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if ($mimeType !== 'application/pdf') {
            error_log("Rejected file due to invalid MIME type: $mimeType");
            return false;
        }

        // Validate the PDF magic bytes. A PDF file must start with "%PDF-", eg. hex 25 50 44 46 2d .
        $handle = fopen($file['tmp_name'], 'rb');
        if ($handle === false) {
            error_log('Rejected file because the upload could not be opened for signature validation.');
            return false;
        }

        $magicBytes = fread($handle, 5);
        fclose($handle);

        if ($magicBytes !== '%PDF-') {
            error_log('Rejected file due to invalid PDF signature.');
            return false;
        }

        // Validate file extension if necessary

        if (file_exists($targetFile)) {
            error_log("File already exists: $targetFile");
        } elseif (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $_SESSION['uploadFile'] = $targetFile;
            $_SESSION['originalFileName'] = htmlentities(basename($file['name']));
            return true;
        }

        return false;
    }

    /**
     * Creates name and display name of the processed file and saves them to the session.
     *
     * @param string $fileExt - the file extension
     */
    public function createAndSaveProcessedFileName($fileExt)
    {
        if (!file_exists($this->configs['processedPath'])) {
            mkdir($this->configs['processedPath'], 0755, true);
        }

        $_SESSION['processedFile'] = $this->configs['processedPath']
            . $this->addFileSuffix($_SESSION['uploadFile'], $fileExt, '_processed');

        $_SESSION['processedDisplayName'] = $this->addFileSuffix($_SESSION['originalFileName'],
            $fileExt, '_processed');
    }

    /**
     * Saves the given content as an xmp file.
     *
     * @param string $content
     */
    public function saveXmpFile($content)
    {
        if (!file_exists($this->configs['xmpPath'])) {
            mkdir($this->configs['xmpPath'], 0755, true);
        }

        $xmpPath = $this->configs['xmpPath'] . basename($_SESSION['uploadFile'], '.pdf') . '.xmp';

        if (file_put_contents($xmpPath, $content)) {
            $_SESSION['xmpFile'] = $xmpPath;
        } else {
            $errorMessage = $this->messages['xmpFileNotSaved'];
            error_log("The .xmp file could not be saved!");
        }
    }

    /**
     * Adds a suffix to the file name keeping the file extension.
     *
     * @param string $filename
     * @param string $fileExt
     * @param string $suffix
     * @return string
     */
    private function addFileSuffix($filename, $fileExt, $suffix)
    {
        return basename($filename, $fileExt) . $suffix . $fileExt;
    }

    /**
     * Creates an associative array from metadata fields in a the post.
     *
     * @return string[]
     */
    public function createMetadataArray()
    {
        $metadataArray = [];

        foreach ($this->configs['metadataField'] as $field) {
            // Only allow alphanumeric fields to prevent injection
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
                continue;
            }

            if (isset($_POST[$field])) {
                $value = trim($_POST[$field]);

                if (!empty($value)) {
                    $metadataArray[$field] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }
        }

        return $metadataArray;
    }

    /**
     * Creates the arguments for PDF/A conversion.
     *
     * @param string $level - the compliancy level
     * @param string $mode - the conversion mode
     * @param string[] $metadataArray - additional metadata
     * @return string - the arguments
     */
    public function createPdfaArgs($level, $mode, $lang)
    {
        $args = [];

        if (!empty($mode)) {
            $args[] = $mode;
        }

        if (!empty($_SESSION['xmpFile'])) {
            $args[] = $this->configs['metadataArg'] . $_SESSION['xmpFile'];
        }

        $args[] = $this->configs['pdfLevelArg'] . $level;
        $args[] = $this->configs['pdfOutputArg'] . $_SESSION['processedFile'];
        $args[] = $this->configs['pdfOverwriteArg'];
        $args[] = $this->configs['cachefolderArg'];
        $args[] = $this->configs['pdfLangArg'] . $lang;
        $args[] = $_SESSION['uploadFile'];
        $args[] = $this->configs['pdfForceConversion'];

        // Filter out any accidental empty strings
        return array_filter($args, fn($v) => trim($v) !== '');
    }

    /**
     * Creates the arguments for PDF/A validation.
     *
     * @param string $level - the compliancy level
     * @return string - the arguments
     */
    public function createPdfaValidateArgs($level, $lang)
    {
        $args = ' --analyze '
        . $this->configs['pdfLevelArg'] . $level . ' '
        . $this->configs['cachefolderArg'] . ' '
        . $this->configs['pdfLangArg'] . $lang . ' '
        . $_SESSION['uploadFile'];

        return $args;
    }

    /**
     * Creates the arguments for PDF profile processing.
     *
     * @param string $profile - the profile file name
     * @return string - the arguments
     */
    public function createPdfProfileArgs($profile, $lang)
    {
        $args = $this->configs['pdfProfileArg'] . ' '
            . $this->configs['pdfProfilesPath'] . escapeshellarg($profile) . ' '
            . $_SESSION['uploadFile'] . ' ' . $this->configs['pdfOutputArg']
            . $_SESSION['processedFile'] . ' ' . $this->configs['pdfOverwriteArg'] . ' '
            . $this->configs['pdfLangArg'] . $lang . ' '
            . $this->configs['cachefolderArg'];

        return $args;
    }

    /**
     * Creates the arguments for free PDF processing.
     *
     * @param string $args - the free args
     * @return string - the arguments
     */
    public function createPdfFreeArgs($freeArgs, $lang)
    {
        $args = escapeshellcmd($freeArgs) . ' ' . $this->configs['pdfOutputArg']
            . $_SESSION['processedFile'] . ' ' . $this->configs['pdfOverwriteArg'] . ' '
            . $this->configs['cachefolderArg'] . ' '
            . $this->configs['pdfLangArg'] . $lang . ' '
            . $_SESSION['uploadFile'];

        return $args;
    }

    /**
     * Returns the full path to the lock file for the given upload file.
     *
     * @param string $uploadFile
     * @return string|null
     */
    public function getLockFilePath($uploadFile)
    {
        if (empty($uploadFile)) {
            return null;
        }

        $directory = dirname($uploadFile);
        $fileName = basename($uploadFile);

        if (empty($directory) || empty($fileName)) {
            return null;
        }

        return $directory . DIRECTORY_SEPARATOR . $fileName . '.lock';
    }

    /**
     * Creates a lock file next to the upload file to signal an ongoing process.
     *
     * @param string $uploadFile
     * @return string|null - the created lock file path or null on failure
     */
    public function createLockFile($uploadFile)
    {
        $lockFile = $this->getLockFilePath($uploadFile);

        if (empty($lockFile)) {
            return null;
        }

        $lockDir = dirname($lockFile);

        if (!is_dir($lockDir) || !is_writable($lockDir)) {
            error_log("Cannot create lock file: directory '$lockDir' is not writable.");
            return null;
        }

        if ($this->writeLockFileStatus($lockFile, 'running') === false) {
            return null;
        }

        return $lockFile;
    }

    /**
     * Writes the given status into the lock file.
     *
     * @param string $lockFile
     * @param string $status
     * @return bool
     */
    public function writeLockFileStatus($lockFile, $status)
    {
        if (empty($lockFile)) {
            return false;
        }

        if (file_put_contents($lockFile, $status) === false) {
            error_log("Failed to write status '$status' to lock file at '$lockFile'.");
            return false;
        }

        return true;
    }

    /**
     * Removes a previously created lock file.
     *
     * @param string|null $lockFile
     */
    public function removeLockFile($lockFile)
    {
        if (!empty($lockFile) && file_exists($lockFile)) {
            unlink($lockFile);
        }
    }

    /**
     * Executes the pdf processor with the given arguments.
     *
     * @param string $args
     * @return string - the pdf processor return value
     */
    public function executePdfProcessing($args)
    {
        $command = array_merge(
            [$this->configs['pdfProcessor']],
            $args
        );

        error_log("Executing: " . implode(' ', array_map('escapeshellarg', $command)));

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"],
        ];

        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            fclose($pipes[0]);

            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $exitCode = proc_close($process);
            # All return codes below 100 indicate a successful operation.
            # https://hilfe.callassoftware.com/m/pdfapilot/l/652813-results-return-codes-error-codes-reason-codes
            if ($exitCode > 100) {
                error_log("PDF processor error (code $exitCode): " . trim($stderr));
            }

            return $stdout;
        } else {
            error_log("Failed to start PDF processor");
            return false;
        }
    }

    /**
     * Returns the pdf profile in the profiles directory.
     *
     * @return array
     */
    public function getPdfProfiles()
    {
        $profileDir = $this->configs['pdfProfilesPath'];
        if (!is_dir($profileDir)) {
            error_log("The pdf profiles path in the config.ini '$profileDir' is not a valid directory");
            return array();
        }

        $profiles = scandir($profileDir);
        $cleanedProfiles = array();

        foreach ($profiles as $val) {
            if ($val != '.' && $val != '..') {
                array_push($cleanedProfiles, $val);
            }
        }

        return $cleanedProfiles;
    }

    /**
     * Writes a download file in the output buffer.
     *
     * @param string $path
     * @param string $mimeType
     * @param string $displayName
     */
    public function downloadFile($path, $mimeType, $displayName)
    {
        if (!file_exists($path) || !is_readable($path)) {
            error_log("Attempted to download missing or unreadable file: $path");
            http_response_code(404);
            exit("File not found");
        }

        $displayName = basename($displayName);  // Avoid header injection
        $filesize = filesize($path);

        header("Content-Type: $mimeType");
        header("Content-Disposition: attachment; filename=\"$displayName\"");
        header("Content-Length: $filesize");

        readfile($path);
    }

    /**
     * Filters a string line by line.
     *
     * @param string $returnValue
     * @return string - the filtered string
     */
    public function filterReturnValue($returnValue)
    {
        $filteredValue = '';
        $lines = explode(PHP_EOL, $returnValue);

        foreach ($lines as $line) {
            if (preg_match($this->configs['lineRegex'], $line)) {
                $filteredValue .= $line . PHP_EOL;
            }
        }

        return $filteredValue;
    }

    /**
     * Checks if their were no errors in the return summary.
     *
     * @param string $returnValue
     * @return boolean
     */
    public function returnOk($returnValue)
    {
        $lines = explode(PHP_EOL, $returnValue);
        $value = "nothing found";

        foreach ($lines as $line) {
            if (preg_match($this->configs['summaryRegex'], $line)) {
                $value = preg_replace($this->configs['summaryRegex'], '$1', $line);
            }
        }

        return strcmp("0", $value) == 0;
    }
}
