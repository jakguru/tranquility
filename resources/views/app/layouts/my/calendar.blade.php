@extends('app.blueprints.framed')

@section('title')
	{{ __('My Calendar') }}
@endsection

@section('main')
	@include('app.shared.breadcrumbs',['crumbs' => [
		[
			'name' => config('app.name'),
			'url' => route('dashboard'),
		],
		[
			'name' => __('My Calendar'),
			'url' => '#',
		],
	]])
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-2">
				<table class="table table-calendar table-sm table-bordered">
					<thead>
						<tr>
							<th colspan="5">{{ __($params->cmonth->englishMonth) }} {{$params->cmonth->year}}</th>
							<th class="text-center"><a href="{{ \App\Http\Controllers\MyController::makeCalendardLink($params->month == 1 ? $params->year - 1 : $params->year, $params->month == 1 ? 12 : $params->month - 1) }}"><i class="fas fa-chevron-left"></i></a></th>
							<th class="text-center"><a href="{{ \App\Http\Controllers\MyController::makeCalendardLink($params->month == 12 ? $params->year + 1 : $params->year, $params->month == 12 ? 1 : $params->month + 1) }}"><i class="fas fa-chevron-right"></i></a></th>
						</tr>
						<tr>
							@foreach($params->days as $day)
							<th class="text-center">{{ strtoupper(substr(__($day), 0, 1)) }}</th>
							@endforeach
						</tr>
					</thead>
					<tbody>
						@php
						$shownDates = 0;
						@endphp
						@while($shownDates < $params->cmonth->daysInMonth)
						<tr>
							@foreach($params->days as $day)
								@php
								$carbon = Carbon\Carbon::createFromDate($params->year, $params->month, $shownDates + 1);
								@endphp
								@if(strtolower($carbon->englishDayOfWeek) == $day && $shownDates < $params->cmonth->daysInMonth)
									<td class="text-center{{$carbon->isSameDay($params->date) ? ' today' : ''}}">
										<a href="{{ \App\Http\Controllers\MyController::makeCalendardLink($params->year, $params->month, $carbon->toDateTimeString()) }}">{{ $shownDates + 1 }}</a>
									</td>
									@php $shownDates ++; @endphp
								@else
									<td>&nbsp;</td>
								@endif
							@endforeach
						</tr>
						@endwhile
					</tbody>
				</table>
			</div>
			<div class="col-md-10">
				<div class="card">
					<div class="card-header bg-dark text-white">
						<h4 class="mb-0 mt-0">{{ sprintf(__('Schedule for %s'), $params->date->format(is_null(Auth::user()->dateformat) ? config('app.dateformat') : Auth::user()->dateformat      )) }}</h4>
					</div>
					<div class="table-responsive mb-0">
						<table class="table table-sm table-striped table-hover mb-0 table-schedule table-bordered">
							<thead>
								<tr>
									<th class="text-center">{{ __('UTC') }}</th>
									<th class="text-center">{{ ucwords(str_replace('_', ' ', $params->timezone)) }}</th>
									<th class="appointments"></th>
								</tr>
							</thead>
							<tbody>
							@php
							$hour = 0;
							$minute = 0;
							@endphp
							@while( $hour < 24 && $minute < 60 )
							@php
							$format = is_null(Auth::user()->timeformat) ? config('app.timeformat') : Auth::user()->timeformat;
							$dt = new Carbon\Carbon(sprintf('1970-01-01 %s:%s:00', $hour, $minute), $params->timezone);
							$utcdt = $dt->copy()->setTimezone('UTC');
							@endphp
							<tr>
								<td class="text-center">{{ $utcdt->format($format) }}</td>
								<td class="text-center">{{ $dt->format($format) }}</td>
								<td class="appointments"></td>
							</tr>
							@php
							if ( $minute + 15 >= 60 ) {
								$hour ++;
								$minute = 0;
							} else {
								$minute += 15;
							}
							@endphp
							@endwhile
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection