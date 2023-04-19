<?php

namespace App\Http\Controllers;

use App\Models\GoalType;
use Illuminate\Http\Request;

class GoalTypeController extends Controller
{
   public function index(Request $request)
   {
       $types = GoalType::whereIsActive()->get();
       return response()->json($types);
   }
}
