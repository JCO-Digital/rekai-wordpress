document.addEventListener("DOMContentLoaded", () => {
  let modeValue = "";

  // Select all input elements with name="rekai_autocomplete_mode".
  document
    .querySelectorAll('input[name="rekai_autocomplete_mode"]')
    .forEach((element) => {
      // Add on select listener that calls changeAutocompleteMode with the value of the selected field.
      if (element instanceof HTMLInputElement) {
        if (element.checked) {
          modeValue = element.value;
        }
        element.addEventListener("change", () => {
          changeAutocompleteMode(element.value);
        });
      }
    });

  changeAutocompleteMode(modeValue);

  setTimeout(() => {
    document.getElementById("rekai-selector-section")?.classList.add("animate");
  }, 100);

  // Select and loop over elements that have the "data-toggle-hide" attribute, and target the id equal to the value of the attribute.
  document.querySelectorAll("[data-toggle-hide]").forEach((element) => {
    element.addEventListener("click", () => {
      const targetId = element.getAttribute("data-toggle-hide");
      if (targetId) {
        const targetElement = document.getElementById(targetId);
        if (targetElement instanceof HTMLInputElement) {
          if (targetElement.type === "text") {
            targetElement.type = "password";
            element.classList.toggle("show", true);
          } else if (targetElement.type === "password") {
            targetElement.type = "text";
            element.classList.toggle("show", false);
          }
        }
      }
    });
  });
});

function changeAutocompleteMode(value: string) {
  document
    .getElementById("rekai-selector-section")
    ?.classList.toggle("show", value === "auto");
}
