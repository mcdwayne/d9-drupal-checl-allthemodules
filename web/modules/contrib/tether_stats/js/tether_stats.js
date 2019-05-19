/**
 * @file
 * JavaScript methods for collecting Tether Stats data.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.tetherStats = {

    attach : function (context, settings) {

      if (typeof drupalSettings.tetherStats === 'undefined') {
        return;
      }

      drupalSettings.tetherStats.trackUrl = '/tether-stats/track';
      tetherStatsPrepareTracking();
    }
  };

  /**
   * Called on page load to setup all tracking requirements.
   */
  function tetherStatsPrepareTracking() {

    $('body').once('tether_stats').each(function () {

      // Track the hit on this page.
      tetherStatsTrackPage();
    });

    // Track link clicks.
    $('a.tether_stats-track-link').once('a.tether_stats-track-link').each(function () {

      var url = $(this).attr('href');

      // Test to see if we are opening a new window or tab.
      if ($(this).attr('target') == '_blank') {

        $(this).click(function () {

          $(this).tetherStatsHit({
            'type' : 'click'
          });
        });
      } else {
        $(this).attr('href', '#');

        $(this).click(function (e) {

          e.preventDefault();

          $(this).tetherStatsHit({
            'callback' : function (json) {
              location.href = url;
            },
            'type' : 'click'
          });
        });
      }
    });

    // Track impressions if not tracked in tetherStatsTrackPage().
    $('.tether_stats-track-impress').each(function () {

      $(this).tetherStatsImpress();
      $(this).removeClass('tether_stats-track-impress');
    });
  }

  /**
   * Called on each page. Uses an AJAX callback to track a hit on this page along with any impressions.
   */
  function tetherStatsTrackPage() {

    var settings = drupalSettings.tetherStats;
    var params = new Object();

    if (settings.elid != undefined) {

      params.type = 'hit';
      params.elid = settings.elid;
      params.referrer = document.referrer;

      $('.tether_stats-track-impress').each(function () {

        $(this).tetherStatsImpress();
        $(this).removeClass('tether_stats-track-impress');
      });

      if (settings.impressions == null) {

        settings.impressions = [];
      }

      for (var i = 0; i < settings.impressions.length; i++) {

        params['imp' + i] = tetherStatsObjectToUriParam(settings.impressions[i]);
      }

      $.getJSON(
        settings.trackUrl,
        params,
        tetherStatsTrackCallback
      );
    }
  }

  /**
   * Callback after tracking the current page.
   */
  function tetherStatsTrackCallback(json) {

    if (json.status) {

      drupalSettings.tetherStats.alid = json.alid;
    }
  }

  /**
   * Extract a series of attributes as a stat element identifying set.
   */
  function tetherStatsGetTrackingElementParams(jQueryObject) {

    var params = new Object();
    var identifiers = ['name', 'entity_id', 'entity_type', 'url', 'query', 'derivative'];

    for (var i = 0; i < identifiers.length; i++) {

      var attribute = jQueryObject.attr('data-' + identifiers[i]);

      if (attribute != undefined && attribute.length > 0) {

        params[identifiers[i]] = attribute;
      }
    }

    return (params);
  }

  /**
   * Convert the given object into a url encoded, comma separated list of fields and values.
   */
  function tetherStatsObjectToUriParam(myObject) {

    var uri;
    var elements = new Array();

    for (var key in myObject) {

      if (myObject.hasOwnProperty(key)) {

        elements.push(key + '=' + myObject[key]);
      }
    }
    uri = elements.join(',');
    return encodeURIComponent(uri);
  }

  /**
   * Track an event on the jQuery element.
   *
   * This method must not be called by itself. It will be attached to
   * the prototype jQuery object as it references a jQuery element.
   *
   * Usage: $('selector').tetherStatsHit();
   */
  function tetherStatsHitPrototype () {

    var settings = drupalSettings.tetherStats;
    var params = tetherStatsGetTrackingElementParams($(this));
    var opt = null;

    params.type = 'hit';
    params.referrer = document.referrer;

    if (arguments.length > 0) {

      opt = arguments[0];
    } else {

      opt = new Object();
    }

    if (opt.callback == undefined) {
      opt.callback = function (json) {};
    }

    if (opt.type != undefined) {
      params.type = opt.type;
    }

    $.getJSON(
      settings.trackUrl,
      params,
      opt.callback
    );
  };

  /**
   * Impress the jQuery element on the current page.
   *
   * This method must not be called by itself. It will be attached to
   * the prototype jQuery object as it references a jQuery element.
   *
   * Usage: $('selector').tetherStatsImpress();
   */
  function tetherStatsImpressPrototype () {

    var settings = drupalSettings.tetherStats;
    var params = tetherStatsGetTrackingElementParams($(this));
    var opt = null;

    if (typeof settings.alid !== "undefined") {

      params.type = 'impression';
      params.alid = settings.alid;

      if (arguments.length > 0) {

        opt = arguments[0];
      } else {

        opt = new Object();
      }

      if (opt.callback == undefined) {
        opt.callback = function (json) {};
      }

      $.getJSON(
        settings.trackUrl,
        params,
        opt.callback
      );
    } else {

      if (typeof settings.impressions === "undefined") {
        settings.impressions = [];  
      }
      settings.impressions.push(params);
    }
  };

  // Attach prototype methods to extend the jQuery object.
  jQuery.prototype.tetherStatsHit = tetherStatsHitPrototype;
  jQuery.prototype.tetherStatsImpress = tetherStatsImpressPrototype;

})(jQuery, Drupal, drupalSettings);
