<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyCampaignRequest;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\Number;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CampaignController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('campaign_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Campaign::with(['numbers', 'created_by'])->select(sprintf('%s.*', (new Campaign)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'campaign_show';
                $editGate      = 'campaign_edit';
                $deleteGate    = 'campaign_delete';
                $crudRoutePart = 'campaigns';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? Campaign::STATUS_SELECT[$row->status] : '';
            });
            $table->editColumn('dedup', function ($row) {
                return $row->dedup ? $row->dedup : '';
            });
            $table->editColumn('dedup_limit', function ($row) {
                return $row->dedup_limit ? Campaign::DEDUP_LIMIT_SELECT[$row->dedup_limit] : '';
            });
            $table->editColumn('number', function ($row) {
                $labels = [];
                foreach ($row->numbers as $number) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $number->number);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'number']);

            return $table->make(true);
        }

        $numbers = Number::get();
        $users   = User::get();

        return view('admin.campaigns.index', compact('numbers', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('campaign_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $numbers = Number::pluck('number', 'id');

        return view('admin.campaigns.create', compact('numbers'));
    }

    public function store(StoreCampaignRequest $request)
    {
        $campaign = Campaign::create($request->all());
        $campaign->numbers()->sync($request->input('numbers', []));

        return redirect()->route('admin.campaigns.index');
    }

    public function edit(Campaign $campaign)
    {
        abort_if(Gate::denies('campaign_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $numbers = Number::pluck('number', 'id');

        $campaign->load('numbers', 'created_by');

        return view('admin.campaigns.edit', compact('campaign', 'numbers'));
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $campaign->update($request->all());
        $campaign->numbers()->sync($request->input('numbers', []));

        return redirect()->route('admin.campaigns.index');
    }

    public function show(Campaign $campaign)
    {
        abort_if(Gate::denies('campaign_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaign->load('numbers', 'created_by', 'campaignNumbers');

        return view('admin.campaigns.show', compact('campaign'));
    }

    public function destroy(Campaign $campaign)
    {
        abort_if(Gate::denies('campaign_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaign->delete();

        return back();
    }

    public function massDestroy(MassDestroyCampaignRequest $request)
    {
        $campaigns = Campaign::find(request('ids'));

        foreach ($campaigns as $campaign) {
            $campaign->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
