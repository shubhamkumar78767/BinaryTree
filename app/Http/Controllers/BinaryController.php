<?php

namespace App\Http\Controllers;

use App\Models\Binary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class BinaryController extends Controller
{
    public function index()
    {
        $allRootElement = Binary::all();
        return view('binary', compact('allRootElement'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'root' => 'required|string|max:50',
            'name' => 'required|string|max:50',
            'position' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 204, 'message' => $validator->errors()->first()]);
        }

        $root = $parent = Binary::findOrFail($request->input('root'));
        $position = $request->input('position');

        while ($root) {
            if ($position == 1) {
                if ($root->left == 0) {
                    $data = array(
                        'name' => $request->input('name'),
                        'left' => 0,
                        'right' => 0,
                        'sponser' => $root->id,
                        'director' => $parent->id,
                    );
                    $entry = Binary::create($data);
                    $root->update(['left' => $entry->id]);
                    $root = false;
                } else {
                    $root = Binary::findOrFail($root->left);
                }
            } else {
                if ($root->right == 0) {
                    $data = array(
                        'name' => $request->input('name'),
                        'left' => 0,
                        'right' => 0,
                        'sponser' => $root->id,
                        'director' => $parent->id,
                    );
                    $entry = Binary::create($data);
                    $root->update(['right' => $entry->id]);
                    $root = false;
                } else {
                    $root = Binary::findOrFail($root->right);
                }
            }
        }

        return redirect()->back()->with('message', 'inserted successfully');
    }

    public function bnbSend()
    {
        return view('bnbSend');
    }

    public function TokenSend()
    {
        return view('TokenSend');
    }

    public function BinaryTree(Request $req)
    {
        $referralId = $req->get('refferal_id'); //Meta12345
        $position = $req->get('position');
        $parent = $direct_sponser_id = User::where('customer_sponser_id', $referralId)->first();
        $flag = true;

        $register_array = array(

            'name' => $req->get('full_name'),
            'email' => $req->get('email'),
            'phone' => $req->get('phone'),
            'wallet_address' => $req->get('wallet_address'),
            'password' => Hash::make($req->get('password')),
            'position' => $req->get('position'),
            'customer_sponser_id' => 'META123456',
            'active' => 0,
            'active_date' => NUll,
            'block' => 0,
            'package' => 0,
        );

        while ($flag) {

            $root = User::where(['sponser_id' => $parent->customer_sponser_id, 'position' => $position])->first();

            if (!$root) {
                $sponser_id = $parent->customer_sponser_id;
                $inserted_user_id = $this->RegisterWithBinaryTree($register_array, $sponser_id, $direct_sponser_id);

                if ($inserted_user_id) {

                    $res_logclient = $this->InsertInToLogClient($inserted_user_id);
                }

                $flag  =  false;
            } else {

                $parent = $root;
            }
        }
    }

    private function RegisterWithBinaryTree($register_array, $sponser_id, $direct_sponser_id)
    {
        $register_array['sponser_id'] = $sponser_id;
        $register_array['direct_sponser_id'] = $direct_sponser_id->customer_sponser_id;

        $user = User::updateOrCreate([

            // 'wallet_address'   => $req->get('wallet_address'),
            'email'            => $register_array['email'],
        ], $register_array);

        if ($user) {
            return $user->id;
        }
        return false;
    }

    function fetchUserBalance($userToken = null)
    {

        $userAddress = '0x1C913bf1F34daA8b51D397f7b29DF375EEa917A1';
        $contactAddress = '0x337610d27c682E347C9cD60BD4b3b107C9d34dDd';

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => "https://api-testnet.bscscan.com/api?module=account&action=tokenbalance&contractaddress=$contactAddress&address=$userAddress&tag=latest&apikey=5CYGIHYGEG13CK77VGKW42CZ886GYYXZX1",

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);

        if ($response->status == 1) {
            echo json_encode([true, $response->result]);
            die;
        } else {
            echo json_encode([false, 'Something Went Wrong By EtherScan...']);
            die;
        }
    }

    function fetchContractAbi($address = null)
    {
        
        $contactAddress = '0x337610d27c682E347C9cD60BD4b3b107C9d34dDd';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-testnet.bscscan.com/api?module=contract&action=getabi&address=$contactAddress&apikey=5CYGIHYGEG13CK77VGKW42CZ886GYYXZX1",

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);

        if ($response->status == 1) {
            echo json_encode([true, $response->result]);
            die;
        } else {
            echo json_encode([false, 'Something Went Wrong By EtherScan...']);
            die;
        }
    }
}
