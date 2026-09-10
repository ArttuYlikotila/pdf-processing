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
<!DOCTYPE html>
<html lang="<?php echo $lang ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo $messages['htmlTitle'] ?></title>

        <base href="<?php echo htmlspecialchars($configs['baseUrl'], ENT_QUOTES, 'UTF-8') ?>">

        <!-- Latest compiled and minified CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

        <!-- additional css -->
        <link rel="stylesheet" href="css/pdf.css">

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="any" href="images/favicon.ico">
    </head>
    <body>
        <header class="page-header">
            <nav class="navbar">
                <div class="container-fluid">
                    <a class="logo-link" href="<?php echo $messages['logo_link'] ?>" target="_blank">
                        <img
                            class="logo"
                            src="<?php echo $messages['logo_image'] ?>"
                            alt="<?php echo $messages['logo_alt'] ?>"
                        />
                    </a>
                    <h3><?php echo $messages['headline'] ?></h3>

                    <div class="nav-links">
                        <?php foreach ($messages['navButton'] as $nav) {
                            $navigator = explode(",", $nav);
                        ?>
                            <a class="nav-link" href="<?php echo $navigator[1] ?>">
                                <i class="bi <?php echo $navigator[2] ?>" aria-hidden="true"></i>
                                <?php echo $navigator[0] ?>
                            </a>
                        <?php } ?>
                        <!-- TODO: is this element needed for some reason? -->
                        <!-- <li class="text-center active"></li> -->
                    </div>

                    <div class="lang-selector">
                        <?php $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2); ?>
                        <a
                            href="<?php echo htmlspecialchars($uri_parts[0], ENT_QUOTES, 'UTF-8') ?>?lang=en"
                            <?php if ($lang === 'en') echo "class='chosen'" ?>
                        >
                            Englanti
                        </a>
                        <span> | </span>
                        <a
                            href="<?php echo htmlspecialchars($uri_parts[0], ENT_QUOTES, 'UTF-8') ?>?lang=fi"
                            <?php if ($lang === 'fi') echo "class='chosen'" ?>
                        >
                            Suomi
                        </a>
                    </div>
                </div>
            </nav>
        </header>
        <main class="top-buffer bottom-buffer">
