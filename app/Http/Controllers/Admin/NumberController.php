<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyNumberRequest;
use App\Http\Requests\StoreNumberRequest;
use App\Http\Requests\UpdateNumberRequest;
use App\Models\Campaign;
use App\Models\Number;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class NumberController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('number_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Number::with(['campaign', 'created_by'])->select(sprintf('%s.*', (new Number)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'number_show';
                $editGate      = 'number_edit';
                $deleteGate    = 'number_delete';
                $crudRoutePart = 'numbers';

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
            $table->editColumn('number', function ($row) {
                return $row->number ? $row->number : '';
            });
            $table->editColumn('usecount', function ($row) {
                return $row->usecount ? $row->usecount : '';
            });
            $table->addColumn('campaign_name', function ($row) {
                return $row->campaign ? $row->campaign->name : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'campaign']);

            return $table->make(true);
        }

        $campaigns = Campaign::get();
        $users     = User::get();

        return view('admin.numbers.index', compact('campaigns', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('number_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaigns = Campaign::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.numbers.create', compact('campaigns'));
    }

    public function store(StoreNumberRequest $request)
    {
        $number = Number::create($request->all());

        return redirect()->route('admin.numbers.index');
    }

    public function edit(Number $number)
    {
        abort_if(Gate::denies('number_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $campaigns = Campaign::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $number->load('campaign', 'created_by');

        return view('admin.numbers.edit', compact('campaigns', 'number'));
    }

    public function update(UpdateNumberRequest $request, Number $number)
    {
        $number->update($request->all());

        return redirect()->route('admin.numbers.index');
    }

    public function show(Number $number)
    {
        abort_if(Gate::denies('number_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $number->load('campaign', 'created_by', 'numberCampaigns');

        return view('admin.numbers.show', compact('number'));
    }

    public function destroy(Number $number)
    {
        abort_if(Gate::denies('number_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $number->delete();

        return back();
    }

    public function massDestroy(MassDestroyNumberRequest $request)
    {
        $numbers = Number::find(request('ids'));

        foreach ($numbers as $number) {
            $number->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
