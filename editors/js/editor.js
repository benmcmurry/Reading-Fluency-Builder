(() => {
    const config = window.EDITOR_CONFIG || {};

    const saveButton = document.getElementById('save');
    const newQuestionButton = document.getElementById('new_question');
    const saveDialog = document.getElementById('save_dialog');
    const questionsList = document.getElementById('questions');

    const userButton = document.getElementById('user');
    const userDropdown = document.getElementById('drop-down');

    function flashStatus(message) {
        if (!saveDialog) {
            return;
        }
        saveDialog.textContent = message;
        window.setTimeout(() => {
            saveDialog.textContent = '';
        }, 2000);
    }

    function formEncode(obj) {
        const params = new URLSearchParams();
        Object.entries(obj).forEach(([key, value]) => {
            params.set(key, value);
        });
        return params;
    }

    async function savePassage() {
        const payload = {
            passage_id: String(config.passageId),
            passage_title: document.getElementById('title')?.textContent ?? '',
            passage_text: document.getElementById('passage_text')?.innerHTML ?? '',
            author: document.getElementById('author')?.textContent ?? '',
            source: document.getElementById('source')?.textContent ?? '',
            length: document.getElementById('length')?.textContent ?? '',
            lexile: document.getElementById('lexile')?.textContent ?? '',
            flesch_reading_ease: document.getElementById('flesch_reading_ease')?.textContent ?? '',
            flesch_kincaid_level: document.getElementById('flesch_kincaid_level')?.textContent ?? '',
            library_id: document.getElementById('library_id')?.textContent ?? '',
            vocabulary: document.getElementById('vocabulary')?.innerHTML ?? '',
            modified_by: String(config.netid || ''),
        };

        const response = await fetch('save_passage.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: formEncode(payload),
        });
        flashStatus(await response.text());
    }

    async function saveQuestion(questionEl) {
        if (!questionEl) {
            return;
        }

        const questionId = questionEl.dataset.questionId;
        const values = {};
        questionEl.querySelectorAll('.quiz_item').forEach((el) => {
            values[el.dataset.field] = el.textContent ?? '';
        });

        const response = await fetch('save_question.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: formEncode({
                question_id: String(questionId),
                question_text: values.question_text || '',
                correct_answer: values.correct_answer || '',
                distractor_1: values.distractor_1 || '',
                distractor_2: values.distractor_2 || '',
                distractor_3: values.distractor_3 || '',
                modified_by: String(config.netid || ''),
            }),
        });

        flashStatus(await response.text());
    }

    async function deleteQuestion(questionEl) {
        const questionId = questionEl?.dataset.questionId;
        if (!questionId) {
            return;
        }

        const response = await fetch('delete_question.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: formEncode({ question_id: String(questionId) }),
        });

        questionEl.remove();
        flashStatus(await response.text());
    }

    async function saveQuestionOrder() {
        if (!questionsList) {
            return;
        }

        const params = new URLSearchParams();
        questionsList.querySelectorAll('.question-box').forEach((el) => {
            params.append('orders[]', String(el.dataset.questionId));
        });

        const response = await fetch('set_question_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: params,
        });

        flashStatus(await response.text());
    }

    async function addQuestion() {
        const response = await fetch('add_question.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body: formEncode({ passage_id: String(config.passageId) }),
        });
        flashStatus(await response.text());
        window.location.reload();
    }

    function moveQuestion(questionEl, direction) {
        if (!questionsList || !questionEl) {
            return;
        }

        if (direction < 0) {
            const prev = questionEl.previousElementSibling;
            if (prev) {
                questionsList.insertBefore(questionEl, prev);
            }
        } else {
            const next = questionEl.nextElementSibling;
            if (next) {
                questionsList.insertBefore(next, questionEl);
            }
        }
        saveQuestionOrder().catch(() => {
            flashStatus('Unable to save question order.');
        });
    }

    function onPlainTextPaste(event) {
        event.preventDefault();
        const text = (event.clipboardData || window.clipboardData).getData('text');
        document.execCommand('insertText', false, text);
    }

    function setupUserDropdown() {
        if (!userButton || !userDropdown) {
            return;
        }

        userButton.addEventListener('click', () => {
            const open = !userDropdown.hidden;
            userDropdown.hidden = open;
            userButton.setAttribute('aria-expanded', open ? 'false' : 'true');
        });

        document.addEventListener('click', (event) => {
            if (!userDropdown.contains(event.target) && !userButton.contains(event.target)) {
                userDropdown.hidden = true;
                userButton.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function setupEvents() {
        if (saveButton) {
            saveButton.addEventListener('click', () => {
                savePassage().catch(() => flashStatus('Unable to save passage.'));
            });
        }

        if (newQuestionButton) {
            newQuestionButton.addEventListener('click', () => {
                addQuestion().catch(() => flashStatus('Unable to add question.'));
            });
        }

        document.addEventListener('keydown', (event) => {
            const isSave = (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's';
            if (isSave) {
                event.preventDefault();
                savePassage().catch(() => flashStatus('Unable to save passage.'));
            }
        });

        document.querySelectorAll('.editable-passage').forEach((el) => {
            el.addEventListener('blur', () => {
                savePassage().catch(() => flashStatus('Unable to save passage.'));
            });
            el.addEventListener('paste', onPlainTextPaste);
        });

        if (questionsList) {
            questionsList.addEventListener('blur', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement) || !target.classList.contains('quiz_item')) {
                    return;
                }
                const questionEl = target.closest('.question-box');
                saveQuestion(questionEl).catch(() => flashStatus('Unable to save question.'));
            }, true);

            questionsList.querySelectorAll('.quiz_item').forEach((el) => {
                el.addEventListener('paste', onPlainTextPaste);
            });

            questionsList.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement)) {
                    return;
                }

                const questionEl = target.closest('.question-box');
                if (!questionEl) {
                    return;
                }

                if (target.classList.contains('delete')) {
                    const shouldDelete = window.confirm('Are you sure you want to delete this question?');
                    if (shouldDelete) {
                        deleteQuestion(questionEl).catch(() => flashStatus('Unable to delete question.'));
                    }
                    return;
                }

                if (target.classList.contains('move-up')) {
                    moveQuestion(questionEl, -1);
                    return;
                }

                if (target.classList.contains('move-down')) {
                    moveQuestion(questionEl, 1);
                }
            });
        }

        setupUserDropdown();
    }

    setupEvents();
})();
