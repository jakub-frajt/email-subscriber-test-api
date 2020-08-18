<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

const STATUS_ERROR   = 'error';
const STATUS_SUCCESS = 'success';

function sendJsonResponse(string $status, string $message): void
{
    $response = new JsonResponse(
        [
            'status'  => $status,
            'message' => $message,
        ],
        $status === STATUS_ERROR ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK
    );

    $response->send();
    exit(0);
}

$request   = Request::createFromGlobals();
$validator = Validation::createValidator();

if ($request->isMethod('POST') === false) {
    sendJsonResponse(STATUS_ERROR, 'Invalid request method. API accepts POST requests only.');
}

if ($request->getContentType() !== 'json') {
    sendJsonResponse(
        STATUS_ERROR,
        'Invalid content type of request.'
    );
}

$requestData = json_decode($request->getContent(), true);

if ($requestData === null || $requestData === false) {
    sendJsonResponse(STATUS_ERROR, 'Invalid JSON.');
}

$validationResult = $validator->validate(
    $requestData['email'] ?? null,
    [
        new NotBlank(),
        new Email(),
    ]
);


if ($validationResult->count() > 0) {
    sendJsonResponse(STATUS_ERROR, 'Invalid e-mail address');
}

sendJsonResponse(STATUS_SUCCESS, 'E-mail address added.');

