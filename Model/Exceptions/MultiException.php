<?php

namespace bookkeeping\Model\Exceptions;

use \bookkeeping\Model\Traits\TCollection;

class MultiException
    extends \Exception
    implements \ArrayAccess, \Iterator, \Countable
{
    use TCollection;
}