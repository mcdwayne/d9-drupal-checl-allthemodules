/**
 * @file
 * Initializes all modifications.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.modifiers = {

    attach: function attach(context, settings) {
      // Process only if there are some modifiers.
      if (typeof settings.modifiers !== 'undefined') {
        this.initAttributes(context, settings);
        this.initSettings(context, settings);
      }
    },

    initSettings: function (context, settings) {
      // Skip processing if there are no modifications.
      if (typeof settings.modifiers.settings !== 'undefined') {
        var modifications = [];

        // Group all modifications into single array.
        $.each(settings.modifiers.settings, function (index, group) {
          modifications = modifications.concat(group);
        });

        // Process all modifications.
        $.each(modifications, function (index, modification) {
          var callback = window[modification.namespace][modification.callback];
          if (typeof callback === 'function') {
            // Check number of callback arguments.
            if (callback.length > 3) {
              callback(context, modification.selector, modification.media, modification.args);
            }
            else {
              // Callback without context for backward compatibility.
              callback(modification.selector, modification.media, modification.args);
            }
          }
        });
      }
    },

    initAttributes: function (context, settings) {
      // Skip processing if there are no attributes.
      if (typeof settings.modifiers.attributes !== 'undefined') {
        var attributes = {};

        // Group all attributes into single array.
        $.each(settings.modifiers.attributes, function (index, group) {
          $.each(group, function (media, selectors) {
            // Initialize array for this media.
            if (typeof attributes[media] === 'undefined') {
              attributes[media] = {};
            }
            $.each(selectors, function (selector, values) {
              attributes[media][selector] = values;
            });
          });
        });

        // Process all attributes immediately.
        this.toggleAttributes(context, attributes);

        var that = this;
        // Process all attributes again after resize.
        window.addEventListener('resize', function () {
          that.toggleAttributes(context, attributes);
        });
      }
    },

    toggleAttributes: function (context, attributes) {
      var enable = {};
      var disable = {};

      // Check all media queries validity and split selectors to sets.
      $.each(attributes, function (media, selectors) {
        if (window.matchMedia(media).matches) {
          // Fill these selectors for enabling.
          $.each(selectors, function (selector, values) {
            enable[selector] = values;
          });
        }
        else {
          // Fill these selectors for disabling.
          $.each(selectors, function (selector, values) {
            disable[selector] = values;
          });
        }
      });

      // Remove unwanted attributes from target objects.
      $.each(disable, function (selector, values) {
        var element = $(selector, context);
        if (element.length) {
          // Process all attributes.
          $.each(values, function (attribute, value) {
            if (attribute === 'class') {
              $.each(value, function (index, item) {
                element.removeClass(item);
              });
            }
            else {
              element.prop(attribute, null);
            }
          });
        }
      });

      // Set required attributes to target objects.
      $.each(enable, function (selector, values) {
        var element = $(selector, context);
        if (element.length) {
          // Process all attributes.
          $.each(values, function (attribute, value) {
            if (attribute === 'class') {
              $.each(value, function (index, item) {
                element.addClass(item);
              });
            }
            else if (typeof value === 'object') {
              element.prop(attribute, value.join(' '));
            }
            else {
              element.prop(attribute, value);
            }
          });
        }
      });
    }
  };

})(jQuery, Drupal);
