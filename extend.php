<?php

/*
 * This file is part of cobbz/flarum-ext-money-transfer.
 *
 * Copyright (c) 2024 cobbz.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Cobbz\FlarumExtMoneyTransfer;

use Flarum\Extend;
use Cobbz\FlarumExtMoneyTransfer\Api\Controller\MoneyTransferController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->post('/fine-transfer-money', 'fine-transfer-money', MoneyTransferController::class)
];
