/*!
 * jQuery QueryBuilder Select2 Filters Enhancement
 * Makes the field/column selector dropdown searchable using Select2
 */

(function($) {
    'use strict';

    // Wait for document ready and QueryBuilder to be available
    $(document).ready(function() {
        // Add Select2 to existing QueryBuilder instances (exclude comparison builder)
        function enhanceQueryBuilderWithSelect2() {
            $('.query-builder').not('#query-builder-original').each(function() {
                var $builder = $(this);
                var instance = $builder.data('queryBuilder');
                
                if (instance && $.fn.select2) {
                    var select2Options = {
                        theme: 'bootstrap',
                        width: 'resolve',
                        placeholder: 'Select field...',
                        allowClear: false,
                        escapeMarkup: function(markup) {
                            return markup;
                        },
                        dropdownAutoWidth: true
                    };

                    // Apply Select2 to existing filter dropdowns
                    $builder.find('.rule-filter-container select').each(function() {
                        var $select = $(this);
                        if (!$select.hasClass('select2-hidden-accessible')) {
                            $select.removeClass('form-control');
                            $select.select2(select2Options);
                        }
                    });

                    // Hook into QueryBuilder events if possible
                    if (instance.on) {
                        instance.on('afterCreateRuleFilters.select2', function(e, rule) {
                            var $select = rule.$el.find('.rule-filter-container select');
                            if ($select.length && !$select.hasClass('select2-hidden-accessible')) {
                                $select.removeClass('form-control');
                                $select.select2(select2Options);
                            }
                        });

                        instance.on('afterAddRule.select2', function(e, rule) {
                            var $select = rule.$el.find('.rule-filter-container select');
                            if ($select.length && !$select.hasClass('select2-hidden-accessible')) {
                                $select.removeClass('form-control');
                                $select.select2(select2Options);
                            }
                        });

                        instance.on('beforeDeleteRule.select2', function(e, rule) {
                            var $select = rule.$el.find('.rule-filter-container select');
                            if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                                $select.select2('destroy');
                            }
                        });
                    }
                }
            });
        }

        // Enhanced version - watch for dynamically added QueryBuilder elements
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    $(mutation.addedNodes).find('.rule-filter-container select').each(function() {
                        var $select = $(this);
                        if ($.fn.select2 && !$select.hasClass('select2-hidden-accessible')) {
                            $select.removeClass('form-control');
                            $select.select2({
                                theme: 'bootstrap',
                                width: 'resolve',
                                placeholder: 'Select field...',
                                allowClear: false,
                                dropdownAutoWidth: true
                            });
                        }
                    });
                }
            });
        });

        // Start observing
        setTimeout(function() {
            enhanceQueryBuilderWithSelect2();
            
            // Observe the QueryBuilder container for dynamic changes (exclude comparison builder)
            $('.query-builder').not('#query-builder-original').each(function() {
                observer.observe(this, {
                    childList: true,
                    subtree: true
                });
            });
        }, 100);
    });

})(jQuery);