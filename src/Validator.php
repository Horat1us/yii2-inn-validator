<?php declare(strict_types=1);

namespace Horat1us\Inn\Yii;

use Horat1us\Inn;
use yii\validators;

class Validator extends validators\Validator
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
        if ($this->enableCheckSum && !$parser->isValid()) {
            return [$this->message, []];
        }
        if (!is_null($this->minAge)) {
            $max = Inn\Parser::maximalValue($this->minAge);
            if ((int)$value > $max) {
                return [$this->message, []];
            }
        }
        if (!is_null($this->maxAge)) {
            $min = Inn\Parser::minimalValue($this->maxAge);
            if ((int)$value < $min) {
                return [$this->message, []];
            }
        }
        return null;
    }
}
