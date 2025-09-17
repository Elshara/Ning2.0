<?php
namespace Ning\SDK\Entities;

use Ning\SDK\Entities\ContentAttributes;

class Content
{
    public $id;
    public $type;
    public $title;
    public $description;
    public $contributorName;
    public $createdDate;
    public $updatedDate;
    public $isPrivate = false;
    public $my;

    public function __construct(string $type)
    {
        $this->type = $type;
        $this->my = new ContentAttributes();
    }
}
