/**
 * @file
 * Defines Javascript behaviors for the Entity pilot departure form module.
 */

(function ($) {

  "use strict";

  Drupal.behaviors.entityPilotDepartureForm = {
    attach: function (context) {
      var $context = $(context);
      $context.find('.ep-departure-form-revision').drupalSetSummary(function (context) {
        var $context = $(context);
        var revisionCheckbox = $context.find('.form-item-revision input');

        // Return 'New revision' if the 'Create new revision' checkbox is checked,
        // or if the checkbox doesn't exist, but the revision log does. For users
        // without the "Administer content" permission the checkbox won't appear,
        // but the revision log will if the content type is set to auto-revision.
        if (revisionCheckbox.is(':checked') || (!revisionCheckbox.length && $context.find('.form-item-log textarea').length)) {
          return Drupal.t('New revision');
        }

        return Drupal.t('No revision');
      });

    }
  };

})(jQuery);
