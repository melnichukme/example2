<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    /**
     * @var array
     */
    protected array $availableFields = [];

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(
        protected ValidatorInterface $validator
    ) {
        $this->populate();

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        $errors = $this->validator->validate($this);

        $messages = ['message' => 'validation_failed', 'errors' => []];

        /** @var \Symfony\Component\Validator\ConstraintViolation */
        foreach ($errors as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        if (count($messages['errors']) > 0) {
            $response = new JsonResponse($messages, 422);
            $response->send();

            exit;
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        $requestFields = $this->getRequestData();

        foreach (get_object_vars($this) as $attribute => $_) {
            if (isset($requestFields[$attribute])) {
                if (count($this->availableFields) && !in_array($attribute, $this->availableFields)) {
                    continue;
                }

                $data[$attribute] = $requestFields[$attribute];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getRequestData(): array
    {
        $request = Request::createFromGlobals();

        $response = match ($request->server->get('REQUEST_METHOD')) {
            Request::METHOD_POST, Request::METHOD_PUT => $request->toArray(),
            Request::METHOD_GET => $request->query->all(),
        };

        if ($request->server->get('REQUEST_METHOD') === Request::METHOD_GET) {
            foreach ($response as $key => $item) {
                $response[$key] = ctype_digit($item) ? (int)$item : $item;
            }
        }


        return $response;
    }

    /**
     * @return void
     */
    protected function populate(): void
    {
        $requestFields = $this->getRequestData();

        foreach (get_object_vars($this) as $attribute => $_) {
            if (isset($requestFields[$attribute])) {
                $this->{$attribute} = $requestFields[$attribute];
            }
        }
    }

    /**
     * @return bool
     */
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}