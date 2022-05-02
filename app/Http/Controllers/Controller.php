<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cookie;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCurrentPage(Request $request): int
    {
        return is_numeric($request->query('page')) ? (int) $request->query('page') : 1;
    }

    public function setCookies(string $speciesNameType, string $speciesGroup, string $axiophyteFilter)
    {
        Cookie::queue('speciesNameType', $speciesNameType);
        Cookie::queue('speciesGroup', $speciesGroup);
        Cookie::queue('axiophyteFilter', $axiophyteFilter);
    }
}
