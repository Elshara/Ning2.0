<?php
namespace Ning\SDK\Services;

use Ning\SDK\Entities\Content;

class ContentService
{
    /** @var Content[] keyed by id */
    private $contentById = [];
    private $nextId = 1;

    public function create(string $type): Content
    {
        $content = new Content($type);
        return $content;
    }

    public function save(Content $content): Content
    {
        if (!$content->id) {
            $content->id = (string)$this->nextId++;
            $content->createdDate = $content->createdDate ?: date('c');
        }
        $content->updatedDate = date('c');
        $this->contentById[$content->id] = $content;
        return $content;
    }

    public function load($id)
    {
        if (is_array($id)) {
            $results = [];
            foreach ($id as $singleId) {
                $loaded = $this->load($singleId);
                if ($loaded) {
                    $results[] = $loaded;
                }
            }
            return $results;
        }
        return $this->contentById[(string)$id] ?? null;
    }

    public function delete($content): void
    {
        if ($content instanceof Content) {
            unset($this->contentById[$content->id]);
            return;
        }
        if (is_array($content)) {
            foreach ($content as $item) {
                $this->delete($item);
            }
        } else {
            unset($this->contentById[(string)$content]);
        }
    }

    /**
     * @return Content[]
     */
    public function all(): array
    {
        return array_values($this->contentById);
    }

    public function clear(): void
    {
        $this->contentById = [];
        $this->nextId = 1;
    }
}
