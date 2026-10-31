<?php

/**
 * Shared request form input validation. Maintainer: B.
 * Reject non-scalar HTTP fields before assigning typed form properties.
 */
declare(strict_types=1);

namespace common\models;

use yii\base\Model;

abstract class RequestForm extends Model
{
    private array $invalidInputFields = [];

    public function setAttributes($values, $safeOnly = true): void
    {
        $this->invalidInputFields = [];
        if (!is_array($values)) {
            return;
        }

        $safe = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
        $accepted = [];
        foreach ($values as $name => $value) {
            if (!isset($safe[$name])) {
                continue;
            }
            if (!is_scalar($value)) {
                $this->invalidInputFields[] = $name;
                continue;
            }
            if (is_bool($this->$name)) {
                if (!in_array($value, [true, false, 0, 1, '0', '1'], true)) {
                    $this->invalidInputFields[] = $name;
                    continue;
                }
                $accepted[$name] = (bool) $value;
            } else {
                $accepted[$name] = (string) $value;
            }
        }
        parent::setAttributes($accepted, $safeOnly);
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        foreach ($this->invalidInputFields as $attribute) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ' must be a single value.');
        }
        return $this->invalidInputFields === [];
    }
}
