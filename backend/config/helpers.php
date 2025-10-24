<?php

function jsonResponse($response, $data, $statusCode = 200) {
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
}
