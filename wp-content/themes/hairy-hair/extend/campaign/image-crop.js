var $img = jQuery('[id^="image-preview-"]');
if ($img.length && typeof $img.imgAreaSelect !== "undefined") {
  jQuery('input[id^="imgedit-crop-width-"]').val("1");
  jQuery('input[id^="imgedit-crop-height-"]').val("1");

  jQuery('input[id^="imgedit-sel-width-"]').val("512");
  jQuery('input[id^="imgedit-sel-height-"]').val("512");

  jQuery(".imgedit-crop-ratio").hide();
}
