<select class="form-control" id="name" name="name">
	<option value="">Choose...</option>
	@foreach ($crs as $cr)
		<option value="{{ $cr->id }}" >{{ $cr->cr_no }} - ({{ $cr->application->name }}) - ({{ $cr->description }})</option>
	@endforeach
</select>