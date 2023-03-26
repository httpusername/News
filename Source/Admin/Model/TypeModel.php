<?php

namespace Admin\Model;

use Common\Model;

class TypeModel extends Model
{
    public function __construct()
    {
        $this->tableName = 'test';
        parent::__construct();
    }
}