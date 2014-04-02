<?php
namespace DigitalCanvas\EzText\Exception;

use DigitalCanvas\EzText\ExceptionInterface;

/**
 * Exception thrown if an argument does not match with the expected value.
 *
 * @package ExText
 * @category Exception
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface {

}
