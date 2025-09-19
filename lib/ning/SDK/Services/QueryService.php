<?php
namespace Ning\SDK\Services;

use ArrayIterator;
use Ning\SDK\Entities\Content;
use Ning\SDK\Query\FilterEvaluator;
use Ning\SDK\Query\QueryDefinition;

class QueryService
{
    /** @var ContentService */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function createQuery(string $resourceType): QueryDefinition
    {
        return new QueryDefinition($resourceType, $this);
    }

    /**
     * Execute a query definition and return the matching objects.
     *
     * @return array
     */
    public function execute(QueryDefinition $definition): array
    {
        $items = [];
        if ($definition->getResourceType() === 'Content') {
            $items = $this->contentService->all();
        }

        $evaluator = new FilterEvaluator();
        $filters = $definition->getFilters();
        if ($filters) {
            $items = array_values(array_filter($items, function ($item) use ($filters, $evaluator) {
                return $evaluator->matchesAll($item, $filters);
            }));
        }

        foreach (array_reverse($definition->getOrders()) as $order) {
            usort($items, function ($a, $b) use ($order) {
                $field = $order['field'];
                $direction = strtolower($order['direction']);
                $valueA = FilterEvaluator::readField($a, $field);
                $valueB = FilterEvaluator::readField($b, $field);
                if ($valueA == $valueB) {
                    return 0;
                }
                $comparison = ($valueA < $valueB) ? -1 : 1;
                return $direction === 'desc' ? -$comparison : $comparison;
            });
        }

        list($begin, $end) = $definition->getRange();
        if (!is_null($end)) {
            $length = max(0, $end - $begin);
            $items = array_slice($items, $begin, $length);
        } elseif ($begin > 0) {
            $items = array_slice($items, $begin);
        }

        return $items;
    }

    public function resultSize(QueryDefinition $definition): int
    {
        return count($this->execute($definition));
    }
}
