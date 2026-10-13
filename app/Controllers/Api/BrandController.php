<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Models\BrandModel;

class BrandController extends ApiController
{
    public function index()
    {
        return $this->respondOK((new BrandModel())->activeBrands());
    }

    public function show($id = null)
    {
        $brand = (new BrandModel())->find($id);
        if ($brand === null) {
            return $this->respondNotFound('Brand not found.');
        }

        return $this->respondOK($brand);
    }
}