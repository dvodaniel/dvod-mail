<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function Symfony\Component\Mime\getAttachments;

class AttachementsController extends Controller
{

    public function store(Request $request){

        $path = $request->file('attachements')->store('s3');

        return $path;

    }

}
