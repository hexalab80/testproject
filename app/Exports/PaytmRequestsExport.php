<?php

namespace App\Exports;
  
use App\PaytmRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
  
class PaytmRequestsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return PaytmRequest::all();
        $paytm_requests = PaytmRequest::where('status','!=','1')->orderBy('id', 'desc')->get(); 
        return $paytm_requests;
    }
}
