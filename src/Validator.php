<?php declare(strict_types=1);

namespace Horat1us\Inn\Yii;

use Horat1us\Inn;
use Horat1us\Yii\Validation\JsonSchema;
use yii\validators;

class Validator extends validators\Validator implements JsonSchema
{
    public bool $enableCheckSum = true;
    public ?int $minAge = null;
    public ?int $maxAge = null;

    public function init(): void
    {
        parent::init();
        if ($this->message === null) {
            $this->message = \Yii::t('yii', '{attribute} is invalid.');
        }
    }

    protected function validateValue($value)
    {
        try {
            $parser = new Inn\Parser((string)$value);
        } catch (\InvalidArgumentException $e) {
            return [$this->message, []];
        }
        if (($this->enableCheckSum && !$parser->isValid())
            || (($maxValue = $this->getMaxValue()) && $maxValue < $value)
            || (($minValue = $this->getMinValue()) && $minValue > $value)
        ) {
            return [$this->message, []];
        }
        return null;
    }

    public function getJsonSchema(): array
    {
        return array_filter([
            'type' => 'string',
            'format' => 'inn',
            'min' => $this->getMinValue(),
            'max' => $this->getMaxValue(),
        ]);
    }

    public function getMinValue(): ?int
    {
        if (!is_null($this->maxAge)) {
            return Inn\Parser::minimalValue($this->maxAge);
        }
        return null;
    }

    public function getMaxValue(): ?int
    {
        if (!is_null($this->minAge)) {
            return Inn\Parser::maximalValue($this->minAge);
        }
        return null;
    }
}
