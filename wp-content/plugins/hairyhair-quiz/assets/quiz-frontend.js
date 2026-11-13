document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("hairy-quiz-form");
  if (!form) return; // Safety check

  const steps = form.querySelectorAll(".quiz-step");
  const bullets = document.querySelectorAll(".step-indicator .step-bullet");
  
  // Add progress bar element dynamically if not in HTML
  let progress = document.querySelector(".step-progress");
  if (!progress) {
    const ul = document.querySelector(".step-indicator");
    progress = document.createElement("div");
    progress.classList.add("step-progress");
    ul.appendChild(progress);
  }

  let currentStep = 0;

  // Show a step by index and update progress
  function showStep(index) {
    steps.forEach((step, i) => {
      step.style.display = i === index ? "block" : "none";
    });
    bullets.forEach((b, i) => b.classList.toggle("active", i <= index));

    // Animate progress pipe
    const totalSteps = bullets.length - 1; // number of intervals
    const scale = index / totalSteps;
    progress.style.transform = `scaleX(${scale})`;
  }

  showStep(currentStep);

  // Next and Previous buttons
  form.addEventListener("click", function (e) {
    if (e.target.classList.contains("next-step") || e.target.classList.contains("prev-step")) {
      const currentStepEl = steps[currentStep];

      if (e.target.classList.contains("next-step")) {
        // Validate inputs before moving forward
        const inputs = currentStepEl.querySelectorAll("input, textarea, select");
        let allFilled = true;

        inputs.forEach((input) => {
          const type = input.type.toLowerCase();

          if (type === "radio" || type === "checkbox") {
            const group = currentStepEl.querySelectorAll(`input[name="${input.name}"]`);
            const checked = Array.from(group).some((i) => i.checked);
            if (!checked) allFilled = false;
          } else if (!input.value.trim()) {
            allFilled = false;
          }
        });

        if (!allFilled) {
          alert("Please fill in all required fields before continuing.");
          return;
        }

        if (currentStep < steps.length - 1) currentStep++;
      } else if (e.target.classList.contains("prev-step")) {
        if (currentStep > 0) currentStep--;
      }

      showStep(currentStep);
    }
  });

  // Checkbox & radio toggle class
  form.addEventListener("change", function (e) {
    if (e.target.matches(".answer-option input[type='checkbox'], .answer-option input[type='radio']")) {
      const parent = e.target.closest(".answer-option");

      if (e.target.type === "checkbox") {
        parent.classList.toggle("checked", e.target.checked);
      } else if (e.target.type === "radio") {
        const name = e.target.name;
        const radios = form.querySelectorAll(`.answer-option input[type="radio"][name="${name}"]`);
        radios.forEach((r) => r.closest(".answer-option").classList.remove("checked"));
        if (e.target.checked) parent.classList.add("checked");
      }
    }
  });

  // Form submission via AJAX
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    formData.append("action", "hairy_quiz_submit");
    formData.append("security", hairyQuizAjax.nonce);

    const answers = {};
    let quizIndex = 0;

    steps.forEach((step, i) => {
      if (i === 0) return; // skip contact info
      const inputs = step.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked');
      if (inputs.length > 0) {
        answers[`question_${quizIndex}`] = Array.from(inputs).map((input) => input.value);
      }
      quizIndex++;
    });

    formData.append("answers", JSON.stringify(answers));

    fetch(hairyQuizAjax.ajax_url, { method: "POST", body: formData })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          window.location.href = data.data.redirect;
        } else {
          alert("Error saving submission");
          console.error(data);
        }
      })
      .catch((err) => {
        console.error(err);
        alert("Something went wrong.");
      });
  });
});
