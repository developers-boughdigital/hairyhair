<div id="hairy-quiz" class="hairy-quiz-wrapper">
    <form id="hairy-quiz-form" enctype="multipart/form-data">

        <!-- STEP 1: Contact Information -->
        <div class="quiz-step" data-step="0">
            <h2>About You</h2>
            <p>Answer a few quick questions so we can personalise your results.</p>
            <div class="contact-form-group">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" required>
                </div>
            </div>
            <div class="field-list-all form-check">
                <input type="radio" class="form-check-input" name="receive_communication" id="exampleCheck1" checked="">
                <label class="form-check-label" for="exampleCheck1">
                    By completing this form, you agree to receive communications from HairyHair
                    about our hair loss products and services, including access to our licensed health practitioners.
                    Your information will only be used to provide personalised recommendations. You can unsubscribe at
                    any time.
                </label>
            </div>
            <div class="quiz-navigation">
                <button type="button" class="btn secondary-btn next-step">Next</button>
            </div>
        </div>

        <?php
        $quiz_repeater = get_field('quiz_repeater', 'option');
        $q_index = 0;
        foreach ( $quiz_repeater as $q ) : ?>
        <div class="quiz-step" data-step="<?php echo esc_attr($q_index); ?>">
            <h2><?php echo esc_html($q['question']); ?></h2>
            <div class="contact-form-group answers-wrap">

                <?php
                    $attribute = $q['attribute'];
                    $input_type = $q['answer_type'];
                    $input_name = "question_{$q_index}";
                    $answers = $q['answers'];
                    foreach ( $answers as $a_index => $answer ) :
                        $val = $answer['value'];
                        $a = $answer['answer'];
                        $disclaimer = $answer['disclaimer'];
                        $input_id   = "q{$q_index}a{$a_index}";
                    ?>
                <div class="form-group answer-option">
                    <label for="<?php echo esc_attr($input_id); ?>">
                        <span class="answer-text"><?php echo esc_html($a); ?></span>
                        <input disclaimer="<?php echo esc_attr($disclaimer); ?>"
                            type="<?php echo esc_attr($input_type); ?>" name="<?php echo esc_attr($input_name); ?>"
                            id="<?php echo esc_attr($input_id); ?>" value="<?php echo esc_attr($val); ?>"
                            data-att="<?php echo esc_attr($attribute); ?>">
                    </label>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation -->
            <div class="quiz-navigation">
                <?php if ( $q_index > -1 ) : ?>
                <button type="button" class="prev-step btn dark-btn">Previous</button>
                <?php endif; ?>
                <button type="button" class="btn secondary-btn next-step">Next</button>
            </div>
        </div>
        <?php $q_index++; endforeach; ?>

        <!-- Final Step -->
        <div class="quiz-step" data-step="<?php echo esc_attr($q_index); ?>">
            <h2>Submit Quiz</h2>
            <div class="form-group upload-btn">
                <label for="quiz_file">
                    <h2>Would you like to upload a picture of your scalp for a more personalised assessment? (Optional)
                    </h2>
                </label>
                <input type="file" name="quiz_file" id="quiz_file" accept="image/*">
            </div>
            <div class="quiz-navigation">
                <button type="button" class="prev-step btn secondary-btn">Previous</button>
                <button type="submit" class="submit-quiz btn secondary-btn">Submit</button>
            </div>
        </div>
        <div class="quiz-steps">
            <ul class="step-indicator">
                <?php for ($i = -1; $i <= $q_index; $i++) : ?>
                <li class="step-bullet <?php echo $i === 0 ? 'active' : ''; ?>"><?php echo $i + 1; ?></li>
                <?php endfor; ?>
                <div class="step-progress"></div> <!-- Progress bar -->
            </ul>
        </div>
    </form>
</div>