<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
	@foreach($crumbs as $index => $crumb)
		@if($index == count($crumbs) - 1)
			<li class="breadcrumb-item active" aria-current="page">{{ $crumb['name'] }}</li>
		@else
			<li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a></li>
		@endif
	@endforeach
	</ol>
</nav>