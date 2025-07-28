<?php

namespace App\Services\Moyasar;

class Search
{
    protected $params = [];

    public static function query()
    {
        return new static();
    }

    public function id($id)
    {
        $this->params['id'] = $id;
        return $this;
    }

    public function status($status)
    {
        $this->params['status'] = $status;
        return $this;
    }

    public function source($source)
    {
        $this->params['source'] = $source;
        return $this;
    }

    public function page($page)
    {
        $this->params['page'] = $page;
        return $this;
    }

    public function createdAfter($date)
    {
        $this->params['created[gte]'] = $date;
        return $this;
    }

    public function createdBefore($date)
    {
        $this->params['created[lte]'] = $date;
        return $this;
    }

    public function toArray()
    {
        return $this->params;
    }
}
