

    function pluginFormatSelection(plugin) {
        return plugin.title;
    }

(function ( $ ) {
	"use strict";

	$(function () {

		function pluginFormatResult(plugin) { console.log(plugin);
        var markup = "<table class='plugin-result'><tr>";
        markup += "<td class='plugin-info'><div class='plugin-title'>" + plugin.name + " by: " + plugin.author + "</div>";
        if (plugin.short_description !== undefined) {
            markup += "<div class='plugin-description'>" + plugin.short_description + "</div>";
        }
        markup += "</td></tr></table>";
        return markup;
    }

		$("#e7").select2({
			placeholder: "Search for a plugin",
			minimumInputLength: 5,
			ajax: {
				url: ajax_object.ajaxurl,
				dataType: 'json',
				quietMillis: 200,
				data: function (term, page) { // page is the one-based page number tracked by Select2
					return {
						search: term, //search term
						action: 'reviewable_plugins',
						page_limit: 10, // page size
						page: page // page number
					};
				},
				results: function (data, page) {
					var more = (page * 10) < data.total; // whether or not there are more results available
		 
					// notice we return the value of more so Select2 knows if more results can be loaded
					return {results: data.plugins, more: more};
				}
			},
			formatResult: pluginFormatResult, // omitted for brevity, see the source of this page
			formatSelection: pluginFormatSelection, // omitted for brevity, see the source of this page
			//dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
			escapeMarkup: function (m) { return m; } // we do not want to escape markup since we are displaying html in results
		});

	});

}(jQuery));