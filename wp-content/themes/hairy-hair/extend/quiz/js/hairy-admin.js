jQuery(document).ready(function($) {
    // Ensure the localized script object exists
    if (typeof hairyQuizAdmin === 'undefined') {
        console.error('hairyQuizAdmin object not found. Is hairy-admin.js localized correctly?');
        return;
    }

    const $form = $('#hairy-question-form');
    const $questionIdField = $('#question_id');
    const $quizTypeSelect = $('#quiz_type_select');
    const $answerTypeSelect = $('#answer_type');
    const $quizTypeNewField = $('#quiz_type_new');
    const $questionSequenceField = $('#question_sequence');
    const $questionTextField = $('#question_text');
    const $questionNoteField = $('#question_note');
    const $answersContainer = $('#answers_container');
    const $addAnswerButton = $('#hairy-add-answer');
    const $saveButton = $('#submit');
    const $cancelEditButton = $('#hairy-cancel-edit');
    const $quizFormContainer = $('#hairy-quiz-form-container');

    // Function to add a new answer input row with text and label select
    function addAnswerField(textValue = '', labelValue = '', isExisting = false) {
        const removeButtonHtml = isExisting ?
            '<button type="button" class="button button-secondary hairy-remove-answer">&times;</button>' :
            '<button type="button" class="button button-secondary hairy-remove-answer" style="display:none;">&times;</button>';

        let labelOptionsHtml = `<option value="">-- Label --</option>`;
        hairyQuizAdmin.answer_labels.forEach(label => {
            const selected = (label === labelValue) ? 'selected' : '';
            labelOptionsHtml += `<option value="${label}" ${selected}>${label}</option>`;
        });

        const answerRow = `
            <div class="answer-row">
                <select name="answers_label[]" class="hairy-answer-label" required>
                    ${labelOptionsHtml}
                </select>
                <input type="text" name="answers_text[]" class="regular-text hairy-answer-input" placeholder="${hairyQuizAdmin.add_answer_placeholder}" value="${textValue}" required>
                ${removeButtonHtml}
            </div>
        `;
        $answersContainer.append(answerRow);

        if (isExisting) {
            $answersContainer.find('.answer-row:last-child .hairy-remove-answer').show();
        }
        updateRemoveButtonsVisibility();
        updateAnswerLabelOptions(); // Update available labels for new row
    }

    // Function to update the visibility of remove buttons
    function updateRemoveButtonsVisibility() {
        const $answerRows = $answersContainer.find('.answer-row');
        if ($answerRows.length <= 1) {
            $answerRows.find('.hairy-remove-answer').hide();
        } else {
            $answerRows.find('.hairy-remove-answer').show();
        }
    }

    // Function to update available labels in answer label selects
    // This ensures unique labels are selected across answer options
    function updateAnswerLabelOptions() {
        const selectedLabels = new Set();
        $answersContainer.find('.hairy-answer-label').each(function() {
            const currentLabel = $(this).val();
            if (currentLabel && currentLabel !== '' && currentLabel !== '0') {
                selectedLabels.add(currentLabel);
            }
        });

        $answersContainer.find('.hairy-answer-label').each(function() {
            const $currentSelect = $(this);
            const currentValue = $currentSelect.val(); // Preserve current value if possible

            $currentSelect.find('option:not([value=""])').remove(); // Remove all labels except the placeholder

            hairyQuizAdmin.answer_labels.forEach(label => {
                // If the label is currently selected in *this* select, or not selected anywhere else, add it
                if (label === currentValue || !selectedLabels.has(label)) {
                    $currentSelect.append(`<option value="${label}">${label}</option>`);
                }
            });

            // Re-select the value if it's still available or set to placeholder
            if ($currentSelect.find(`option[value="${currentValue}"]`).length) {
                $currentSelect.val(currentValue);
            } else {
                $currentSelect.val(''); // Reset if current value is no longer available
            }
        });
    }

    // Call updateAnswerLabelOptions on page load and whenever an answer label changes
    $answersContainer.on('change', '.hairy-answer-label', updateAnswerLabelOptions);


    // Initial answer field
    addAnswerField();
    updateRemoveButtonsVisibility();
    updateAnswerLabelOptions();

    

    // Change answer type
    $form.on('change', '#answer_type', function () {
        const val = $(this).val();
        const optionsWrapper = $('.hairy-answer-options');
        const optionsInputs = optionsWrapper.find('input, select');

        if (val === 'input') {
            optionsInputs.removeAttr('required');
            optionsWrapper.hide();
        } else {
            optionsInputs.attr('required', true);
            optionsWrapper.show();
        }
    });



    // Add another answer field
    $addAnswerButton.on('click', function() {
        if ($answersContainer.find('.answer-row').length < hairyQuizAdmin.answer_labels.length) {
            addAnswerField();
        } else {
            displayMessage('You have reached the maximum number of answer options (A, B, C, D, E).', 'info');
        }
    });

    // Remove answer field
    $answersContainer.on('click', '.hairy-remove-answer', function() {
        if ($answersContainer.find('.answer-row').length > 1) {
            $(this).closest('.answer-row').remove();
            updateRemoveButtonsVisibility();
            updateAnswerLabelOptions(); // Update options after removal
        }
    });

    // Handle Quiz Type select change
    $quizTypeSelect.on('change', function() {
        if ($(this).val() === 'add_new') {
            $quizTypeNewField.show().prop('required', true).focus();
        } else {
            $quizTypeNewField.hide().prop('required', false).val('');
        }
    });


    // Handle form submission (Add/Edit Question)
    $form.on('submit', function(e) {
        e.preventDefault();
 
        const providedAnswerLabels = [];
        let allAnswersValid = true;
        var checkForValidation = true;

        if($(this).find('#answer_type').val() == 'options') {

            $answersContainer.find('.answer-row').each(function() {
                const $labelSelect = $(this).find('.hairy-answer-label');
                const $inputText = $(this).find('.hairy-answer-input');

                if ($labelSelect.val() === '' || $inputText.val().trim() === '') {
                    allAnswersValid = false;
                    return false; // Break .each loop
                }

                if ($labelSelect.val() === '0') {
                    checkForValidation = false;
                }
                providedAnswerLabels.push($labelSelect.val());
            }); 

            if (!allAnswersValid && $(this).find('#answer_type').val() == 'options') {
                displayMessage('Please ensure all answer options have both a text and a unique label selected.', 'error');
                return;
            }

            const uniqueProvidedLabels = new Set(providedAnswerLabels);
            if (uniqueProvidedLabels.size !== providedAnswerLabels.length && checkForValidation) {
                displayMessage('Each answer option must have a unique label (A, B, C...). Duplicate labels are not allowed.', 'error');
                return;
            }

 
        }



        const formData = $form.serialize();

        

        console.log(formData, 'formData')

        $.ajax({
            url: hairyQuizAdmin.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    displayMessage(response.data.message, 'success');
                    // Reload the page to show updated list and quiz types
                    location.reload();
                } else {
                    displayMessage(response.data.message, 'error');
                }
            },
            error: function() {
                displayMessage(hairyQuizAdmin.quiz_error_message, 'error');
            }
        });
    });

    // Handle Delete Question
    $(document).on('click', '.hairy-delete-question', function() {
        if (confirm(hairyQuizAdmin.delete_confirm)) { // Custom message box implementation should replace this
            const questionId = $(this).data('id');
            const $row = $(this).closest('tr');

            $.ajax({
                url: hairyQuizAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'hairy_delete_question',
                    nonce: hairyQuizAdmin.nonce,
                    question_id: questionId
                },
                success: function(response) {
                    if (response.success) {
                        displayMessage(response.data.message, 'success');
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            // Optional: Reload if table becomes empty
                            if ($('.hairy-quiz-table tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        displayMessage(response.data.message, 'error');
                    }
                },
                error: function() {
                    displayMessage(hairyQuizAdmin.quiz_error_message, 'error');
                }
            });
        }
    });

    // Handle Edit Question
    $(document).on('click', '.hairy-edit-question', function() {
        const questionId = $(this).data('id');
                    

        $.ajax({
            url: hairyQuizAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'hairy_get_question_edit_form',
                nonce: hairyQuizAdmin.nonce,
                question_id: questionId
            },
            success: function(response) {
                if (response.success) {
                    const question = response.data.question;
                    const answers = response.data.answers;

                    // Populate form fields
                    $questionIdField.val(question.ID);
                    $questionSequenceField.val(question.question_sequence);
                    $questionTextField.val(question.question);
                    $questionNoteField.val(question.note);

                    // Set Quiz Type dropdown
                    if ($quizTypeSelect.find(`option[value="${question.quiz_type}"]`).length) {
                        $quizTypeSelect.val(question.quiz_type);
                        $quizTypeNewField.hide().prop('required', false).val('');
                    } else {
                        // If current quiz type is not in the existing list, select "Add New" and populate new field
                        $quizTypeSelect.val('add_new');
                        $quizTypeNewField.val(question.quiz_type).show().prop('required', true);
                    }

                    $answerTypeSelect.val(question.answer_type);
                    // Set Answer Type dropdown
                    if (question.answer_type == 'input') {
                        $('.hairy-answer-options').hide();
                    }

                    // Clear existing answers and add fetched ones
                    $answersContainer.empty();
                    if (answers.length > 0) {
                        answers.forEach(ans => addAnswerField(ans.answer_text, ans.answer_label, true));
                    } else {
                        addAnswerField(); // Add at least one empty field if no answers
                    }
                    updateRemoveButtonsVisibility();
                    updateAnswerLabelOptions(); // Important to update after populating answers 


                    // Change button text and show cancel button
                    $saveButton.val('Update Question');
                    $cancelEditButton.show();

                    // Scroll to form
                    $('html, body').animate({
                        scrollTop: $quizFormContainer.offset().top - 50
                    }, 500);

                } else {
                    displayMessage(response.data.message, 'error');
                }
            },
            error: function() {
                displayMessage(hairyQuizAdmin.quiz_error_message, 'error');
            }
        });
    });

    // Handle Cancel Edit
    $cancelEditButton.on('click', function() {
        clearForm();
        displayMessage('Edit cancelled.', 'info');
    });

    // Function to clear form and reset to "Add New" mode
    function clearForm() {
        $form[0].reset(); // Reset all form fields
        $questionIdField.val(''); // Clear hidden ID field
        $answersContainer.empty(); // Clear answer fields
        addAnswerField(); // Add a fresh empty answer field
        updateRemoveButtonsVisibility();
        updateAnswerLabelOptions(); // Reset labels
        $saveButton.val('Save Question'); // Reset button text
        $cancelEditButton.hide(); // Hide cancel button
        $quizTypeSelect.val(''); // Reset select dropdown
        $quizTypeNewField.hide().prop('required', false).val(''); 
        $('.hairy-message').fadeOut(200, function() {
            $(this).remove();
        }); // Clear messages
    }

    // Function to display temporary messages (success/error)
    function displayMessage(message, type) {
        $('.hairy-message').remove(); // Remove any existing messages
        const $messageDiv = $('<div class="hairy-message ' + type + '"></div>');
        $messageDiv.text(message);
        $quizFormContainer.prepend($messageDiv);
        $messageDiv.fadeIn('fast').delay(5000).fadeOut('slow', function() {
            $(this).remove();
        });
    }

    // Initialize Datepicker for admin side if needed (e.g., if you add DOB to questions or results)
    // For now, it's only on the front-end, but kept here for future reference
    // $( ".hairy-datepicker" ).datepicker({
    //     dateFormat: 'yy-mm-dd'
    // });
});