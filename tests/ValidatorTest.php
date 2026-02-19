<?php

declare(strict_types=1);

namespace Horat1us\Inn\Yii\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Horat1us\Inn\Yii\Validator;

class ValidatorTest extends TestCase
{
    public static function validateDateProvider(): array
    {
        return [
            [new Validator(), '3900000000', 'the input value is invalid.'],
            [new Validator(), '', 'the input value is invalid.'],
            [new Validator(), null, 'the input value is invalid.'],
            [new Validator(), '3184710691',],
            [new Validator(), 3184710691,],
            [new Validator(['enableCheckSum' => false,]), '3184710692',],
            [new Validator(['minAge' => 18, 'maxAge' => 40,]), '3184710691',],
            [new Validator(['minAge' => 40,]), '3184710691', 'the input value is invalid.',],
            [new Validator(['maxAge' => 18,]), '3184710691', 'the input value is invalid.',],
        ];
    }

    #[DataProvider('validateDateProvider')]
    public function testValidate(Validator $validator, $value, ?string $expected = null): void
    {
        $this->assertEquals(
            is_null($expected),
            $validator->validate($value, $error)
        );
        $this->assertEquals(
            $expected,
            $error
        );
    }

    public static function jsonSchemaDataProvider(): array
    {
        return [
            [new Validator(), ['type' => 'string', 'format' => 'inn',],],
            [
                new Validator(['minAge' => 18, 'maxAge' => 70]),
                [
                    'type' => 'string',
                    'format' => 'inn',
                    'min' => \Horat1us\Inn\Parser::minimalValue(70),
                    'max' => \Horat1us\Inn\Parser::maximalValue(18),
                ],
            ],
        ];
    }

    #[DataProvider('jsonSchemaDataProvider')]
    public function testJsonSchema(Validator $validator, array $expected): void
    {
        $actual = $validator->getJsonSchema();
        $this->assertEquals($expected, $actual);
    }
}
