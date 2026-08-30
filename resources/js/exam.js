export function initExamNavigation() {
    const examForm = document.getElementById('examForm');
    if (!examForm) {
        return;
    }

    const totalQuestions = Number(document.querySelector('[data-total-questions]')?.dataset.totalQuestions || 0);
    const questionPanels = Array.from(document.querySelectorAll('.question-step'));
    const progressBar = document.getElementById('questionProgress');
    const currentQuestionLabel = document.querySelector('#questionProgress')?.previousElementSibling?.querySelector('p');
    const progressLabel = document.getElementById('progressLabel');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const submitButton = document.getElementById('submitButton');
    const answerInputs = Array.from(document.querySelectorAll('input[type="radio"][name^="answers["]'));
    const stepIndicators = Array.from(document.querySelectorAll('.step-indicator'));

    if (!totalQuestions || !questionPanels.length || !progressBar || !currentQuestionLabel || !progressLabel || !prevButton || !nextButton || !submitButton) {
        return;
    }

    let currentStep = 1;
    const answeredQuestions = new Set();
    const questionOfTemplate = document.querySelector('[data-question-template]')?.dataset.questionTemplate || 'Question :step of :total';

    function getStepLabel(step) {
        return questionOfTemplate.replace(':step', step).replace(':total', totalQuestions);
    }

    function getPassedPercent(step) {
        const percent = (step / totalQuestions) * 100;
        return ` ${percent}% ${document.querySelector('[data-complete-label]')?.dataset.completeLabel || 'Complete'}`;
    }

    function showStep(step) {
        const activePanel = questionPanels.find(panel => Number(panel.dataset.step) === step);
        const previousPanel = questionPanels.find(panel => Number(panel.dataset.step) === currentStep);

        if (previousPanel && previousPanel !== activePanel) {
            previousPanel.classList.add('opacity-0', 'pointer-events-none', '-translate-x-8');
            previousPanel.classList.remove('opacity-100', 'pointer-events-auto', 'translate-x-0');
        }

        if (activePanel) {
            activePanel.classList.remove('hidden');
            requestAnimationFrame(() => {
                activePanel.classList.remove('opacity-0', '-translate-x-8', 'translate-x-8');
                activePanel.classList.add('opacity-100', 'pointer-events-auto', 'translate-x-0');
            });
        }

        questionPanels.forEach(panel => {
            if (panel !== activePanel && panel !== previousPanel) {
                panel.classList.add('hidden', 'opacity-0', 'pointer-events-none', 'translate-x-8');
                panel.classList.remove('opacity-100', 'pointer-events-auto', 'translate-x-0', '-translate-x-8');
            }
        });

        stepIndicators.forEach(indicator => {
            const isActive = Number(indicator.dataset.step) === step;
            indicator.classList.toggle('bg-primary', isActive);
            indicator.classList.toggle('text-white', isActive);
            indicator.classList.toggle('border-transparent', isActive);
            indicator.classList.toggle('border-slate-300', !isActive);
            indicator.classList.toggle('text-slate-700', !isActive);
        });

        currentQuestionLabel.textContent = getStepLabel(step);
        progressLabel.textContent = getPassedPercent(step);
        progressBar.value = step;
        prevButton.disabled = step === 1;
        nextButton.classList.toggle('hidden', step === totalQuestions);
        submitButton.classList.toggle('hidden', step !== totalQuestions);

        currentStep = step;
    }

    function updateSubmitButton() {
        if (answeredQuestions.size >= totalQuestions) {
            submitButton.removeAttribute('disabled');
        }
    }

    function navigateTo(step) {
        currentStep = step;
        showStep(currentStep);
    }

    stepIndicators.forEach(indicator => {
        indicator.addEventListener('click', () => {
            navigateTo(Number(indicator.dataset.step));
        });
    });

    prevButton.addEventListener('click', () => {
        if (currentStep > 1) {
            navigateTo(currentStep - 1);
        }
    });

    nextButton.addEventListener('click', () => {
        if (currentStep < totalQuestions) {
            navigateTo(currentStep + 1);
        }
    });

    function updateCardSelection(input) {
        const questionName = input.name;
        const allOptions = Array.from(document.querySelectorAll(`input[name="${questionName}"]`));

        allOptions.forEach(option => {
            const card = option.closest('.option-card');
            const icon = card?.querySelector('i.check-icon');

            if (option.checked) {
                card?.classList.add('border-primary', 'bg-primary/10');
                card?.classList.remove('border-slate-200', 'bg-slate-50');
                icon?.classList.remove('hidden');
            } else {
                card?.classList.remove('border-primary', 'bg-primary/10');
                card?.classList.add('border-slate-200', 'bg-slate-50');
                icon?.classList.add('hidden');
            }
        });
    }

    answerInputs.forEach(input => {
        input.addEventListener('change', (event) => {
            const questionName = event.target.name;
            answeredQuestions.add(questionName);
            updateSubmitButton();
            updateCardSelection(event.target);
        });

        if (input.checked) {
            updateCardSelection(input);
        }
    });

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            examForm.reset();
            answeredQuestions.clear();
            submitButton.setAttribute('disabled', true);
            navigateTo(1);
        }
    });

    showStep(currentStep);
}
