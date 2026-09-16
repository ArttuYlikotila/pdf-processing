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
 * Side effect functions for use in php elements.
 */

/**
 * Creates a select box.
 *
 * @param $id - the id of the select element.
 * @param $messageArray - an array of options.
 */
function createSelectBox(string $id, array $messageArray, bool $hidden = false): void
{
    $hide = "";
    if ($hidden) {
        $hide = "hidden";
    }

    echo '<select ' . $hide . ' name="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" class="selectpicker">';

    foreach ($messageArray as $key => $val) {
        $explodedVal = explode(',', $val);
        $value = htmlspecialchars($explodedVal[0], ENT_QUOTES, 'UTF-8');

        if (count($explodedVal) > 1) {
            $text = htmlspecialchars($explodedVal[1], ENT_QUOTES, 'UTF-8');
        } else {
            $text = $value;
        }

        $selected = "";

        if (isset($_POST[$id]) && $value == $_POST[$id]) {
            $selected = " selected";
        }

        echo '<option value="' . $value . '"' . $selected . '>' . $text . '</option>';
    }
    echo '</select>';
}
