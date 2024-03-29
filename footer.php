<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\FileHelper;

global $APPLICATION;
global $USER;
global $directory;
global $properties;
global $template;
global $part;

if (empty($template))
    return;

?>
        <?php include($directory.'/parts/'.$part.'/footer.php'); ?>
        <?php if (FileHelper::isFile($directory.'/parts/custom/body.end.php')) include($directory.'/parts/custom/body.end.php') ?>
        
        <script>
            const catalogLinks = document.querySelectorAll('.menu-column-header-link');

            catalogLinks.forEach(link => {
                if (link.getAttribute('href') === '/catalog/') {
                link.addEventListener('click', function(event) {
                    event.preventDefault();  // Отменяем действие по умолчанию
                });
                link.classList.add('inactive');
                }
            });
        </script>

        <style>
        .inactive {
            pointer-events: none;
            color: gray;
            text-decoration: none;
            cursor: default;
        }
        </style>
    </body>
</html>
<?php if (FileHelper::isFile($directory.'/parts/custom/end.php')) include($directory.'/parts/custom/end.php') ?>