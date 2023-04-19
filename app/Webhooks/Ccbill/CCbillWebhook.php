<?php


namespace App\Webhooks\Ccbill;


use App\Models\CcbillPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CCbillWebhook
{
    public function store(Request $request)
    {
        try {
            $metadata = ($request->has('X-metadata'))
                ? json_decode(Crypt::decryptString($request->get('X-metadata')), false)
                : null;
            $request->metadata = $metadata;
        }catch (\Exception $e){
            return response()->json(['message' => "Error to decrypt metadata", 'exception' => $e->getMessage()], 400);
        }

        if(isset($metadata)){

            $ccbill_payload = CcbillPayload::query()->where('uuid', $metadata->uuid)->first();
            if(isset($ccbill_payload) && !isset($ccbill_payload->error)){
                return response()->json(['message' => "WebHook already processed already runned"]);
            }
        }

        if(!isset($ccbill_payload)){
            $ccbill_payload = CcbillPayload::query()->create([
                'payload' => json_encode($request->toArray()),
                'uuid' => (isset($metadata))
                    ? $metadata->uuid
                    : null
            ]);
        }

        if(!$request->has('X-metadata') && in_array($request->eventType, ['NewSaleFailure', 'NewSaleSuccess'])){
            $ccbill_payload->update(['error' => 'error, no metadata', 'workflow_completed' => false]);
            return response()->json(['message' => 'error, no metadata'], 400);
        }

        try {

            $eventType = strtolower($request->eventType);

            $type = $metadata->type ?? null;
            $leader_id = $metadata->leader_id ?? null;

            if($eventType==='newsalesuccess'){
                CCbillWebhookHandler::handleNewSaleSuccess($type, $leader_id, $request);
                return response()->json([]);
            }

            if($eventType==='newsalefailure'){
                CCbillWebhookHandler::handleNewSaleFailure($type, $leader_id, $request);
                return response()->json([]);
            }

            if(in_array($eventType, ['cancellation', 'void', 'refund', 'return', 'chargeback'])){
                CCbillWebhookHandler::handleRefund($eventType, $request);
                return response()->json([]);
            }

            $ccbill_payload->update(['error' => 'Error, failed to fetch type', 'workflow_completed' => false]);
            return response()->json(['message' => 'Error, failed to fetch type'], 400);

        }catch (\Exception $e){
            $ccbill_payload->update(['error' => $e->getMessage(), 'workflow_completed' => false]);
            return response()->json(['message' => "Error during the creation of the transactions!", 'exception' => $e->getMessage()], 500);
        }
    }
}
