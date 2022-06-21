$(document).ready(function() {

	$('.catalog-filter-wcp .dropdown.hersteller').on('click', 'a.dropdown-item', function(ev) {

		$('.hersteller-name', ev.delegateTarget).text($(ev.target).text());
		$('.catalog-filter-wcp .dropdown.dd-gruppe').addClass('hidden');
		$('#' + $(ev.target).data('id')).removeClass('hidden');

		ev.preventDefault();
	});
});