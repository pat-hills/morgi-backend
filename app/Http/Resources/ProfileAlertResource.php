<?php

namespace App\Http\Resources;

use App\Models\ProfileAlertCode;
use App\Models\Subscription;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileAlertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $profile_alert_code = ProfileAlertCode::query()->find($this->code_id);

        $response = [
            'code' => $profile_alert_code->code ?? null,
            'message' => $profile_alert_code->message ?? null
        ];

        if($profile_alert_code->code==='PA_LEADER_002'){

            $response['subscriptions_failed_count'] = Subscription::query()
                ->where('leader_id', $this->user_id)
                ->where('status', 'failed')
                ->count();
        }

        return $response;
    }
}
