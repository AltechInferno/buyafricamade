<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Currency;
use GeoIP;

class AutoSetCurrency
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('currency_code')) {
            $ip = $request->ip();
            $country = geoip($ip)->iso_code;
            $currency_code = get_currency_by_country($country);

            if ($currency_code) {
                $currency = Currency::where('code', $currency_code)->first();
                if ($currency) {
                    $request->session()->put('currency_code', $currency->code);
                    $request->session()->put('currency_symbol', $currency->symbol);
                    $request->session()->put('currency_exchange_rate', $currency->exchange_rate);
                }
            }
        }

        return $next($request);
    }
}
