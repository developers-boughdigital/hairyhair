<?php /* Template Name: Personal Information */ ?>
<?php get_header(); ?>


    <div class="main-page">

        <section class="main-banner-content cms-main-banner mb-0">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="ban-left-content cms-h1">
                        <nav aria-label="breadcrumb">
                            <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                                
                                <?php if (function_exists('bcn_display')) {
                                bcn_display();
                                } ?>
                                
                            </div>
                        </nav>
                            <h1><?php the_title(); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="personal-info-wrap">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12"> 

                        <div class="mb-5 text-center">
                            <p class="mb-2">We’ll ask a few quick questions about your hair so we can recommend the best treatment for you.</p>
                            <p class="mb-2">It’s 100% confidential and takes less than 2 minutes.</p>
                            <p class="mb-2"><b>Your hair goals are just a couple of clicks away!</b></p> 
                        </div>


                        <div class="sign-form-wrap">
                            <div class="step-top-action">
                                <div class="progress" style="height: 6px; width: 480px; margin: 0 auto;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="step-container d-flex justify-content-between">
                                    <div class="step-circle" onclick="displaySteps(1)">
                                        <span>1</span>
                                        <h6>Personal Information</h6>
                                    </div>
                                    <div class="step-circle" onclick="displaySteps(2)">
                                        <span>2</span>
                                        <h6>Hair Details</h6>
                                    </div>
                                    <div class="step-circle" onclick="displaySteps(3)">
                                        <span>3</span>
                                        <h6>Medical Details</h6>
                                    </div>
                                </div>
                            </div>
                            <form class="sign-up-content-form" id="hairy-frontend-get-started-form" action="" method="post">

                            <?php $user_id = get_current_user_id(); ?>
                                
                                <input type="hidden" name="action" value="hairy_submit_lead">
                                <?php wp_nonce_field( 'hairy_lead_frontend_nonce', 'hairy_lead_frontend_nonce' ); ?> 

                                <div id="multi-step-form">
                                    <div class="step step-1">
                                        <h3>Personal Information</h3>
                                        <p>These details are required so the doctor can provide the best solution.</p>
                                        <div class="row sign-process">
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="field-list-all">
                                                    <label for="exampleInputFirstName" class="form-label">First Name</label>
                                                    <input type="text" name="first_name" class="form-control required alpha" id="exampleInputFirstName" aria-describedby="first_nameHelp" placeholder="Ex. John" value="<?php echo esc_attr( gm_get_user_meta($user_id, 'first_name', true) ); ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="field-list-all">
                                                    <label for="exampleInputLastName" class="form-label">Last Name</label>
                                                    <input type="text" name="last_name" class="form-control required alpha" id="exampleInputLastName" aria-describedby="last_nameHelp" placeholder="Ex. Doe" value="<?php echo esc_attr( gm_get_user_meta($user_id, 'last_name', true) ); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row sign-process mt-0">
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="field-list-all"> 
                                                    <label for="exampleInputEmail" class="form-label">Email Address</label>
                                                    <?php
                                                    $current_user = wp_get_current_user();
                                                    $user_email = $current_user->user_email;
                                                    ?> 
                                                    <input type="email" name="email" class="form-control required email" id="exampleInputEmail" aria-describedby="emaillHelp" placeholder="john.doe@example.com" value="<?php echo esc_attr( gm_get_user_meta($user_id, 'email', true) ? gm_get_user_meta($user_id, 'email', true) : $user_email ); ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="field-list-all">
                                                    <label for="exampleInputPhone" class="form-label">Phone Number</label>
                                                    <input type="tel" name="phone" class="form-control required numeric" id="exampleInputPhone" aria-describedby="phonelHelp" placeholder="Ex. 0412345678" minlength="10" maxlength="10" pattern="0[0-9]{9}" inputmode="numeric" value="<?php echo esc_attr( gm_get_user_meta($user_id, 'phone', true) ); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row sign-process mt-0">
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="field-list-all">
                                                    <label for="exampleInputDOB" class="form-label">DOB</label>
                                                    <input type="text" name="dob" class="form-control required hairy-datepicker" id="exampleInputDOB" aria-describedby="DOBHelp" placeholder="Ex. 2022-12-01" autocomplete="off" value="<?php echo esc_attr( gm_get_user_meta($user_id, 'dob', true) ); ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                <div class="field-list-all">
                                                    <label for="exampleInputPhone" class="form-label">Gender</label>
                                                    <select class="form-select required" name="gender" aria-label="Default select example"> 
                                                        <option value="male" <?php if(gm_get_user_meta($user_id, 'gender', true) == 'male') echo 'selected'; ?>><?php echo esc_html__('Male', 'hairy-hair'); ?></option>
                                                        <option value="female" <?php if(gm_get_user_meta($user_id, 'gender', true) == 'female') echo 'selected'; ?>><?php echo esc_html__('Female', 'hairy-hair'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
                                                <div class="field-list-all form-check">
                                                    <input type="checkbox" class="form-check-input" name="consent_to_contact" id="exampleCheck1" value="1" checked>
                                                    <label class="form-check-label" for="exampleCheck1">By completing this form, you agree to receive communications from HairyHair about our hair loss products and services, including access to our licensed health practitioners. Your information will only be used to provide personalised recommendations. You can unsubscribe at any time.</label>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="next-btn-form">
                                            <button type="button" class="btn secondary-btn next-step-btn next-step-one-button">Next</button>
                                        </div>
                                    </div>
                                
                                    <div class="step step-2" style="display:none">
                                        <h3>Hair Details</h3>
                                        <p>These details are required so the doctor can provide the best solution.</p>
                                        <div class="row">
                                            <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
                                                <div class="hair-loss-question">
                                                    <h5>How long have you been experiencing hair loss?</h5>
                                                    <div class="all-checkbox">
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="hair-loss-duration" value="Less than 6 months">
                                                                <span>Less than 6 months</span>
                                                            </label>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="hair-loss-duration" value="6 months to 1 year">
                                                                <span>6 months to 1 year</span>
                                                            </label>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="hair-loss-duration" value="1 - 2 years">
                                                                <span>1 - 2 years</span>
                                                            </label>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="hair-loss-duration" value="2+ years">
                                                                <span>2+ years</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="hair-loss-question">
                                                    <h5>What results are you hoping to achieve with a hair loss treatment?</h5>
                                                    <div class="all-checkbox">
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="expacted_results" value="Slowing down further hair loss">
                                                                <span>Slowing down further hair loss</span>
                                                            </label>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="expacted_results" value="Regrowing lost hair">
                                                                <span>Regrowing lost hair</span>
                                                            </label>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="expacted_results" value="Both">
                                                                <span>Both</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                // Values must match the Zoho CRM picklists exactly (see Hairy_Quiz_Frontend::lead_picklist_options()).
                                                $hh_lead_picklists = array(
                                                    'age_group'         => array( 'What is your age group?', Hairy_Quiz_Frontend::lead_picklist_options( 'age_group' ) ),
                                                    'current_hair_loss' => array( 'Which of the following best describes your current hair loss?', Hairy_Quiz_Frontend::lead_picklist_options( 'current_hair_loss' ) ),
                                                );
                                                foreach ( $hh_lead_picklists as $hh_name => $hh_question ) : ?>
                                                <div class="hair-loss-question" data-question="<?php echo esc_attr( $hh_name ); ?>">
                                                    <h5><?php echo esc_html( $hh_question[0] ); ?></h5>
                                                    <div class="all-checkbox">
                                                        <?php foreach ( $hh_question[1] as $hh_option ) : ?>
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="<?php echo esc_attr( $hh_name ); ?>" value="<?php echo esc_attr( $hh_option ); ?>" data-required="1">
                                                                <span><?php echo esc_html( $hh_option ); ?></span>
                                                            </label>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="next-btn-form">
                                            <button type="button" class="btn dark-btn prev-step-btn">Back</button>
                                            <button type="button" class="btn secondary-btn next-step-btn next-step-two-button">Next</button>
                                        </div>
                                    </div>
                                
                                    <div class="step step-3" style="display:none">
                                        <h3>Medical Details</h3>
                                        <p>These details are required so the doctor can provide the best solution.</p> 
                                        <div class="row">
                                            <div class="col-lg-12 col-md-6 col-sm-12 col-xs-12">
                                                <?php
                                                // Values must match the Zoho CRM picklists exactly (see Hairy_Quiz_Frontend::lead_picklist_options()).
                                                $hh_step3 = array(
                                                    'allergies_or_sensitivities' => array( 'Do you have any known allergies or sensitivities to medications or supplements?', 'explain_allergies', 'Please explain your allergies or sensitivities.' ),
                                                    'taking_any_medications'     => array( 'Are you currently taking any other medications or supplements?', 'explain_medications', 'Please list or explain your other medications or supplements.' ),
                                                    'high_blood_pressure'        => array( 'Do you have a history of high blood pressure (relevant to minoxidil use)?', '', '' ),
                                                );
                                                foreach ( $hh_step3 as $hh_name => $hh_q ) : ?>
                                                <div class="hair-loss-question" data-question="<?php echo esc_attr( $hh_name ); ?>">
                                                    <h5><?php echo esc_html( $hh_q[0] ); ?></h5>
                                                    <div class="all-checkbox">
                                                        <?php foreach ( Hairy_Quiz_Frontend::lead_picklist_options( $hh_name ) as $hh_option ) : ?>
                                                        <div class="form-group">
                                                            <label class="checkbox-styled">
                                                                <input type="radio" name="<?php echo esc_attr( $hh_name ); ?>" value="<?php echo esc_attr( $hh_option ); ?>">
                                                                <span><?php echo esc_html( $hh_option ); ?></span>
                                                            </label>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <?php if ( $hh_q[1] ) : ?>
                                                    <div class="mb-3 field-list-all hh-explanation" data-explains="<?php echo esc_attr( $hh_name ); ?>" style="display:none">
                                                        <label for="<?php echo esc_attr( $hh_q[1] ); ?>" class="form-label"><?php echo esc_html( $hh_q[2] ); ?></label>
                                                        <textarea name="<?php echo esc_attr( $hh_q[1] ); ?>" id="<?php echo esc_attr( $hh_q[1] ); ?>" class="form-control" rows="4" maxlength="<?php echo (int) Hairy_Quiz_Frontend::EXPLANATION_MAX_CHARS; ?>"></textarea>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    
                                        <div class="next-btn-form">
                                            <button type="button" class="btn dark-btn prev-step-btn">Back</button>
                                            <button type="submit" id="hairy-submit-lead-button" class="btn secondary-btn">Submit</button>
                                        </div>
                                    </div>

                                    <div class="formErrorMessage" id="hairy-lead-message" style="display:none;"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>


<?php get_footer(); ?>