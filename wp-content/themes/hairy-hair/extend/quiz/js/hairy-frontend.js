jQuery(document).ready(function ($) {
  if (typeof hairyQuiz === "undefined") {
    console.error(
      "hairyQuiz object not found. Is hairy-frontend.js localized correctly?"
    );
    return;
  }

  $(".hairy-datepicker").datepicker({
    dateFormat: "dd-mm-yy",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:2007",
    maxDate: new Date(2007, 11, 31),
  });

  const $quizForm = $("#hairy-frontend-quiz-form");
  const $quizMessage = $("#hairy-quiz-message");
  const $quizResultsDisplay = $("#hairy-quiz-results-display");
  const $quizScore = $("#hairy-quiz-score");
  const $quizRecommendations = $("#hairy-quiz-recommendations");
  const $submitButton = $("#hairy-submit-quiz-button");
  const $optionCard = $(".option-card");

  function displayFrontendMessage(message, type) {
    $quizMessage
      .removeClass("success error")
      .addClass(type)
      .text(message)
      .fadeIn();
  }

  $optionCard.on("click", function () {
    $quizMessage.fadeOut();
  });

  $quizForm.on("submit", function (e) {
    e.preventDefault();

    let isValid = true;
    var count = 1;

    $(".hairy-quiz-question").each(function () {
      if ($(this).hasClass('hairy-quiz-question-checkbox')) return;

      const questionId = $(this).data("question_id");
      const count_id = $(this).data("count_id"); // Only needed if you're using it later

      const isSelected = $(`input[name="question_${questionId}"]:checked`).val();

      if (!isSelected) {
        isValid = false;
        return false; // Breaks out of `.each()` early
      }

      count++;
    });


    if (!isValid) {
      displaySteps(count);
      displayFrontendMessage(hairyQuiz.required_fields_message, "error");
      return;
    } else {
      $quizMessage.fadeOut();
    }

    $submitButton.prop("disabled", true).text("Submitting...");

    const formData = $quizForm.serialize();
    const quizType = $quizForm.data("quiz-type");
    const finalFormData =
      formData + "&quiz_type=" + encodeURIComponent(quizType);

    $.ajax({
      url: hairyQuiz.ajax_url,
      type: "POST",
      data: finalFormData,
      success: function (response) {
        // Stay disabled after success (the page redirects); re-enable only on error.
        if (!response.success) {
          $submitButton.prop("disabled", false).text("Submit Quiz");
        }

        displayFrontendMessage(response.data.message, "success");

        if (response.success) {
          if (window.location.href.includes("localhost")) {
            window.location.href =
              "http://localhost/hairy-hair/my-account/quiz/";
          } else {
            window.location.href = window.location.origin + "/my-account/quiz/";
          }
        } else {
          displayFrontendMessage(response.data.message, "error");
        }
      },
      error: function () {
        $submitButton.prop("disabled", false).text("Submit Quiz");
        displayFrontendMessage(hairyQuiz.quiz_error_message, "error");
      },
    });
  });

  $quizForm.on("input change", ".invalid-field", function () {
    $(this).removeClass("invalid-field");
  });

  $quizForm.on(
    "change",
    '.hairy-quiz-question input[type="radio"]',
    function () {
      $(this)
        .closest(".hairy-quiz-question")
        .removeClass("invalid-question-section");
    }
  );
});

$(document).ready(function () {
  let currentStep = 1;
  const totalSteps = $("#multi-step-form .step").length;

  function updateStepsProgressBar() {
    const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
    $(".progress-bar").css("width", progressPercentage + "%");
  }

  window.displaySteps = function (stepNumber) {
    if (stepNumber < 1 || stepNumber > totalSteps) return;

    currentStep = stepNumber;

    $(".step").hide();
    $(".step-" + currentStep).show();

    const isLastStep = currentStep === totalSteps;
    const isFirstStep = currentStep === 1;

    $(".prev-step-btn").toggle(!isFirstStep);
    $(".next-step-btn")
      .html(isLastStep ? "Submit" : "Next")
      .attr("type", isLastStep ? "submit" : "button");

    updateStepsProgressBar();
  };

  $(".next-step-btn").click(function () {
    if (currentStep < totalSteps) {
      // Validate required fields in the lead form before advancing
      var $leadForm = $("#hairy-frontend-get-started-form");
      if ($leadForm.length) {
        var isStepValid = true;
        var $currentStepEl = $(".step-" + currentStep);
        var $leadMessage = $("#hairy-lead-message");

        // Check all required inputs and selects in the current step
        $currentStepEl.find("input.required, select.required").each(function () {
          if ($(this).val().trim() === "") {
            isStepValid = false;
            $(this).addClass("error");
          }
        });

        // Required radio groups (marked data-required), e.g. age group and current hair loss.
        $currentStepEl.find('input[type="radio"][data-required]').each(function () {
          var $question = $(this).closest(".hair-loss-question");
          var answered = $currentStepEl.find('input[name="' + this.name + '"]:checked').length > 0;
          $question.toggleClass("error", !answered);
          if (!answered) {
            isStepValid = false;
          }
        });

        if (!isStepValid) {
          $leadMessage
            .removeClass("success error")
            .addClass("error")
            .text(hairyQuiz.required_fields_message)
            .fadeIn();
          return;
        }

        // Step 1: phone must be exactly 9 digits before advancing
        if (currentStep === 1) {
          var $phoneField = $leadForm.find('[name="phone"]');
          var phoneDigits = ($phoneField.val() || '').replace(/\D/g, '');
          if (phoneDigits.length !== 10 || phoneDigits[0] !== '0') {
            $phoneField.addClass('error');
            $leadMessage
              .removeClass('success error')
              .addClass('error')
              .text('Please enter a valid 10-digit Australian phone number starting with 0 (e.g. 0412345678).')
              .fadeIn();
            return;
          }
        }

        $leadMessage.hide();

        // Steps 1 and 2 are saved on the server before moving on (one Lead per email).
        if (currentStep === 1 || currentStep === 2) {
          hhSaveLeadStep($leadForm, currentStep, $(this), $leadMessage, goToStep);
          return;
        }
      }

      goToStep(currentStep + 1);
    }
  });

  function goToStep(target) {
    if (target <= currentStep || target > totalSteps) {
      return;
    }
    $(".step-" + currentStep).addClass("animate__animated animate__fadeOutLeft");
    setTimeout(function () {
      $(".step").removeClass("animate__animated animate__fadeOutLeft").hide();
      currentStep = target;
      $(".step-" + currentStep)
        .show()
        .addClass("animate__animated animate__fadeInRight");
      displaySteps(currentStep);
    }, 500);
  }

  $(".prev-step-btn").click(function () {
    if (currentStep > 1) {
      $(".step-" + currentStep).addClass(
        "animate__animated animate__fadeOutRight"
      );

      setTimeout(function () {
        $(".step")
          .removeClass("animate__animated animate__fadeOutRight")
          .hide();
        currentStep--;
        $(".step-" + currentStep)
          .show()
          .addClass("animate__animated animate__fadeInLeft");

        displaySteps(currentStep);
      }, 500);
    }
  });

  displaySteps(1);

  // Step 3: show an explanation box only while its question is answered "Yes".
  // Typed text is kept when hidden (the server ignores it unless the answer is "Yes").
  function syncExplanations() {
    $(".hh-explanation").each(function () {
      var question = $(this).data("explains");
      var answer = $('input[name="' + question + '"]:checked').val();
      $(this).toggle(answer === "Yes");
    });
  }
  $(document).on("change", 'input[name="allergies_or_sensitivities"], input[name="taking_any_medications"]', syncExplanations);
  syncExplanations();
});

function attachFieldValidation($form) {
  $form.find("input, select, textarea").on("blur change", function () {
    validateField($(this));
  });
}

function attachFieldValidationonSubmit($form) {
  $form.find("input, select, textarea").each(function () {
    validateField($(this));
  });
}

function validateField($field) {
  const value = $field.val().trim();
  let hasError = false;
  $field.removeClass("error");

  if ($field.hasClass("required") && value === "") {
    hasError = true;
  }

  if (
    $field.hasClass("alpha") &&
    value !== "" &&
    !/^[a-zA-Z\s]+$/.test(value)
  ) {
    hasError = true;
  }

  if (
    $field.hasClass("alphanumeric") &&
    value !== "" &&
    !/^[a-z0-9]+$/i.test(value)
  ) {
    hasError = true;
  }

  if (
    $field.hasClass("numeric") &&
    value !== "" &&
    !/^[\d()+\s-]+$/.test(value)
  ) {
    hasError = true;
  }

  if (
    $field.hasClass("numeric") &&
    value !== "" &&
    !/^[\d()+\s-;]+$/.test(value)
  ) {
    hasError = true;
  }

  if ($field.attr("type") === "email" && value !== "") {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      hasError = true;
    }
  }

  if (hasError) {
    $field.addClass("error");
  }
}

$(document).ready(function () {
  const $leadForm = $("#hairy-frontend-get-started-form");
  const $leadMessage = $("#hairy-lead-message");
  const $leadSubmitButton = $("#hairy-submit-lead-button");
  attachFieldValidation($leadForm);

  function displayFrontendLeadMessage(message, type) {
    $leadMessage
      .removeClass("success error")
      .addClass(type)
      .text(message)
      .fadeIn();
  }

  $leadForm.find("input").on("click", function (e) {
    $("#hairy-lead-message").hide();
    $(this).removeClass("error");
  });

  $leadForm.find("select").on("change", function () {
    $("#hairy-lead-message").hide();
    $(this).removeClass("error");
  });

  $leadForm.on("submit", function (e) {
    e.preventDefault();

    var step = 1;
    var isValid = true;

    attachFieldValidationonSubmit($leadForm);

    $leadForm.find('input[type="text"], input[type="email"]').each(function () {
      if ($(this).hasClass("required") && $(this).val().trim() === "") {
        isValid = false;
        $(this).addClass("error");
        displaySteps(1);
        displayFrontendLeadMessage(hairyQuiz.required_fields_message, "error");
        return false;
      }
    });

    if (!isValid) return;

    var $phoneField = $leadForm.find('[name="phone"]');
    var phoneDigits = ($phoneField.val() || '').replace(/\D/g, '');
    if (phoneDigits.length !== 10 || phoneDigits[0] !== '0') {
      isValid = false;
      $phoneField.addClass("error");
      displaySteps(1);
      displayFrontendLeadMessage('Please enter a valid 10-digit Australian phone number starting with 0 (e.g. 0412345678).', "error");
      return;
    }

    const hairLossDurationChecked = $leadForm
      .find('input[name="hair-loss-duration"]:checked')
      .val();
    const expectedResultsChecked = $leadForm
      .find('input[name="expacted_results"]:checked')
      .val();
    const ageGroupChecked = $leadForm
      .find('input[name="age_group"]:checked')
      .val();
    const currentHairLossChecked = $leadForm
      .find('input[name="current_hair_loss"]:checked')
      .val();

    if (
      hairLossDurationChecked === undefined ||
      expectedResultsChecked === undefined ||
      ageGroupChecked === undefined ||
      currentHairLossChecked === undefined
    ) {
      isValid = false;
      step = 2;
    }

    if (!isValid) {
      displaySteps(step);
      displayFrontendLeadMessage(hairyQuiz.required_fields_message, "error");
      return;
    }

    const allergiesChecked = $leadForm
      .find('input[name="allergies_or_sensitivities"]:checked')
      .val();
    const medicationsChecked = $leadForm
      .find('input[name="taking_any_medications"]:checked')
      .val();
    const highBloodPressureChecked = $leadForm
      .find('input[name="high_blood_pressure"]:checked')
      .val();

    if (
      allergiesChecked === undefined ||
      medicationsChecked === undefined ||
      highBloodPressureChecked === undefined
    ) {
      isValid = false;
      step = 3;
    }

    if (!isValid) {
      displaySteps(step);
      displayFrontendLeadMessage(hairyQuiz.required_fields_message, "error");
      return;
    }

    // Explanations are required when the related answer is "Yes".
    var missingExplanation = false;
    $leadForm.find(".hh-explanation").each(function () {
      var question = $(this).data("explains");
      var answer = $leadForm.find('input[name="' + question + '"]:checked').val();
      var $text = $(this).find("textarea");
      var missing = answer === "Yes" && $.trim($text.val()) === "";
      $text.toggleClass("error", missing);
      if (missing) {
        missingExplanation = true;
      }
    });
    if (missingExplanation) {
      displaySteps(3);
      displayFrontendLeadMessage("Please add the requested details for each question answered \"Yes\".", "error");
      return;
    }

    $leadSubmitButton.prop("disabled", true).text("Submitting...");

    $.ajax({
      url: hairyQuiz.ajax_url,
      type: "POST",
      data: $leadForm.serialize(),
      success: function (response) {
        // Stay disabled after success (the page redirects); re-enable only on error.
        if (!response.success) {
          $leadSubmitButton.prop("disabled", false).text("Submit Quiz");
        }

        if (response.success) {
          if (response.data && response.data.message) {
            displayFrontendLeadMessage(response.data.message, "success");
          }
          if (window.location.href.includes("localhost")) {
            window.location.href =
              "http://localhost/tridev/hairy-hair/my-account/quiz/";
          } else {
            window.location.href = window.location.origin + "/my-account/quiz/";
          }
        } else {
          var errMsg = (response.data && response.data.message) ? response.data.message : hairyQuiz.quiz_error_message;
          displayFrontendLeadMessage(errMsg, "error");
        }
      },
      error: function () {
        $leadSubmitButton.prop("disabled", false).text("Submit Quiz");
        displayFrontendLeadMessage(hairyQuiz.quiz_error_message, "error");
      },
    });
  });
});

 
$(document).ready(function() {
  $('.step-14 input').on('change', function() {
    $('.question-file-input').hide()
    if ($(this).val() === '52') {
      $('.question-file-input').show()
    }
  });
});

// Phone field: digits only, exactly 10 digits starting with 0 (Australian format)
$(document).ready(function () {
  function isValidAusPhone(val) {
    var digits = (val || '').replace(/\D/g, '');
    return digits.length === 10 && digits[0] === '0';
  }

  function updateNextBtnState() {
    // Personal-information form Next button
    var $piNext = $('.next-step-one-button');
    if (isValidAusPhone($('#exampleInputPhone').val())) {
      $piNext.prop('disabled', false).css('opacity', '');
    } else {
      $piNext.prop('disabled', true).css('opacity', '0.5');
    }

    // Quiz form Submit button
    var $quizSubmit = $('#hairy-submit-quiz-button');
    if (isValidAusPhone($('#phone').val())) {
      $quizSubmit.prop('disabled', false).css('opacity', '');
    } else {
      $quizSubmit.prop('disabled', true).css('opacity', '0.5');
    }
  }

  $(document).on('keypress', '#phone, #exampleInputPhone', function (e) {
    if (e.which < 48 || e.which > 57) {
      e.preventDefault();
    }
  });

  $(document).on('input', '#phone, #exampleInputPhone', function () {
    var digits = $(this).val().replace(/\D/g, '');
    if (digits.length > 10) {
      digits = digits.slice(0, 10);
    }
    $(this).val(digits);
    updateNextBtnState();
  });

  $(document).on('blur', '#phone, #exampleInputPhone', function () {
    if (!isValidAusPhone($(this).val())) {
      $(this).addClass('error');
    } else {
      $(this).removeClass('error');
    }
  });

  // Set initial state on page load
  updateNextBtnState();
});

// Server-issued submission token (fetched via admin-ajax so cached pages still get a fresh one).
// Re-sent unchanged on retries, so the server can recognise a repeated request and return the
// saved result instead of storing it twice.
jQuery(function ($) {
  $("#hairy-frontend-quiz-form, #hairy-frontend-get-started-form").each(function () {
    var $form = $(this);
    var $input = $form.find('input[name="hh_submission_token"]');
    if (!$input.length) {
      $input = $('<input type="hidden" name="hh_submission_token" value="">').appendTo($form);
    }
    if (typeof hairyQuiz === "undefined") {
      return;
    }
    $.post(hairyQuiz.ajax_url, { action: "hh_submission_token" }).done(function (r) {
      if (r && r.success && r.data && r.data.token) {
        $input.val(r.data.token);
      }
    });
  });
});

// Lead form Steps 1 and 2: save on the server, then continue according to the structured response.
// One request at a time (double clicks are ignored); inputs are never cleared on failure.
function hhSaveLeadStep($form, step, $button, $message, goToStep) {
  var $ = jQuery;
  if ($form.data("hhSaving")) {
    return;
  }
  $form.data("hhSaving", true);
  $button.prop("disabled", true);

  var data = $.map($form.serializeArray(), function (f) {
    return f.name === "action" ? null : f;
  });
  data.push({ name: "action", value: "hairy_lead_step" }, { name: "lead_step", value: step });

  function show(text, type) {
    $message.removeClass("success error").addClass(type).text(text).fadeIn();
  }
  function restore(answers) {
    // Only fill questions the customer has not answered on this page.
    $.each(answers || {}, function (name, value) {
      var $radio = $form.find('input[type="radio"][name="' + name + '"]');
      if ($radio.length) {
        if (!$radio.filter(":checked").length) {
          $radio.filter(function () { return this.value === value; }).prop("checked", true).trigger("change");
        }
        return;
      }
      var $text = $form.find('textarea[name="' + name + '"]');
      if ($text.length && $.trim($text.val()) === "") {
        $text.val(value);
      }
    });
  }

  $.ajax({ url: hairyQuiz.ajax_url, type: "POST", data: $.param(data) })
    .done(function (r) {
      var d = (r && r.data) || {};
      if (r && r.success) {
        restore(d.answers);
        var next = step === 1 && d.next_step === 3 ? 3 : step + 1;
        goToStep(next);
        return;
      }
      // already_completed, verification_required, needs_review, restart or a validation error.
      show(d.message || hairyQuiz.quiz_error_message, "error");
    })
    .fail(function () {
      show(hairyQuiz.quiz_error_message, "error");
    })
    .always(function () {
      $form.data("hhSaving", false);
      $button.prop("disabled", false);
    });
}
