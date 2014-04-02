<?php
namespace DigitalCanvas\EzText\Exception;

use DigitalCanvas\EzText\ExceptionInterface;

/**
 * Exception thrown if a value does not adhere to a defined valid data domain.
 *
 * @package EzText
 * @category Exception
 */
class DomainException extends \DomainException implements ExceptionInterface {

}
