<?php

namespace ChrisKonnertz\DeepLy\Protocol;

/**
 * A class that implements this interface represents the protocol used for communication with the API
 */
interface ProtocolInterface
{

    /**
     * Creates a request bag according to the JSON RPC protocol.
     * The API will be able to understand it.
     * The result is encoded as a JSON string.
     *
     * @param array  $payload The payload / parameters of the request. Will be encoded as JSON
     * @param string $method  The method of the API call
     * @return string
     */
    public function createRequestData(array $payload, $method);

}
