/**
 * @file
 * Field Operations page behaviors.
 */

(function ($, Drupal, debounce) {

    'use strict';
    Drupal.behaviors.fieldTableFilterByText = {
        attach: function (context, settings) {
            var $input = $('input.table-filter-text').once('table-filter-text');
            var $table = $($input.attr('data-table'));
            var $rowsAndDetails;
            var $rows;
            var $details;
            var searching = false;

            function hidePackageDetails(index, element) {
                var $packDetails = $(element);
                var $visibleRows = $packDetails.find('tbody tr:visible');
                $packDetails.toggle($visibleRows.length > 0);
            }

            function filterFieldList(e) {
                var query = $(e.target).val();
                // Case insensitive expression to find query at the beginning of a word.
                var re = new RegExp('\\b' + query, 'i');

                function showFieldRow(index, row) {
                    var $row = $(row);
                    var $sources = $row.find('.priority-low');
                    var textMatch = $sources.text().search(re) !== -1;
                    $row.closest('tr').toggle(textMatch);
                }
                // Search over all rows and packages.
                $rowsAndDetails.show();

                // Filter if the length of the query is at least 2 characters.
                if (query.length >= 2) {
                    searching = true;
                    $rows.each(showFieldRow);
                    $details.not('[open]').attr('data-drupal-system-state', 'forced-open');
                    $details.attr('open', true).each(hidePackageDetails);
                }
                else if (searching) {
                    searching = false;
                    $rowsAndDetails.show();
                    $details.filter('[data-drupal-system-state="forced-open"]')
                            .removeAttr('data-drupal-system-state')
                            .attr('open', false);
                }
            }

            function preventEnterKey(event) {
                if (event.which === 13) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            }

            if ($table.length) {
                $rowsAndDetails = $table.find('tr, details');
                $rows = $table.find('tbody tr');
                $details = $rowsAndDetails.filter('#field-operations');

                $input.on({
                    keyup: debounce(filterFieldList, 200),
                    keydown: preventEnterKey
                });
            }
        }
    };

}(jQuery, Drupal, Drupal.debounce));