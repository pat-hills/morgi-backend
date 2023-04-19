<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Logger\Logger;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\Photo;
use App\Models\PhotoHistory;
use App\Models\User;
use App\Models\UserDescriptionHistory;
use App\Models\Video;
use App\Models\VideoHistory;
use App\Utils\Admin\DescriptionUtils;
use App\Utils\Admin\PhotoUtils;
use App\Utils\Admin\VideoUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserUpdateController extends Controller
{

    //improve this controller with UserDescriptionHistory cast inside params and remove query inside each function

    // START DESCRIPTION SECTION
    public function approveDescription(Request $request, User $user)
    {
        $user_description_history = UserDescriptionHistory::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        try {
            DescriptionUtils::approve($user_description_history);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        try {
            UserProfileUtils::storeOrUpdate($user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return redirect()->back()->with(['success' => 'Description approved!']);
    }

    public function declineDescription(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $user_description_history = UserDescriptionHistory::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        try {
            DescriptionUtils::decline($user_description_history, $request->decline_reason);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        return redirect()->back()->with(['success' => 'Description declined!']);
    }

    public function declineStoredDescription(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        try {
            DescriptionUtils::declineStoredDescription($user, $request->decline_reason);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        try {
            UserProfileUtils::storeOrUpdate($user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return redirect()->back()->with(['success' => 'Description declined!']);
    }

    // END DESCRIPTION SECTION

    // START PHOTO SECTION

    public function approvePhoto(Request $request, PhotoHistory $photo_history)
    {
        try {
            PhotoUtils::approve($photo_history);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        try {
            UserProfileUtils::storeOrUpdate($photo_history->user_id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return redirect()->back()->with(['success' => 'Photo approved!']);
    }

    public function declinePhoto(Request $request, PhotoHistory $photo_history)
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        try {
            PhotoUtils::decline($photo_history, $request->decline_reason);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        return redirect()->back()->with(['success' => 'Photo declined!']);
    }

    public function declineStoredPhoto(Request $request, Photo $photo)
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        try {
            PhotoUtils::declineStoredPhoto($photo, $request->decline_reason);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        return redirect()->back()->with(['success' => 'Photo declined!']);
    }

    // END PHOTO SECTION

    // START VIDEO SECTION

    public function approveVideo(Request $request, VideoHistory $video_history)
    {
        try {
            VideoUtils::approve($video_history);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        try {
            UserProfileUtils::storeOrUpdate($video_history->user_id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return redirect()->back()->with(['success' => 'Video approved!']);
    }

    public function declineVideo(Request $request, VideoHistory $video_history)
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        try {
            VideoUtils::decline($video_history, $request->decline_reason);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        return redirect()->back()->with(['success' => 'Video declined!']);
    }

    public function declineStoredVideo(Request $request, Video $video)
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        try {
            VideoUtils::declineStoredVideo($video, $request->decline_reason);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => $exception->getMessage()], $exception->getCode());
        }

        try {
            UserProfileUtils::storeOrUpdate($video->user_id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return redirect()->back()->with(['success' => 'Video declined!']);
    }

    // END VIDEO SECTION
}
