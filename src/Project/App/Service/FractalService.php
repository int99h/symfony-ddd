<?php

namespace App\Project\App\Service;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\JsonApiSerializer;

/**
 * Class FractalService
 * @package App\Project\App\Service
 */
class FractalService extends Manager
{
    /**
     * @param $resource
     * @param bool $isSuccess
     * @return array
     */
     public function transform($resource, bool $isSuccess = true): array
     {
         $response = [
             'success' => $isSuccess,
         ];
         if ($resource instanceof ResourceInterface) {
             $this->setSerializer(new JsonApiSerializer());
             $data = $this->createData($resource);
             $response = array_merge($response, $data->toArray());
         } else {
             $response['message'] = $resource;
         }

         return $response;
     }
}