<?php

namespace App\Http\Controllers;

use App\Models\GcashSetting;
use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\PaymentReceipt;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        $page = 'Payment';
        
        $gcash = GcashSetting::where('status', 'Active')->first();
        $isAdmin = auth()->user()->role === 'Superadmin';

        return view('pages.back.v_payment', compact('page', 'gcash', 'isAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $uid = auth()->user()->id;
        $role = auth()->user()->role;
        
        if($role === 'Superadmin'){
         
          $payments = Payment::with('user')
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();
            
        } else {
         $payments = Payment::where('user_id', $uid)
            ->with('user')
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();
        }
    
        $formattedData = $payments->map(function ($item) use ($role) {
            // Prepare action buttons dynamically
            $actions = $role === 'Superadmin' ?
                '<a class="received-btn btn btn-primary-soft btn-sm" href="javascript:void(0)" 
                    data-id="' . $item->id . '" 
                    data-modaltitle="Received">
                    Accept Payment <i class="bi bi-check fs-3"></i> 
                </a>   
                
                <a class="reject-btn btn btn-danger-soft btn-sm" href="javascript:void(0)" 
                    data-id="' . $item->id . '" 
                    data-modaltitle="Reject">
                    Reject Payment <i class="bi bi-check fs-3"></i> 
                </a>
                
                
                
                ' :
                ($item->status === 'Received' ?
                    '<span>Payment received. Thank you</span>' :
                
                    ($item->status === 'Rejected' ? '<span>Payment Rejected.</span>' : 
                    '<a class="edit-btn" href="javascript:void(0)" 
                        data-id="' . e($item->id) . '" 
                        data-gcash="' . e($item->gcash_setting_id) . '" 
                        data-amount="' . e($item->amount) . '" 
                        data-status="' . e($item->status) . '" 
                        data-paymentproof="' . e($item->upload_proof_donation) . '" 
                        data-modaltitle="Edit">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </a>
                    <a class="delete-btn d-none" href="javascript:void(0)" 
                        data-id="' . e($item->id) . '">
                        <i class="bi bi-trash fs-3"></i>
                    </a>'
                ));
    
            return [
                'username' => $item->user->username ?? 'N/A',
                'amount' => $item->amount,
                'paymentproof' => $item->upload_proof_payment,
                'status' => $item->status,
                'actions' => $actions,
            ];
        });
    
        return response()->json(['data' => $formattedData]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'gcash_id' => 'required|integer',
            'amount' => 'required|integer',
            'proof_payment' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('proof_payment')) {
            $imgFile = $request->file('proof_payment');
            $filename = time() . '_' . $imgFile->getClientOriginalName();
            $imagePath = 'assets/uploads/' . $filename;
            $imgFile->move(public_path('assets/uploads'), $filename);
        }

        $payment = Payment::create([
            'gcash_setting_id' => $request->gcash_id,
            'user_id' => auth()->user()->id,
            'amount' => $request->amount,
            'upload_proof_payment' => $imagePath,
        ]);

        auth()->user()->notify(new PaymentReceipt($payment));

        return response()->json([
            'message' => 'Payment sent successfully',
            'type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);

        if (!$donation) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $request->validate([
            'gcash_id' => 'required|integer',
            'amount' => 'required|integer',
            'proof_payment' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('proof_payment')) {
            if ($payment->upload_proof_payment && file_exists(public_path($payment->upload_proof_payment))) {
                unlink(public_path($payment->upload_proof_payment));
            }

            $imgFile = $request->file('proof_payment');
            $filename = time() . '_' . $imgFile->getClientOriginalName();
            $imagePath = 'assets/uploads/' . $filename;
            $imgFile->move(public_path('assets/uploads'), $filename);

            $payment->upload_proof_payment = $imagePath;
        }


        $donation->gcash_setting_id = $request->gcash_id;
        $donation->amount = $request->amount;
        $donation->status = $request->status;
        $donation->save();

        return response()->json([
            'message' => 'Payment details updated successfully',
            'type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($payment->upload_proof_payment && file_exists(public_path($payment->upload_proof_payment))) {
            unlink(public_path($payment->upload_proof_payment));
        }

        DB::transaction(function () use ($payment) {
            $payment->delete();
        });

        return response()->json(['message' => 'Payment deleted successfully', 'type' => 'success']);
    }
    
    
    
    public function receive(Request $request)
    {    
        $id = $request->id;
        
        $payment = Payment::find($id);
        $payment->update(['status' => 'Received']);
    

        return response()->json(['message' => 'Payment received successfully', 'type' => 'success']);
    }
    
    
    
        public function reject(Request $request)
    {    
        $id = $request->id;
        $payment = Payment::find($id);

        $payment = Payment::find($id);
        $payment->update(['status' => 'Reject']);

        return response()->json(['message' => 'Payment reject successfully', 'type' => 'success']);
    }

    public function show_gcash(Request $request)
    {
    
        $gcash = GcashSetting::where('status', 'Active')->first();

        if ($gcash) {
            return response()->json([
                'success' => true,
                'data' => [
                    'gcash_id' => $gcash->id,
                    'gcash_number' => $gcash->gcash_number,
                    'gcash_qr' => $gcash->gcash_qr,
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No gcash details found..'
            ], 400);
        }
    }
}
