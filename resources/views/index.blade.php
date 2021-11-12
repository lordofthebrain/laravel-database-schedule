@extends('schedule::layout.master')

@section('content')
    <div class="container">
        @include('schedule::messages')
        <div class="card">
            <div class="card-header">{{ trans('schedule::schedule.titles.list') }}</div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-sm table-hover">
                    <thead>
                    <tr>
                        <th class="text-center">{{ trans('schedule::schedule.fields.command') }}</th>
                        <th class="text-center">{{ trans('schedule::schedule.fields.arguments') }}</th>
                        <th class="text-center">{{ trans('schedule::schedule.fields.options') }}</th>
                        <th class="text-center">{{ trans('schedule::schedule.fields.expression') }}</th>
                        <th class="text-center">{{ trans('schedule::schedule.fields.status') }}</th>
                        <th class="text-center" width="270">{{ trans('schedule::schedule.fields.actions') }}</th>
                    </tr>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->command }}@if ($schedule->command == 'custom'): {{ $schedule->command_custom }} @endif</td>
                            <td>
                                @if(isset($schedule->params))
                                    @foreach($schedule->params as $param => $value)
                                        @if(isset($value['value']))
                                            {{ $param }}={{ $value['value'] }}{{ $value['type'] === 'function' ? '()' : ''}}<br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if(isset($schedule->options))
                                    @foreach($schedule->options as $option => $value)
                                        @if(!is_array($value) || isset($value['value']))
                                            @if(is_array($value))
                                                --{{ $option }}={{ $value['value'] }}{{ $value['type'] === 'function' ? '()' : ''}}
                                            @else
                                                --{{ $option }}
                                            @endif
                                            <br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ $schedule->expression }}</td>
                            <td class="{{ $schedule->status ? 'text-success' : 'text-secondary' }}">
                                {{ $schedule->status ? trans('schedule::schedule.status.active') : trans('schedule::schedule.status.inactive') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ action('\RobersonFaria\DatabaseSchedule\Http\Controllers\ScheduleController@show', $schedule) }}"
                                   class="btn btn-sm btn-info">
                                    {{ trans('schedule::schedule.buttons.history') }}
                                </a>
                                <a href="{{ action('\RobersonFaria\DatabaseSchedule\Http\Controllers\ScheduleController@edit', $schedule) }}"
                                   class="btn btn-sm btn-primary">
                                    {{ trans('schedule::schedule.buttons.edit') }}
                                </a>
                                @if($schedule->status)
                                    <form action="{{ action('\RobersonFaria\DatabaseSchedule\Http\Controllers\ScheduleController@status', [$schedule, 'status' => 0]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            {{ trans('schedule::schedule.buttons.inactivate') }}
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ action('\RobersonFaria\DatabaseSchedule\Http\Controllers\ScheduleController@status', [$schedule, 'status' => 1]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            {{ trans('schedule::schedule.buttons.activate') }}
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ action('\RobersonFaria\DatabaseSchedule\Http\Controllers\ScheduleController@destroy', $schedule) }}" method="POST" class="d-inline">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        {{ trans('schedule::schedule.buttons.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                {{ trans('schedule::schedule.messages.no-records-found') }}
                            </td>
                        </tr>
                    @endforelse
                    </thead>
                </table>
                <code>
                    {{ trans('schedule::schedule.messages.timezone') }}{{ config('database-schedule.timezone') }}
                </code>
                <div class='d-flex'>
                    <div class='mx-auto'>
                        {{ $schedules->links() }}
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ action('\RobersonFaria\DatabaseSchedule\Http\Controllers\ScheduleController@create') }}"
                   class="btn btn-primary">
                    {{ trans('schedule::schedule.buttons.create') }}
                </a>
            </div>
        </div>
    </div>
@endsection
