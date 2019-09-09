{{-- Select2 Multiple Backpack CRUD filter --}}

<li filter-name="{{ $filter->name }}"
	filter-type="{{ $filter->type }}"
	class="nav-item dropdown {{ Request::get($filter->name)?'active':'' }}">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
    <div class="dropdown-menu p-0">
      <div class="form-group backpack-filter mb-0">
			<select id="filter_{{ $filter->name }}" name="filter_{{ $filter->name }}" data-filter-name="{{ $filter->name }}" class="form-control input-sm select2" data-filter-type="select2_multiple" placeholder="{{ $filter->placeholder }}" multiple>
				@if (is_array($filter->values) && count($filter->values))
					@foreach($filter->values as $key => $value)
						<option value="{{ $key }}"
							@if($filter->isActive() && json_decode($filter->currentValue) && array_search($key, json_decode($filter->currentValue)) !== false)
								selected
							@endif
							>
							{{ $value }}
						</option>
					@endforeach
				@endif
			</select>
		</div>
    </div>
  </li>

{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}

{{-- FILTERS EXTRA CSS  --}}
{{-- push things in the after_styles section --}}

@push('crud_list_styles')
    <!-- include select2 css-->
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
	  .form-inline .select2-container {
	    display: inline-block;
	  }
	  .select2-drop-active {
	  	border:none;
	  }
	  .select2-container .select2-choices .select2-search-field input, .select2-container .select2-choice, .select2-container .select2-choices {
	  	border: none;
	  }
	  .select2-container-active .select2-choice {
	  	border: none;
	  	box-shadow: none;
	  }
    </style>
@endpush


{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_list_scripts')
	<!-- include select2 js-->
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    @if (app()->getLocale() !== 'en')
    <script src="{{ asset('packages/select2/dist/js/i18n/' . app()->getLocale() . '.js') }}"></script>
    @endif
    
    <script>
        jQuery(document).ready(function($) {
            // trigger select2 for each untriggered select2 box
            $('select[data-filter-type=select2_multiple]').not('[data-filter-enabled]').each(function () {
            	var filterName = $(this).attr('data-filter-name');

                $(this).select2({
                	allowClear: true,
					closeOnSelect: false,
					theme: "bootstrap",
                });

                $(this).change(function() {
	                var value = '';
	                if ($(this).val() !== null) {
	                    // clean array from undefined, null, "".
	                    var values = $(this).val().filter(function(e){ return e === 0 || e });
	                    // stringify only if values is not empty. otherwise it will be '[]'.
	                    value = values.length !== 0 ? JSON.stringify(values) : '';
	                }

					var parameter = '{{ $filter->name }}';

			    	// behaviour for ajax table
					var ajax_table = $("#crudTable").DataTable();
					var current_url = ajax_table.ajax.url();
					var new_url = addOrUpdateUriParameter(current_url, parameter, value);

					// replace the datatables ajax url with new_url and reload it
					new_url = normalizeAmpersand(new_url.toString());
					ajax_table.ajax.url(new_url).load();

					// add filter to URL
					crud.updateUrl(new_url);

					// mark this filter as active in the navbar-filters
					if (URI(new_url).hasQuery(filterName, true)) {
						$("li[filter-name="+filterName+"]").removeClass('active').addClass('active');
					}
					else
					{
						$("li[filter-name="+filterName+"]").trigger("filter:clear");
					}
				});

				// when the dropdown is opened, autofocus on the select2
				$("li[filter-name="+filterName+"]").on('shown.bs.dropdown', function () {
					$('#filter_'+filterName+'').select2('open');
				});

				// clear filter event (used here and by the Remove all filters button)
				$("li[filter-name="+filterName+"]").on('filter:clear', function(e) {
					// console.log('select2 filter cleared');
					$("li[filter-name="+filterName+"]").removeClass('active');
					$("li[filter-name="+filterName+"] select").select2("val", null);
				});
            });
		});
	</script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}