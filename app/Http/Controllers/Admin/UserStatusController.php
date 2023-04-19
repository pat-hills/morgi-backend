<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserEnum;
use App\Http\Controllers\Controller;
use App\Mixpanel\Events\EventAdminAcceptedUser;
use App\Models\Path;
use App\Models\Rookie;
use App\Models\User;
use App\Models\UserRejectHistory;
use App\Services\Chat\Chat;
use App\Services\Mailer\Mailer;
use App\Utils\Admin\UserUtils;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserStatusController extends Controller
{
    public function approveUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'all_updates' => ['sometimes']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        if ($user->status === UserEnum::STATUS_DELETED) {
            return redirect()->back()->with(['fail' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if ($user->status === UserEnum::STATUS_ACCEPTED) {
            return redirect()->back()->with(['fail' => 'User already approved'], Response::HTTP_BAD_REQUEST);
        }

        if($user->admin_check){

            // not good code.
            $updates = $user->updated_fields;

            if ($request->has('all_updates') and $request->boolean('all_updates')) {

                UserUtils::approveAllUpdates($user, false);

            } elseif (count(array_unique($updates)) === 2 && $updates['id']) {
                //This if check if the only update is the ID. That means the ID card must be verified manually. It can't be approved with others updates

                switch ($user->status) {
                    case 'new':
                    case 'pending':
                        break;
                    default:
                        return redirect()->back()->with(['fail' => 'You must verify manually the ID card']);
                }
            } else {
                return redirect()->back()->with(['fail' => 'Please, check all data before approving or check the checkbox on the approve button']);
            }
        }

        $reason = null;
        if ($user->status === 'pending' && !isset($user->email_verified_at)) {
            $reason = "Email verified";
            $user->update(['email_verified_at' => now()]);
        }

        $user->createUserStatusHistory(UserEnum::STATUS_ACCEPTED, Auth::user()->username, $reason);
        $user->update(['status' => UserEnum::STATUS_ACCEPTED, 'admin_id' => Auth::id()]);

        if (isset($user->referred_by)) {
            $referring_user = User::find($user->referred_by);
            if (isset($referring_user)) {

                $rookie_path = Path::query()->select('paths.name')
                        ->join('users_paths', 'users_paths.path_id', '=', 'paths.id')
                        ->where('users_paths.user_id', $user->id)
                        ->where('paths.is_subpath', false)
                        ->first()
                        ->name ?? '';
                try {
                    $misc = [
                        'rookie_name' => Rookie::find($user->id)->first_name,
                        'leader_name' => $referring_user->username,
                        'rookie_avatar' => $user->getOwnAvatar()->url ?? null,
                        'rookie_profile_url' => env('FRONTEND_URL') . "/{$user->username}",
                        'rookie_path' => $rookie_path
                    ];

                    Chat::config($referring_user->id)->startDirectChat($referring_user, $user, null, null, true);

                    Mailer::create($referring_user)
                        ->setMisc($misc)
                        ->setTemplate('ROOKIE_JOINED_FROM_LEADER_REFER')
                        ->sendAndCreateUserEmailSentRow();

                    NotificationUtils::sendNotification($user->id, "leader_referred_rookie_welcome", now(),
                        ['ref_user_id' => $referring_user->id]);
                    NotificationUtils::sendNotification($referring_user->id, "leader_referred_rookie", now(),
                        ['ref_user_id' => $user->id]);

                } catch (\Exception $exception) {
                }
            }
        }

        NotificationUtils::sendNotification($user->id, 'user_accepted', now());
        try {
            EventAdminAcceptedUser::config($user->id);
        } catch (\Exception $exception) {
        }

        return redirect()->back()->with(['success' => 'Account approved']);
    }

    public function approveAllUpdates(Request $request, User $user){
        $validator = Validator::make($request->all(), [
            'all_updates' => ['accepted']
        ], [
            'all_updates.accepted' => 'Check the checkbox before proceed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        if ($user->status === UserEnum::STATUS_DELETED) {
            return redirect()->back()->with(['fail' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        try {

            UserUtils::approveAllUpdates($user);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => "Couldn't approve all the updates, something went wrong, please check it manually; error Details: " . $exception->getMessage()]);
        }

        return redirect()->back()->with(['success' => "User's updates approved"]);
    }

    public function declineUser(Request $request, User $user){
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required'],
            'all_updates' => ['sometimes']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        if ($user->status === UserEnum::STATUS_DELETED) {
            return redirect()->back()->with(['fail' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if ($user->status === UserEnum::STATUS_REJECTED) {
            return redirect()->back()->with(['fail' => 'User already rejected'], Response::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {

            UserUtils::declineAllUpdates($user, $request->decline_reason, false);

            UserRejectHistory::query()->create([
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'reason' => $request->decline_reason
            ]);

            $user->createUserStatusHistory(UserEnum::STATUS_REJECTED, Auth::user()->username);
            $user->update(['status' => UserEnum::STATUS_REJECTED, 'decline_reason' => $request->decline_reason, 'admin_id', Auth::id()]);

            DB::commit();

        }catch (\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with(['fail' => "Couldn't reject the updates, something went wrong, please check it manually; error Details: " . $exception->getMessage()]);
        }

        NotificationUtils::sendNotification($user->id, 'user_declined', now(), [
            'reason' => ReasonUtils::ALL_REASON[$request->decline_reason] ?? $request->decline_reason
        ]);

        return redirect()->back()->with(['success' => 'User rejected']);
    }

    public function declineAllUpdates(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'decline_reason' => ['required'],
            'all_updates' => ['accepted']
        ], [
            'all_updates.accepted' => 'Check the checkbox before proceed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        if ($user->status === UserEnum::STATUS_DELETED) {
            return redirect()->back()->with(['fail' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        try {

            UserUtils::declineAllUpdates($user, $request->decline_reason);
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => "Couldn't reject the updates, something went wrong, please check it manually; error Details: " . $exception->getMessage()]);
        }

        return redirect()->back()->with(['success' => "User's updates rejected"]);
    }
}
