document.addEventListener("DOMContentLoaded", function () {
  const quantityInputs = document.querySelectorAll("input[name='qty']");
  quantityInputs.forEach(function (input) {
    input.addEventListener("change", function () {
      if (Number(input.value) < 1) {
        input.value = 1;
      }
    });
  });
});
