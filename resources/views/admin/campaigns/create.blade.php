@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.campaign.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.campaigns.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.campaign.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.campaign.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.campaign.fields.status') }}</label>
                <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status">
                    <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Campaign::STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('status', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.campaign.fields.status_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="dedup">{{ trans('cruds.campaign.fields.dedup') }}</label>
                <input class="form-control {{ $errors->has('dedup') ? 'is-invalid' : '' }}" type="number" name="dedup" id="dedup" value="{{ old('dedup', '1') }}" step="1" required>
                @if($errors->has('dedup'))
                    <div class="invalid-feedback">
                        {{ $errors->first('dedup') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.campaign.fields.dedup_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.campaign.fields.dedup_limit') }}</label>
                <select class="form-control {{ $errors->has('dedup_limit') ? 'is-invalid' : '' }}" name="dedup_limit" id="dedup_limit" required>
                    <option value disabled {{ old('dedup_limit', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Campaign::DEDUP_LIMIT_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('dedup_limit', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('dedup_limit'))
                    <div class="invalid-feedback">
                        {{ $errors->first('dedup_limit') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.campaign.fields.dedup_limit_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="numbers">{{ trans('cruds.campaign.fields.number') }}</label>
                <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                </div>
                <select class="form-control select2 {{ $errors->has('numbers') ? 'is-invalid' : '' }}" name="numbers[]" id="numbers" multiple>
                    @foreach($numbers as $id => $number)
                        <option value="{{ $id }}" {{ in_array($id, old('numbers', [])) ? 'selected' : '' }}>{{ $number }}</option>
                    @endforeach
                </select>
                @if($errors->has('numbers'))
                    <div class="invalid-feedback">
                        {{ $errors->first('numbers') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.campaign.fields.number_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection