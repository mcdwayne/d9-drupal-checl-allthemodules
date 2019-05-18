(function($) {
  $(document).ready(function() {
    addRow(6);

    $("#gpa-add-row").click(function(e) {
      addRow();
      e.preventDefault();
    });

    $("#gpa-calculator-form").submit(function(e) {
      e.preventDefault();

      var gradePoints = 0.0;
      var totalCredits = 0.0;

      $(".grade_row").each(function(i) {
        if ($(this).find(".grade_field").val() == "" || $(this).find(".credits_field").val() == "") {
          return;
        }

        gradePoints += parseFloat($(this).find(".grade_field").val()) * parseFloat($(this).find(".credits_field").val());
        totalCredits += parseFloat($(this).find(".credits_field").val());
      });

      var gpaCurrent = gradePoints / totalCredits;

      // Previous GPA info.
      var gpaPrev= $('#prev-gpa').val();
      var prevHours = $('#prev-hours').val();
      // Calculate Cumulative GPA.
      var currentSum = gpaCurrent * totalCredits;
      var previousSum = gpaPrev * prevHours;
      var totalHours = +totalCredits + +prevHours;
      var cumulativeGpaPoints = +currentSum + +previousSum;
      var gpaCumulative = cumulativeGpaPoints / totalHours;

      if (gradePoints == 0.0 || totalCredits == 0.0) {
        $("#gpa-current-output").text("You must enter at least one grade and its corresponding credits earned.");
      } else if (isNaN(gpaCurrent) || isNaN(gpaCumulative)) {
        $("#gpa-current-output").text("Could not calculate GPA. Did you input a grade?");
      } else {
        $("#gpa-current-output").html('<span style="font-weight: bold;">Current GPA:</span> ' + gpaCurrent.toFixed(4));
        $("#gpa-cumulative-output").html('<span style="font-weight: bold;">Overall GPA:</span> ' + gpaCumulative.toFixed(4));
      }
    });

    // Only allow numbers in credits_field.
    $('.credits_field, #prev-gpa, #prev-hours').keypress(function(event) {
      return validateNum(event);
    });

  });

  function addRow(numberOfRows) {
    if (!numberOfRows) numberOfRows = 1;

 //   var gradeOptions = loadGPAOptions();

    for (var i = 0; i < numberOfRows; i++) {
      $("#grades_table .gpa-table-body").append(
        $("<div class='gpa-table-row' />").attr("class", "grade_row").append(
          $("<div class='gpa-table-cell' />")
            .css({
              "font-weight": "bold",
              "text-align": "center"
            })
            .text($(".grade_row").length + 1)
        )
        .append(
          $("<div class='gpa-table-cell' />").append(
            $("<input />")
              .attr({
                "type": "text",
                "class": "class_field",
                "name": "class",
              })
          )
        )
        .append(
          $("<div class='gpa-table-cell' />").append(
            $("<select />")
              .attr({
                "class": "grade_field",
                "name": "grade",
              }).append(
                $("<option />").val('').text('')
              )
          )
        )
        .append(
          $("<div class='gpa-table-cell' />").append(
            $("<input />")
              .attr({
                "type": "text",
                "class": "credits_field",
                "name": "credits",
                "size": "3"
              })
          )
        )
      );
    }

    // Append GPA grades.
    gpaOptions();

  }

  /** Get Grades from GPA Calculator settings page **/
  function gpaOptions() {
    var grades_settings_array = drupalSettings.gpa_calculator.gpaCalculator.grades;

    // If grades_settings in config page is empty then
    // load default values.
    // Otherwise load grades entered in config page.
    if (Object.keys(grades_settings_array)[0] == '' || grades_settings_array.length === 0) {
        return $('.grade_field').append(
                $("<option />").val(4.0).text('A')
              ).append(
                $("<option />").val(3.67).text('A-')
              ).append(
                $("<option />").val(3.33).text('B+')
              ).append(
                $("<option />").val(3.0).text('B')
              ).append(
                $("<option />").val(2.67).text('B-')
              ).append(
                $("<option />").val(2.33).text('C+')
              ).append(
                $("<option />").val(2.0).text('C')
              ).append(
                $("<option />").val(1.67).text('C-')
              ).append(
                $("<option />").val(1.33).text('D+')
              ).append(
                $("<option />").val(1.0).text('D')
              ).append(
                $("<option />").val(0.67).text('D-')
              ).append(
                $("<option />").val(0.0).text('F')
              );
    }
    else {
      $.each(grades_settings_array, function(key, value) {
        return $('.grade_field').append($("<option />").val(key).text(value));
      });
    }
  }

  /** Only allow numbers to be entered **/
  function validateNum(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /[0-9]|\./;
    if( !regex.test(key) ) {
      theEvent.returnValue = false;
      if(theEvent.preventDefault) theEvent.preventDefault();
    }
  }
}(jQuery));
