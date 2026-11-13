// extend/campaign/admin/js/admin-scripts.js

jQuery(document).ready(function ($) {
  $('input[type="datetime-local"]').each(function () {
    if (typeof jQuery.fn.datepicker !== "undefined") {
      $(this).datepicker({
        dateFormat: "yy-mm-dd",
      });
    }
  });

  $("#hh-campaign-cms-form").on("submit", function (e) {
    e.preventDefault();

    var $form = $("#hh-campaign-cms-form");
    var formData = $form.serializeArray();
    var $submitButton = $form.find('input[type="submit"]');

    $submitButton.prop("disabled", true).val("Saving...");
    $("#message").hide().removeClass("notice notice-success notice-error");

    $.ajax({
      url: hhCampaignCMS.ajaxurl,
      type: "POST",
      data: formData,
      success: function (response) {
        if (response.success) {
          $("#message")
            .addClass("notice notice-success is-dismissible")
            .html("<p>" + response.data.message + "</p>")
            .show();
          if (
            response.data.campaign_id &&
            $form.find('input[name="campaign_id"]').val() === "0"
          ) {
            // If it's a new campaign, update the hidden ID and redirect to edit page
            $form
              .find('input[name="campaign_id"]')
              .val(response.data.campaign_id);
            window.location.href =
              "?page=hh-campaign-cms-edit&id=" +
              response.data.campaign_id +
              "&message=saved";
          }
        } else {
          $("#message")
            .addClass("notice notice-error is-dismissible")
            .html("<p>" + response.data.message + "</p>")
            .show();
        }
      },
      error: function (xhr, status, error) {
        $("#message")
          .addClass("notice notice-error is-dismissible")
          .html("<p>AJAX Error: " + error + "</p>")
          .show();
      },
      complete: function () {
        $submitButton
          .prop("disabled", false)
          .val($submitButton.data("original-text") || $submitButton.val());
      },
    });
  });

  // Handle Campaign Action Buttons (Delete, Pause, Activate)
  $(".hh-campaign-cms-action-button").on("click", function (e) {
    e.preventDefault();
    var $button = $(this);
    var campaignId = $button.data("id");
    var action = $button.data("action"); // 'delete', 'pause', 'activate'
    var confirmMessage = "";
    var ajaxAction = "";

    if (action === "delete") {
      confirmMessage =
        "Are you sure you want to delete this campaign? This cannot be undone.";
      ajaxAction = "hh_campaign_cms_delete_campaign";
    } else if (action === "pause") {
      confirmMessage = "Are you sure you want to pause this campaign?";
      ajaxAction = "hh_campaign_cms_pause_campaign";
    } else if (action === "activate") {
      confirmMessage =
        "Are you sure you want to activate this campaign? Its status will update based on its dates.";
      ajaxAction = "hh_campaign_cms_activate_campaign";
    }

    if (confirm(confirmMessage)) {
      $.ajax({
        url: hhCampaignCMS.ajaxurl,
        type: "POST",
        data: {
          action: ajaxAction,
          campaign_id: campaignId,
          nonce: hhCampaignCMS.nonce,
        },
        success: function (response) {
          if (response.success) {
            $("#message")
              .addClass("notice notice-success is-dismissible")
              .html("<p>" + response.data.message + "</p>")
              .show();
            // Reload or update table row
            location.reload(); // Simple reload for demonstration
          } else {
            $("#message")
              .addClass("notice notice-error is-dismissible")
              .html("<p>" + response.data.message + "</p>")
              .show();
          }
        },
        error: function (xhr, status, error) {
          $("#message")
            .addClass("notice notice-error is-dismissible")
            .html("<p>AJAX Error: " + error + "</p>")
            .show();
        },
      });
    }
  });

  // WordPress Media Uploader integration
  var mediaUploader;

  $(".hh-campaign-upload-button").on("click", function (e) {
    e.preventDefault();

    var $button = $(this);

    console.log($button.data("slot-id"), '$button.attr("id")');

    var $parentDiv = $button.closest(".campaign-slot-box");
    var $imagePreview = $("#" + $button.data("slot-id"));
    var $imageUrlInput = $parentDiv.find(".hh-campaign-image-url");
    var $webpUrlInput = $parentDiv.find(".hh-campaign-webp-url");
    var $removeButton = $parentDiv.find(".hh-campaign-remove-button");

    console.log($imagePreview, "$imagePreview");

    // If the uploader already exists, open it
    if (mediaUploader) {
      mediaUploader.off("select");
    }

    // Create the media frame
    mediaUploader = wp.media({
      title: hhCampaignCMS.media_title,
      button: {
        text: hhCampaignCMS.media_button,
      },
      multiple: false,
    });

    // When a file is selected, grab the URL and display it
    mediaUploader.on("select", function () {
      console.log($imagePreview, "$imagePreview inside function");
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      //   openCropper(attachment.url);
      $imagePreview.html(
        '<img src="' +
          attachment.url +
          '" style="max-width:200px; height:auto;" />'
      );
      $imageUrlInput.val(attachment.url);
      $removeButton.show();
      $imagePreview = "";

      // Trigger custom AJAX to upload to custom directory and get WebP
      var campaignName = $("#campaign_name").val() || "untitled-campaign";
      var slotName =
        $parentDiv.find("h3").text().split("(")[0].trim() || "untitled-slot";

      // Create a FormData object for file upload
      var formData = new FormData();
      formData.append("action", "hh_campaign_cms_upload_asset");
      formData.append("nonce", hhCampaignCMS.nonce);
      formData.append("campaign_name", campaignName); 

      formData.append("attachment_id", attachment.id); // Send attachment ID

      $.ajax({
        url: hhCampaignCMS.ajaxurl,
        type: "POST",
        data: formData,
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a multipart form
        success: function (response) {
          if (response.success) {
            $imageUrlInput.val(response.data.original_url); // Update with custom path URL
            $webpUrlInput.val(response.data.webp_url); // Set WebP URL
            console.log("Asset processed and WebP generated:", response.data);
          } else {
            alert("Error processing asset: " + response.data.message);
          }
        },
        error: function (xhr, status, error) {
          alert("AJAX Error processing asset: " + error);
        },
      });
    });

    // Open the media dialog
    mediaUploader.open();

    // function openCropper(imageUrl) {
    //   const image = document.getElementById("image-to-crop");
    //   image.src = imageUrl;

    //   // Wait until image loads
    //   image.onload = function () {
    //     cropper = new Cropper(image, {
    //       aspectRatio: 1, // for square crop
    //       viewMode: 1,
    //     });
    //   };

    //   document.getElementById("crop-btn").onclick = function () {
    //     const canvas = cropper.getCroppedCanvas({
    //       width: 400, // fixed width
    //       height: 400, // fixed height
    //     });

    //     // Convert canvas to blob or base64
    //     canvas.toBlob(function (blob) {
    //       // Upload blob via AJAX or send to media library
    //       uploadCroppedImage(blob);
    //     });
    //   };
    // }
  });

  // Remove Image button handler
  $(".hh-campaign-remove-button").on("click", function (e) {
    e.preventDefault();
    var $button = $(this);
    var $parentDiv = $button.closest(".campaign-slot-box");
    $parentDiv.find(".hh-campaign-image-preview").html("");
    $parentDiv.find(".hh-campaign-image-url").val("");
    $parentDiv.find(".hh-campaign-webp-url").val("");
    $parentDiv.find('input[name="destination_urls[]"]').val("");
    $parentDiv.find('input[name="alt_texts[]"]').val("");
    $button.hide();
  });

  // Handle Slot Save Form Submission (Add/Edit Slot)
  $("#hh-slot-form").on("submit", function (e) {
    e.preventDefault();

    var $form = $(this);
    var formData = $form.serializeArray();
    var $submitButton = $form.find('input[type="submit"]');

    $submitButton.prop("disabled", true).val("Saving...");
    $("#message").hide().removeClass("notice notice-success notice-error");

    $.ajax({
      url: hhCampaignCMS.ajaxurl,
      type: "POST",
      data: formData,
      success: function (response) {
        if (response.success) {
          $("#message")
            .addClass("notice notice-success is-dismissible")
            .html("<p>" + response.data.message + "</p>")
            .show();
          // Reload or update table
          if ($form.find('input[name="slot_id"]').val() === "0") {
            // Clear form for new entry after successful creation
            $form[0].reset();
            location.reload(); // Simple reload for demonstration
          } else {
            // Stay on edit page, but update UI if needed
            location.reload();
          }
        } else {
          $("#message")
            .addClass("notice notice-error is-dismissible")
            .html("<p>" + response.data.message + "</p>")
            .show();
        }
      },
      error: function (xhr, status, error) {
        $("#message")
          .addClass("notice notice-error is-dismissible")
          .html("<p>AJAX Error: " + error + "</p>")
          .show();
      },
      complete: function () {
        $submitButton
          .prop("disabled", false)
          .val($submitButton.data("original-text") || $submitButton.val());
      },
    });
  });

  // Handle Slot Delete Button
  $(".hh-slot-delete-button").on("click", function (e) {
    e.preventDefault();
    var $button = $(this);
    var slotId = $button.data("id");

    if (
      confirm(
        "Are you sure you want to delete this slot? This will also remove all associated campaign assets!"
      )
    ) {
      $.ajax({
        url: hhCampaignCMS.ajaxurl,
        type: "POST",
        data: {
          action: "hh_campaign_cms_delete_slot",
          slot_id: slotId,
          nonce: hhCampaignCMS.nonce,
        },
        success: function (response) {
          if (response.success) {
            $("#message")
              .addClass("notice notice-success is-dismissible")
              .html("<p>" + response.data.message + "</p>")
              .show();
            // Remove the row from the table
            $button.closest("tr").remove();
          } else {
            $("#message")
              .addClass("notice notice-error is-dismissible")
              .html("<p>" + response.data.message + "</p>")
              .show();
          }
        },
        error: function (xhr, status, error) {
          $("#message")
            .addClass("notice notice-error is-dismissible")
            .html("<p>AJAX Error: " + error + "</p>")
            .show();
        },
      });
    }
  });
});
