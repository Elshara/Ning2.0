<?php
namespace Ning\SDK\Query;

class FilterNode
{
    private $type;
    private $field;
    private $operator;
    private $value;
    private $attributeType;
    private $children = [];

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function simple(string $field, $operator, $value, $attributeType = null): self
    {
        $node = new self('simple');
        $node->field = $field;
        $node->operator = $operator;
        $node->value = $value;
        $node->attributeType = $attributeType;
        return $node;
    }

    public static function group(string $type, array $children): self
    {
        $node = new self($type);
        $node->children = $children;
        return $node;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getAttributeType()
    {
        return $this->attributeType;
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}
