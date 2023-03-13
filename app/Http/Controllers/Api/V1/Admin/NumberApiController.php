<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNumberRequest;
use App\Http\Requests\UpdateNumberRequest;
use App\Http\Resources\Admin\NumberResource;
use App\Models\Number;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\Admin\CampaignResource;
use App\Models\Campaign;
use Carbon\Carbon;

class NumberApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('number_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new NumberResource(Number::with(['campaign', 'created_by'])->get());
    }

    public function store(StoreNumberRequest $request)
    {
        $number = Number::create($request->all());

        return (new NumberResource($number))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Number $number)
    {
        // abort_if(Gate::denies('number_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new NumberResource($number->load(['campaign', 'created_by']));
    }

    public function update(UpdateNumberRequest $request, Number $number)
    {
        $number->update($request->all());

        return (new NumberResource($number))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Number $number)
    {
        abort_if(Gate::denies('number_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $number->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    public function getNumbers($id)
    {
        $number = new Number;
        $campaign = new Campaign;
        $campaigndata = $campaign->where('id','=',$id)->get();
        $result = $number->where('usecount', '<', $campaigndata[0]->dedup)->where('campaign_id','=',$id)->get();
        $counterSet = $number->where('usecount', '=', $campaigndata[0]->dedup)->where('campaign_id','=',$id)->get();
        //if there is no numbers less then set limit usecount
        
        if(count($result) == 0){
            foreach($counterSet as $set){
                $timenow = Carbon::now();
                $start_time = new Carbon($set->firstseen);
                $end_time = Carbon::now();
                $time_difference_in_minutes = $end_time->diffInMinutes($start_time);
                // return $campaigndata;
                if($time_difference_in_minutes > ($campaigndata[0]->dedup_limit*60)){
                    $makezero = Number::find($set->id);
                    $makezero->usecount = 1;
                    $makezero->firstseen = $makezero->freshTimestamp();
                    $makezero->save();
                    return $set->number;
                }
                else{
                    return 0;
                }
            }
        }
        else {
            // checking if the 0 use count number is available or not
            foreach($result as $r){
                if($r->usecount == 0)
                {
                    $up = Number::find($r->id);
                    $up->increment('usecount');
                    $up->firstseen = $up->freshTimestamp();
                    $up->save();
                    return $r->number;
                }
            }
            $up = Number::find($result[0]->id);
            $up->increment('usecount');
            $up->save();
            return $result[0]->number;
            
        }

    }
}
