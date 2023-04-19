<?php

namespace App\Http\Controllers;

use App\Logger\Logger;
use App\Models\User;
use App\Models\UserIdentityDocumentHistory;
use App\Models\UserIdentityDocumentPhoto;
use App\Utils\StorageUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IdentityVerifyController extends Controller
{
    public function verify(Request $request)
    {
        $user = $request->user();
        $user_id_verified = $user->id_verified;

        if(isset($user_id_verified['card_id_status']) && $user_id_verified['card_id_status']==='pending'){
            return response()->json(['message' => "Your ID Card is already pending approval!"], 400);
        }

        if(isset($user_id_verified['card_id_status']) && $user_id_verified['card_id_status']==='approved'){
            return response()->json(['message' => trans('rookie.id_already_verified')], 400);
        }

        /*
         * Handle new id request
         */
        if(!isset($user_id_verified)){

            $validator = Validator::make($request->all(),[
                'front_path' => ['required', 'string', 'unique:users_identities_documents_photos,path_location'],
                'back_path' => ['sometimes', 'string', 'unique:users_identities_documents_photos,path_location'],
                'selfie_path' => ['required', 'string', 'unique:users_identities_documents_photos,path_location']
            ], [
                'front_path.required' => "You must upload a Front identity card photo",
                'selfie_path.required' => "You must upload a Selfie"
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            try {
                $document = $this->store($request, $user);
            }catch (\Exception $exception){
                Logger::logException($exception);
                return response()->json(['message' => $exception->getMessage()], 400);
            }
        }

        /*
         * Handle update id request
         */
        if(isset($user_id_verified['card_id_status']) && $user_id_verified['card_id_status']==='rejected'){

            $validator = Validator::make($request->all(),[
                'front_path' => ['sometimes', 'string'],
                'back_path' => ['sometimes', 'string'],
                'selfie_path' => ['sometimes', 'string']
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            try {
                $document = $this->update($request, $user, $user_id_verified);
            }catch (\Exception $exception){
                Logger::logException($exception);
                return response()->json(['message' => $exception->getMessage()], 400);
            }
        }

        $front_response = (isset($document->front_photo))
            ? ['status' => $document->front_photo->status, 'url' => $document->front_photo->url,
                'path_location' => $document->front_photo->path_location, 'reason' => $document->front_photo->decline_reason]
            : null;
        $back_response = (isset($document->back_photo))
            ? ['status' => $document->back_photo->status, 'url' => $document->back_photo->url,
                'path_location' => $document->back_photo->path_location, 'reason' => $document->back_photo->decline_reason]
            : null;
        $selfie_response = (isset($document->selfie_photo))
            ? ['status' => $document->selfie_photo->status, 'url' => $document->selfie_photo->url,
                'path_location' => $document->selfie_photo->path_location, 'reason' => $document->selfie_photo->decline_reason]
            : null;

        $response = [
            'card_id_status' => $document->status,
            'card_id_reason' => $document->reason,
            'front' => $front_response,
            'back' => $back_response,
            'selfie' => $selfie_response
        ];

        return response()->json($response, 201);
    }

    private function update(Request $request, User $user, $old_id_verified)
    {
        $front_status = $old_id_verified['front']['status'] ?? null;
        $back_status = $old_id_verified['back']['status'] ?? null;
        $selfie_status = $old_id_verified['selfie']['status'] ?? null;

        $front_path = $old_id_verified['front']['path_location'] ?? null;
        $back_path = $old_id_verified['back']['path_location'] ?? null;
        $selfie_path = $old_id_verified['selfie']['path_location'] ?? null;

        if(isset($request->front_path) && $front_path !== $request->front_path){
            $front = StorageUtils::assignObject($request->front_path, 'identity_document', $user);
            if($front['status']==='error'){
                throw new \Exception($front['message']);
            }
        }

        $front_id = UserIdentityDocumentPhoto::create([
            'user_id' => $user->id,
            'type' => 'front',
            'path_location' => $front['path_location'] ?? $front_path,
            'status' => (isset($front_status) && $front_status === 'approved')
                ? 'approved'
                : 'pending'
        ])->id;

        if(isset($request->back_path) && $back_path!==$request->back_path){
            $back = StorageUtils::assignObject($request->back_path, 'identity_document', $user);
            if($back['status']==='error'){
                throw new \Exception($back['message']);
            }
        }

        if(isset($back) || isset($back_path)){
            $back_id = UserIdentityDocumentPhoto::create([
                'user_id' => $user->id,
                'type' => 'back',
                'path_location' => $back['path_location'] ?? $back_path,
                'status' => (isset($back_status) && $back_status === 'approved')
                    ? 'approved'
                    : 'pending'
            ])->id;
        }

        if(isset($request->selfie_path) && $selfie_path!==$request->selfie_path){
            $selfie = StorageUtils::assignObject($request->selfie_path, 'identity_document', $user);
            if($selfie['status']==='error'){
                throw new \Exception($selfie['message']);
            }
        }

        $selfie_id = UserIdentityDocumentPhoto::create([
            'user_id' => $user->id,
            'type' => 'selfie',
            'path_location' => $selfie['path_location'] ?? $selfie_path,
            'status' => (isset($selfie_status) && $selfie_status === 'approved')
                ? 'approved'
                : 'pending'
        ])->id;

        $document = UserIdentityDocumentHistory::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'front_path_id' => $front_id,
            'selfie_path_id' => $selfie_id,
            'back_path_id' => $back_id ?? null,
        ]);
        
        $user->update([
            'admin_check' => true
        ]);

        return $document;
    }

    private function store(Request $request, User $user)
    {
        $front = StorageUtils::assignObject($request->front_path, 'identity_document', $user);
        $selfie = StorageUtils::assignObject($request->selfie_path, 'identity_document', $user);

        if(isset($request->back_path)){
            $back = StorageUtils::assignObject($request->back_path, 'identity_document', $user);
        }

        if($front['status']==='error' || $selfie['status']==='error' || (isset($back) && $back['status']==='error')){
            throw new \Exception($front['message']);
        }

        $front_id = UserIdentityDocumentPhoto::create([
            'user_id' => $user->id,
            'type' => 'front',
            'path_location' => $front['path_location']
        ])->id;

        $selfie_id = UserIdentityDocumentPhoto::create([
            'user_id' => $user->id,
            'type' => 'selfie',
            'path_location' => $selfie['path_location']
        ])->id;

        if(isset($request->back_path)){
            $back_id = UserIdentityDocumentPhoto::create([
                'user_id' => $user->id,
                'type' => 'back',
                'path_location' => $back['path_location']
            ])->id;
        }

        $id_attributes = [
            'user_id' => $user->id,
            'status' => 'pending',
            'front_path_id' => $front_id,
            'selfie_path_id' => $selfie_id,
            'back_path_id' => $back_id ?? null,
        ];

        $document = UserIdentityDocumentHistory::create($id_attributes);
        $user->update(['admin_check' => true]);

        return $document;
    }
}
