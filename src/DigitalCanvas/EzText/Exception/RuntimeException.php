<?php
namespace DigitalCanvas\EzText\Exception;

use DigitalCanvas\EzText\ExceptionInterface;

/**
 * Exception thrown if an error which can only be found on runtime occurs.
 *
 * @package EzText
 * @category Exception
 */
class RuntimeException extends \RuntimeException implements ExceptionInterface {

}
