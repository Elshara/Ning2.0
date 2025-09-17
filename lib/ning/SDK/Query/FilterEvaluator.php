<?php
namespace Ning\SDK\Query;

use Ning\SDK\Entities\Content;
use Ning\SDK\Entities\ContentAttributes;

class FilterEvaluator
{
    public function matchesAll($item, array $filters): bool
    {
        foreach ($filters as $filter) {
            if (!$this->matches($item, $filter)) {
                return false;
            }
        }
        return true;
    }

    private function matches($item, FilterNode $filter): bool
    {
        switch ($filter->getType()) {
            case 'simple':
                $value = self::readField($item, $filter->getField());
                return $this->compare($value, $filter->getOperator(), $filter->getValue(), $filter->getAttributeType());
            case 'any':
                foreach ($filter->getChildren() as $child) {
                    if ($this->matches($item, $child)) {
                        return true;
                    }
                }
                return false;
            case 'all':
                foreach ($filter->getChildren() as $child) {
                    if (!$this->matches($item, $child)) {
                        return false;
                    }
                }
                return true;
            case 'not':
                $child = $filter->getChildren()[0] ?? null;
                return $child ? !$this->matches($item, $child) : true;
            default:
                return true;
        }
    }

    private function compare($value, $operator, $expected, $attributeType): bool
    {
        switch ($operator) {
            case '=':
                return $value == $expected;
            case '!=':
                return $value != $expected;
            case 'in':
                return in_array($value, (array)$expected, true);
            case 'not in':
                return !in_array($value, (array)$expected, true);
            case '>':
                return $value > $expected;
            case '>=':
                return $value >= $expected;
            case '<':
                return $value < $expected;
            case '<=':
                return $value <= $expected;
            case 'like':
                return stripos((string)$value, (string)$expected) !== false;
            case '!like':
                return stripos((string)$value, (string)$expected) === false;
            case 'likeic':
                return stripos((string)$value, (string)$expected) !== false;
            default:
                return $value == $expected;
        }
    }

    public static function readField($item, string $field)
    {
        if ($field === 'id') {
            return $item->id ?? null;
        }
        if ($field === 'type') {
            return $item->type ?? null;
        }
        if ($field === 'contributorName') {
            return $item->contributorName ?? null;
        }
        if (strpos($field, 'my->') === 0 && isset($item->my)) {
            $attribute = substr($field, 4);
            if (method_exists($item->my, 'raw')) {
                return $item->my->raw($attribute);
            }
            return $item->my->$attribute ?? null;
        }
        if (property_exists($item, $field)) {
            return $item->$field;
        }
        if (is_array($item) && array_key_exists($field, $item)) {
            return $item[$field];
        }
        return null;
    }
}
