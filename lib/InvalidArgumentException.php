<?php
declare(strict_types=1);

namespace Lcobucci\CacheStuff;

final class InvalidArgumentException extends \InvalidArgumentException implements \Psr\SimpleCache\InvalidArgumentException
{
}
