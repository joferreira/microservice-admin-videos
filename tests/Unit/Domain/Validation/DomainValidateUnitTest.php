<?php

namespace Tests\Unit\Domain\Validation;

use Core\Domain\Validation\DomainValidation;
use Core\Domain\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;
use Throwable;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        try {
            $value = '';
            DomainValidation::notNull($value);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }        
    }

    public function testNotNullCustomMessageException()
    {
        try {
            $value = '';
            DomainValidation::notNull($value, 'custom message error');
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'custom message error');
        }
    }

    public function testStrMaxlength()
    {
        try {
            $value = 'test';
            DomainValidation::strMaxLength($value, 3, 'Custom message');
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom message');
        }        
    }

    public function testStMinlength()
    {
        try {
            $value = 'test';
            DomainValidation::strMinLength($value, 8, 'Custom message');
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom message');
        }        
    }

    public function testStrCanNullAndMaxlength()
    {
        try {
            $value = 'teste';
            DomainValidation::strCanNullAndMaxlength($value, 3, 'Custom message');
            $this->assertTrue(false);
        } catch (Throwable $th) {
            var_dump($th->getMessage());
            $this->assertInstanceOf(EntityValidationException::class, $th, 'Custom message');
        }        
    }
}