<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
  public function __invoke(Request $request)
  {
    dd('checkout');
  }
}
