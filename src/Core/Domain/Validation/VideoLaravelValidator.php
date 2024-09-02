<?php

namespace Core\Domain\Validation;

use Core\Domain\Entity\Entity;
use Illuminate\Support\Facades\Validator;

class VideoLaravelValidator implements ValidatorInterface
{
    public function validate(Entity $entity): void
    {
        $data = $this->convertEntityToArray($entity);

        $valitador = Validator::make($data, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'year_launched' => 'required|integer',
            'duration' => 'required|integer',
        ]);

        if ($valitador->fails()) {
            foreach ($valitador->errors()->messages() as $error) {
                $entity->notification->addError([
                    'context'=> 'video',
                    'message' => $error[0]
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