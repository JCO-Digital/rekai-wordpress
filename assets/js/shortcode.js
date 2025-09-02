function updateShortcodeDescription() {
  var selectedType = $("#rekai-shortcode-type").find(":selected");
  var shortcode_type = selectedType.val();
  var description = "description";
  $("#rekai-shortcode-description").text(description);
}

function generateShortcode() {
  var selectedType = $("#rekai-shortcode-type").find(":selected");
  var shortcode = selectedType.data("shortcode");
  var attributes = [];

  $(".rekai-shortcode-attributes input").each(function () {
    var $input = $(this);
    var name = $input.attr("name");
    var value = $input.val();

    if ($input.attr("type") === "checkbox") {
      if ($input.is(":checked")) {
        attributes.push(name + '="true"');
      }
    } else if (value) {
      attributes.push(name + '="' + value + '"');
    }
  });

  var output = "[" + shortcode;
  if (attributes.length > 0) {
    output += " " + attributes.join(" ");
  }
  output += "]";

  $("#rekai-shortcode-output").text(output);
}

jQuery(document).ready(function ($) {
  $("#rekai-shortcode-type").on("change", function () {
    updateShortcodeDescription();
    generateShortcode();
  });

  $(".rekai-shortcode-attributes input").on("change keyup", generateShortcode);

  $("#rekai-copy-shortcode").on("click", function () {
    var shortcode = $("#rekai-shortcode-output").text();
    navigator.clipboard.writeText(shortcode).then(function () {
      var $button = $("#rekai-copy-shortcode");
      var originalText = $button.text();
      $button.text("Copied!");
      setTimeout(function () {
        $button.text(originalText);
      }, 2000);
    });
  });

  // Initial update
  updateShortcodeDescription();
  generateShortcode();
});
