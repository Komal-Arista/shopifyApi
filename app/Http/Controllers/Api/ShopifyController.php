<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Shopify;
use Exception;

class ShopifyController extends Controller
{
    public function sendOtp(Request $request)
    { 
        try
        {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|regex:/^\+?[0-9]{7,15}$/'
            ]);

            if ($validator->fails()) {
                $response['ERROE_CODE'] = config('constants.ERROE_CODE');
                $response['error'] = $validator->errors();
                return response()->json($response);
            }

            $otp = rand(1000, 9999); // Generate a 4-digit OTP

            $otp_datas = [
                "phone_number" =>  $request->phone_number ?? '',
                "otp" => $otp ?? '',
            ];

            //Update record if exists else create new record (Check by Phone_number field)
            $shopify = Shopify::updateOrCreate(
                [
                    'phone_number' => $request->phone_number
                ],
                [
                    'otp' => $otp
                ]
            );
            DB::commit();

            $response['SUCCESS_CODE'] = config('constants.SUCCESS_CODE');
            $response['data'] = $otp_datas;
            
            return response()->json($response);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status'=>FALSE,'message'=>$e->getMessage(),'code'=> 500]);
        }
    }

    public function verifyOtp(Request $request)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|regex:/^\+?[0-9]{7,15}$/',
                'otp' => 'required|digits:4'
            ]);

            if ($validator->fails()) {
                $response['ERROE_CODE'] = config('constants.ERROE_CODE');
                $response['error'] = $validator->errors();
                return response()->json($response);
            }

            // Verify OTP
            $recordExists = Shopify::where('phone_number', $request->phone_number)->where('otp', $request->otp)->first();

            if ($recordExists) {
                return response()->json(['success' => 'OTP verified successfully'], 200);
            } else {
                return response()->json(['error' => 'Invalid OTP'], 400);
            }

        } catch (Exception $e) {
            return response()->json(['status'=>FALSE,'message'=>$e->getMessage(),'code'=> 500]);
        }     
    }
}
