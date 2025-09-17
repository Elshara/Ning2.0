<?php
namespace Ning\SDK\Query;

use Ning\SDK\Services\QueryService;

class QueryDefinition
{
    private $resourceType;
    private $service;
    private $filters = [];
    private $orders = [];
    private $begin = 0;
    private $end = null;

    public function __construct(string $resourceType, QueryService $service)
    {
        $this->resourceType = $resourceType;
        $this->service = $service;
    }

    public function filter($field, $operator = null, $value = null, $type = null): self
    {
        if ($field instanceof FilterNode) {
            $this->filters[] = $field;
            return $this;
        }
        $this->filters[] = FilterNode::simple($field, $operator, $value, $type);
        return $this;
    }

    public function order(string $field, string $direction = 'asc', $attributeType = null): self
    {
        $this->orders[] = [
            'field' => $field,
            'direction' => $direction,
            'attributeType' => $attributeType,
        ];
        return $this;
    }

    public function begin(int $index): self
    {
        $this->begin = $index;
        return $this;
    }

    public function end(int $index): self
    {
        $this->end = $index;
        return $this;
    }

    public function execute(): array
    {
        return $this->service->execute($this);
    }

    public function getResultSize(): int
    {
        return $this->service->resultSize($this);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getRange(): array
    {
        return [$this->begin, $this->end];
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }
}
