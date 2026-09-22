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
 * Functions for LDAP-Login by tubIT.
 *
 * Implementation by Clemens Zimmermann, tubIT.
 * Adaption to onIT code style by Per Broman.
 */

class SessionControl {
    /**
     * Deletes the uploaded and processed files and clears the session.
     */
    public function clearSession(): void
    {
        if (!empty($_SESSION['uploadFile'])) {
            if (file_exists($_SESSION['uploadFile'])) {
                unlink($_SESSION['uploadFile']);
            }
        }

        if (!empty($_SESSION['processedFile'])) {
            if (file_exists($_SESSION['processedFile'])) {
                unlink($_SESSION['processedFile']);
            }
        }

        if (!empty($_SESSION['xmpFile'])) {
            if (file_exists($_SESSION['xmpFile'])) {
                unlink($_SESSION['xmpFile']);
            }
        }

        unset($_SESSION['uploadFile']);
        unset($_SESSION['processedFile']);
        unset($_SESSION['xmpFile']);
    }

    /**
     * Destroys the session.
     */
    public function destroySession(): void {
        $this->clearSession();
        session_destroy();
    }
}
