<?php

namespace App\Utils\User\Update;

use App\Logger\Logger;
use App\Mixpanel\Utils\UserProfileUtils;
use App\Models\User;
use App\Models\ChatTopicsUsers;
use App\Models\FavoriteActivitiesUsers;
use App\Utils\StorageUtils;
use Illuminate\Http\Request;

class UserUpdateUtils
{
    private $request;
    private $user;
    private $attributes_to_update = [];
    private $handler;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = $this->request->user();
        $this->handler = ($this->user->type==='rookie')
            ? new RookieUpdateUtils($this->request)
            : new LeaderUpdateUtils($this->request);
    }

    public function update(): User
    {
        $this->removeAvatar();

        try {
            $this->updateChatTopicsFavoriteActivities();
            $this->setAvatar();
            $this->updateUsername();
            $this->updateGenderId();
            $this->updateDescription();
            $this->updateUserActiveStatus();

            $this->handler->update();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        if(count($this->attributes_to_update)>0){
            $this->user->update($this->attributes_to_update);
        }

        try {
            UserProfileUtils::storeOrUpdate($this->user->id);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        

        return $this->user->refresh();
    }

    private function removeAvatar(): void
    {
        if($this->request->remove_avatar){
            $this->user->removeAvatar();
        }
    }

    private function setAvatar(): void
    {
        if($this->request->path_location){
            $this->user->removeAvatar();

            $response = StorageUtils::assignObject($this->request->path_location, 'photo', $this->user);
            if($response['status']==='error'){
                throw new \Exception($response['message']);
            }

            try {
                $this->user->addPhoto($response['path_location'], true);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }
        }
    }

    private function updateUsername(): void
    {
        if(!isset($this->request->username) || $this->request->username===$this->user->username){
            return;
        }

        $username = strtolower(str_replace(
            ' ', '', preg_replace('/[^A-Za-z0-9_.]/', '', $this->request->username)
        ));

        $is_used = User::query()->where('username', $username)->where('id', '!=', $this->user->id)->exists();
        if($is_used){
            throw new \Exception("This username is already in use!");
        }

        $this->attributes_to_update['username'] = $username;
        $this->attributes_to_update['updated_username'] = true;
    }

    private function updateGenderId(): void
    {
        if(!isset($this->request->gender_id) || $this->request->gender_id===$this->user->gender_id){
            return;
        }

        $this->attributes_to_update['gender_id'] = $this->request->gender_id;
    }

    private function updateDescription(): void
    {
        $this->user->setDescription($this->request->description);
    }

    private function updateUserActiveStatus(): void
    {
        $check_user_chat_topics = ChatTopicsUsers::query()->where('users_id', $this->user->id)->exists();
        $check_user_favorite_activities = FavoriteActivitiesUsers::query()->where('users_id', $this->user->id)->exists();

        if($check_user_chat_topics && $check_user_favorite_activities){
            $this->attributes_to_update['active'] = true;
        }
        
    }

    private function updateChatTopicsFavoriteActivities(): void
    {
        $favorite_activities_ids =$this->request->input('favorite_activities_ids');
        $chat_topics_ids =$this->request->input('chat_topics_ids');

        if(isset($chat_topics_ids) && !empty($chat_topics_ids)){
            $this->user->chatTopicsSaved()->attach($chat_topics_ids);
        }

        if(isset($favorite_activities_ids) && !empty($favorite_activities_ids)){
            $this->user->favoriteActivitiesSaved()->attach($favorite_activities_ids);
        }
        
    }
}
