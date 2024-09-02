<?php

namespace Core\Domain\Validation;

use Core\Domain\Entity\Entity;
use Rakit\Validation\Validator;

class VideoRakitValidator implements ValidatorInterface
{
    public function validate(Entity $entity): void 
    {
        $data = $this->convertEntityToArray($entity);
        $validation = (new Validator())->validate($data, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'year_launched' => 'required|integer',
            'duration' => 'required|integer',
        ]);

        if ($validation->fails()) {
            // dd($validation->errors()->all());
            foreach ($validation->errors()->all() as $error) {
                $entity->notification->addError([
                    'context' => 'video',
                    'message' => $error
                ]);
            }
        }
    }

    private function convertEntityToArray(Entity $entity): array
    {
        return [
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'duration' => $entity->duration,
        ];
    }
}