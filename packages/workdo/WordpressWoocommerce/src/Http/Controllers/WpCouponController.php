<?php

namespace Workdo\WordpressWoocommerce\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class WpCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(Auth::user()->isAbleTo('woocommerce coupon manage'))
        {
            $company_settings = getCompanyAllSetting();
            $api_url = (isset($company_settings['store_url']) ? $company_settings['store_url'] : '').'/wp-json/wc/v3/';
            // Your WooCommerce API credentials
            $consumer_key =   isset($company_settings['consumer_key']) ? $company_settings['consumer_key'] : '';
            $consumer_secret = isset($company_settings['consumer_secret']) ? $company_settings['consumer_secret'] : '';
            if(!empty($consumer_key ) && !empty($consumer_secret))
            {
                // Prepare cURL request with pagination parameters
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url . 'coupons?per_page=' . 100);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: Basic ' . base64_encode($consumer_key . ':' . $consumer_secret),
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                // Execute cURL request
                $response = curl_exec($ch);
                curl_close($ch);
                // Check for errors
                if ($response === false) {
                    $error_message = curl_error($ch);
                    curl_close($ch);
                    return redirect()->back()->with('error', 'cURL Error: ' . $error_message);
                }

                // Decode JSON response
                $wp_coupons = json_decode($response, true);

                return view('wordpress-woocommerce::wpcoupon.index',compact('wp_coupons'));
            }
            else
            {
                return redirect()->back()->with('error', __('Please configure the woocommerce store settings.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('wordpress-woocommerce::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('wordpress-woocommerce::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('wordpress-woocommerce::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
