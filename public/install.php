<?php
declare(strict_types=1);

use App\Support\CRest;
use App\Support\Logger;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../public/bootstrap.php';

$result = CRest::installApp();

Logger::info('Установка приложения', [
    'request' => $_REQUEST,
    'result'  => $result,
]);

if ($result['rest_only'] === false):?>
    <head>
        <script src="//api.bitrix24.com/api/v1/"></script>
        <?php if ($result['install']):?>
            <script>
                BX24.init(function() {
                    BX24.installFinish();
                }
            </script>
        <?php endif;?>
    </head>
    <body>
        <?php if ($result['install']):?>
            Приложение успешно установлено
        <?php else:?>
            Во время установки приложения возникла ошибка
        <?php endif;?>
    </body>
<?php endif;
