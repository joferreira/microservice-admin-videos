<?php

namespace Core\UseCase\DTO\CastMember\Delete;

class CastMemberDeleteOutputDto
{
    public function __construct(
        public bool $success,
    ){} 
}