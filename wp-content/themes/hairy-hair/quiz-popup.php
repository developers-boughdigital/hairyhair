<div id="quizPopup" class="hidden">
    <div>
        <div class="message">
            <img src="<?php echo get_template_directory_uri().'/assets/cross_icon.png' ?>" class="close">
            <img src="<?php echo get_template_directory_uri().'/assets/quix_popup_icon.png' ?>">
            <img src="">
            <h4>Prescription Product - Eligibility Confirmation</h4>
            <div class="blue">This product requires a valid prescription.</div>

            <p>This item requires a prescription, so we need to make sure it’s safe for you. Please confirm that none of the following apply:</p>
            <ul>
                <li>Allergies or sensitivities to any medications or supplements</li>
                <li>Current use of other medications or supplements</li>
                <li>A history of high blood pressure</li>
            </ul>
            <p>If you can answer “No” to all of these, click below to complete your purchase</p> 
            
            
        </div>
        <div class="footer">
            <a class="btn secondary-btn" id="productLink" href="#">Buy Now</a>
            <p>Or</p>
            <a href="/product-category/non-prescriptive/">Show me non-prescriptive options</a>
        </div>
    </div>
</div>



 

<style>
    .hidden{
        display:none !important;
    }
    div#quizPopup{
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: #0000001f;
        z-index: 999999;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: auto;
    }
    #quizPopup > div {
        background: #fff;
        width: 600px; 
        max-width: 90%;
        border-radius: 15px;
        box-shadow: 0 0 5px 0 #00000040;
    }
    #quizPopup .message {
        padding: 30px;
        text-align: center;
        position: relative;
    }
    #quizPopup .message img {
        width: 70px;
    }
    #quizPopup .message img.close {
        position: absolute;
        width: 20px;
        right: 20px;
        top: 20px;
        cursor: pointer;
    }
    #quizPopup .message .blue {
        background: #C7E9FF;
        display: inline;
        padding: 5px 10px;
        border-radius: 15px;
    }
    #quizPopup .message h4 {
        margin-bottom: 25px;
        margin-top: 30px;
    }
    #quizPopup .message p {
        margin-top: 25px
    }
    #quizPopup ul {
        list-style: disc;
        text-align: left;
        max-width: 500px;
        margin: 0 auto;
    }
    #quizPopup .footer {
        background: #EEF2F6;
        padding: 15px;
        border-radius: 0 0 15px 15px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        flex-direction: column;
    }
    #quizPopup .footer p {
        padding: 0;
        margin: 0;
    }
    #quizPopup .btn.secondary-btn { 
        margin-bottom: 10px !important;
    }
</style>

<script> 
$(document).ready(function () {
  
    // $('a[href*="/hair-treatment-quiz"]').on('click', function (e) {
    //   e.preventDefault();

    //   $('#quizPopup').removeClass('hidden');
       
    //   console.log($(this).data('product-url'))
    //   $('#productLink').attr('href', $(this).data('product-url')); 


    //   setTimeout(function(){
    //     $('#quizPopup').addClass('hidden'); 
    //   }, 15000)
    // });

    
    $('#quizPopup .message img.close').on('click', function (e) {
        e.preventDefault(); 
        $('#quizPopup').addClass('hidden');
          // $('#productLink').attr('href', '');
    });
 
    $('#productLink').on('click', function (e) {
        e.preventDefault(); 
        $('.actualCartBtn').click();
          // $('#productLink').attr('href', '');
    });
});
</script>