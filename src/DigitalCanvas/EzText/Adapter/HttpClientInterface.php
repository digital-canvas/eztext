<?php
namespace DigitalCanvas\EzText\Adapter;

use DigitalCanvas\EzText\Request;
use DigitalCanvas\EzText\Response;

/**
 * Http Client Adapter interface
 *
 * @package EzText
 * @category Adapter
 */
interface HttpClientInterface {

    /**
     * Sends request and returns response
     *
     * @param Request $request The request to send
     *
     * @return Response Te received response
     */
    public function sendRequest(Request $request);
}
