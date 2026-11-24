{{--
@name - Name of filter
@filters - Active search filters
@options - Collection of value => label
--}}
<div class="form-group mb-s">
    <label for="search-filter-{{ $name }}">{{ trans('entities.search_' . $name) }}</label>
    <select id="search-filter-{{ $name }}" name="filters[{{ $name }}]">
        <option value="">{{ trans('entities.search_filter_any') }}</option>
        @foreach($options as $value => $label)
            <option value="{{ $value }}" @if(($filters[$name] ?? '') === $value) selected @endif>{{ $label }}</option>
        @endforeach
    </select>
</div>