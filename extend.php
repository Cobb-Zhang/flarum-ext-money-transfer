<?php

/*
 * This file is part of cobbz/flarum-ext-money-batch-transfer.
 *
 * Copyright (c) 2024 cobbz.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Cobbz\FlarumExtMoneyBatchTransfer;

use Flarum\Extend;
use Flarum\Api\Serializer\ForumSerializer;
use Cobbz\FlarumExtMoneyBatchTransfer\Api\Controller\MoneyTransferController;
use Cobbz\FlarumExtMoneyBatchTransfer\Api\Controller\EventsShowController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->post('/helloworld', 'helloworld', MoneyTransferController::class)
];
