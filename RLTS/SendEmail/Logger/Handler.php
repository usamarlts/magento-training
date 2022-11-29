<?php

declare(strict_types=1);

namespace RLTS\SendEmail\Logger;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    protected $loggerType = Logger::DEBUG;

    protected $fileName = '/var/log/rltsquare.log';
}
