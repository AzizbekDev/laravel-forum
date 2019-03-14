<?php 
namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    protected $request, $builder;

    /**
     * Filters constructor
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;
        if($this->request->has('by')){
            $this->by($this->request->by);
        }
        return $this->builder;
    }
}