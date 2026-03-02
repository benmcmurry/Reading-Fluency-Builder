(() => {
    const config = window.APP_CONFIG || { page: 'instructions', passageId: null, wordCount: 0 };

    const pages = document.querySelectorAll('.page');
    const navButtons = document.querySelectorAll('.nav-btn');
    const navPanel = document.getElementById('nav-panel');
    const menuToggle = document.getElementById('menuToggle');
    const userButton = document.getElementById('user');
    const dropdown = document.getElementById('drop-down');
    const overlay = document.getElementById('invisible-background');

    const goBtn = document.getElementById('go');
    const userSpeedInput = document.getElementById('userSpeed');
    const scrollPassage = document.getElementById('scrollPassage');
    const scrollWindow = document.getElementById('window');

    const startTimerBtn = document.getElementById('start-timer');
    const stopTimerBtn = document.getElementById('stop-timer');
    const timerResultsLink = document.getElementById('timer-results');

    const checkAnswersBtn = document.getElementById('check-answers');

    const emailPopupBtn = document.getElementById('email_results');
    const emailPopup = document.getElementById('email_results_popup');
    const closePopupBtn = emailPopup ? emailPopup.querySelector('.close_popup') : null;
    const sendEmailBtn = document.getElementById('send_email');
    const sentMessage = document.getElementById('sent');

    const searchInput = document.getElementById('search');
    const searchResults = document.getElementById('search-results');

    let currentPage = 'instructions';
    let startTime = null;
    let activeScrollAnimation = null;

    function setPage(page, pushHistory = true) {
        currentPage = page;

        pages.forEach((section) => {
            const isActive = section.id === page;
            section.classList.toggle('is-active', isActive);
            section.hidden = !isActive;
        });

        navButtons.forEach((button) => {
            const isActive = button.dataset.page === page;
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        if (pushHistory && config.passageId) {
            const url = new URL(window.location.href);
            url.searchParams.set('passage_id', config.passageId);
            url.searchParams.set('page', page);
            window.history.replaceState({ page }, '', url.toString());
        }
    }

    function openOverlay() {
        if (overlay) {
            overlay.hidden = false;
        }
    }

    function closeOverlayIfUnused() {
        const popupOpen = emailPopup && !emailPopup.hidden;
        const dropdownOpen = dropdown && !dropdown.hidden;
        if (!popupOpen && !dropdownOpen && overlay) {
            overlay.hidden = true;
        }
    }

    function toggleUserDropdown() {
        if (!dropdown) {
            return;
        }
        const isOpen = !dropdown.hidden;
        dropdown.hidden = isOpen;
        if (userButton) {
            userButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        }
        if (isOpen) {
            closeOverlayIfUnused();
        } else {
            openOverlay();
        }
    }

    function closePopup() {
        if (emailPopup) {
            emailPopup.hidden = true;
        }
        closeOverlayIfUnused();
    }

    function openPopup() {
        if (!emailPopup) {
            return;
        }
        emailPopup.hidden = false;
        openOverlay();
        const input = emailPopup.querySelector('#email');
        if (input) {
            input.focus();
        }
    }

    async function postHistory(payload) {
        const body = new URLSearchParams(payload);
        const response = await fetch('history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            },
            body,
        });
        return response.text();
    }

    function startScroller() {
        if (!scrollPassage || !scrollWindow || !userSpeedInput) {
            return;
        }

        const parsed = Number.parseInt(userSpeedInput.value, 10);
        const wpm = Number.isFinite(parsed) && parsed >= 100 ? parsed : 100;
        userSpeedInput.value = String(wpm);

        const duration = Math.max(5000, Math.round((config.wordCount / wpm) * 60000));
        const startTop = scrollWindow.clientHeight + 16;
        const passageHeight = scrollPassage.scrollHeight + 16;

        if (activeScrollAnimation) {
            activeScrollAnimation.cancel();
        }

        scrollPassage.style.top = `${startTop}px`;
        activeScrollAnimation = scrollPassage.animate(
            [
                { top: `${startTop}px` },
                { top: `${-passageHeight}px` },
            ],
            {
                duration,
                easing: 'linear',
                fill: 'forwards',
            }
        );

        postHistory({ userSpeed: String(wpm) })
            .then((text) => {
                document.querySelectorAll('.scrolled_reading').forEach((node) => {
                    node.textContent = text;
                });
            })
            .catch(() => {
                // No-op: keep UX responsive even if save fails.
            });
    }

    function startTimer() {
        if (!startTimerBtn || !stopTimerBtn || !timerResultsLink) {
            return;
        }
        startTime = Date.now();
        startTimerBtn.hidden = true;
        stopTimerBtn.hidden = false;
        timerResultsLink.hidden = true;
    }

    function stopTimer() {
        if (!startTime || !stopTimerBtn || !timerResultsLink || !startTimerBtn) {
            return;
        }

        const elapsedSeconds = Math.max(1, (Date.now() - startTime) / 1000);
        const minutes = elapsedSeconds / 60;
        const fullMinutes = Math.floor(minutes);
        const seconds = Math.floor((minutes - fullMinutes) * 60);
        const formatted = `${fullMinutes}:${String(seconds).padStart(2, '0')}`;
        const timedWpm = Math.round(config.wordCount / minutes);

        stopTimerBtn.hidden = true;
        timerResultsLink.hidden = false;
        timerResultsLink.textContent = `Time: ${formatted} WPM: ${timedWpm}`;

        postHistory({ time: formatted, wpm: String(timedWpm) })
            .then((text) => {
                document.querySelectorAll('.timed_reading').forEach((node) => {
                    node.textContent = text;
                });
            })
            .catch(() => {
                // No-op
            });
    }

    function checkAnswers() {
        const questions = document.querySelectorAll('.question-box');
        if (!questions.length || !checkAnswersBtn) {
            return;
        }

        document.querySelectorAll('.correct-answer').forEach((node) => {
            node.classList.add('is-correct');
        });

        let totalCorrect = 0;
        document.querySelectorAll('input[type="radio"]:checked').forEach((input) => {
            if (input.value === 'correct') {
                totalCorrect += 1;
            }
        });

        const totalPossible = questions.length;
        const percent = Math.round((totalCorrect / totalPossible) * 100);
        const score = `${totalCorrect}/${totalPossible} correct - ${percent}%`;

        checkAnswersBtn.textContent = score;
        checkAnswersBtn.disabled = true;

        postHistory({ score })
            .then((text) => {
                document.querySelectorAll('.comprehension_quiz').forEach((node) => {
                    node.textContent = text;
                });
            })
            .catch(() => {
                // No-op
            });
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    async function sendEmail() {
        const form = document.getElementById('email_results_form');
        if (!form || !sentMessage) {
            return;
        }

        const formData = new FormData(form);
        const email = String(formData.get('email') || '');

        if (!validateEmail(email)) {
            sentMessage.textContent = 'Please enter a valid email address.';
            return;
        }

        const response = await fetch('email.php', {
            method: 'POST',
            body: formData,
        });
        sentMessage.innerHTML = await response.text();
    }

    let searchRequestId = 0;
    function clearSearchResults() {
        if (!searchResults) {
            return;
        }
        searchResults.hidden = true;
        searchResults.innerHTML = '';
    }

    async function runSearch(query) {
        if (!searchResults) {
            return;
        }

        const thisRequest = ++searchRequestId;
        if (query.length < 2) {
            clearSearchResults();
            return;
        }

        const response = await fetch(`search.php?phrase=${encodeURIComponent(query)}`);
        const results = await response.json();
        if (thisRequest !== searchRequestId) {
            return;
        }

        searchResults.innerHTML = '';
        if (!results.length) {
            clearSearchResults();
            return;
        }

        results.forEach((result) => {
            const li = document.createElement('li');
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = result.title;
            btn.addEventListener('click', () => {
                window.location.href = result.link;
            });
            li.appendChild(btn);
            searchResults.appendChild(li);
        });

        searchResults.hidden = false;
    }

    function setupEvents() {
        navButtons.forEach((button) => {
            button.addEventListener('click', () => {
                setPage(button.dataset.page);
            });
        });

        if (menuToggle && navPanel) {
            menuToggle.addEventListener('click', () => {
                const isOpen = navPanel.classList.toggle('is-open');
                menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        if (userButton) {
            userButton.addEventListener('click', toggleUserDropdown);
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                if (dropdown) {
                    dropdown.hidden = true;
                }
                if (userButton) {
                    userButton.setAttribute('aria-expanded', 'false');
                }
                closePopup();
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                if (dropdown && !dropdown.hidden) {
                    dropdown.hidden = true;
                    if (userButton) {
                        userButton.setAttribute('aria-expanded', 'false');
                    }
                }
                closePopup();
            }
        });

        if (goBtn) {
            goBtn.addEventListener('click', startScroller);
        }

        if (startTimerBtn) {
            startTimerBtn.addEventListener('click', startTimer);
        }

        if (stopTimerBtn) {
            stopTimerBtn.addEventListener('click', stopTimer);
        }

        if (checkAnswersBtn) {
            checkAnswersBtn.addEventListener('click', checkAnswers);
        }

        if (emailPopupBtn) {
            emailPopupBtn.addEventListener('click', openPopup);
        }

        if (closePopupBtn) {
            closePopupBtn.addEventListener('click', closePopup);
        }

        if (sendEmailBtn) {
            sendEmailBtn.addEventListener('click', () => {
                sendEmail().catch(() => {
                    if (sentMessage) {
                        sentMessage.textContent = 'Unable to send email right now.';
                    }
                });
            });
        }

        if (searchInput) {
            let searchDebounce = null;
            searchInput.addEventListener('input', () => {
                if (searchDebounce) {
                    window.clearTimeout(searchDebounce);
                }
                searchDebounce = window.setTimeout(() => {
                    runSearch(searchInput.value.trim()).catch(() => {
                        clearSearchResults();
                    });
                }, 180);
            });

            searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && searchResults && !searchResults.hidden) {
                    const first = searchResults.querySelector('button');
                    if (first) {
                        event.preventDefault();
                        first.click();
                    }
                }
            });
        }

        document.addEventListener('click', (event) => {
            if (!searchResults || !searchInput) {
                return;
            }
            const withinSearch = searchResults.contains(event.target) || searchInput.contains(event.target);
            if (!withinSearch) {
                clearSearchResults();
            }
        });
    }

    function initialize() {
        pages.forEach((section) => {
            section.hidden = true;
        });

        const allowedPages = new Set(['instructions', 'reading', 'scroller', 'timer', 'quiz', 'vocab']);
        const firstPage = allowedPages.has(config.page) ? config.page : 'instructions';
        setPage(firstPage, false);

        if (window.matchMedia('(min-width: 48rem)').matches && navPanel) {
            navPanel.classList.add('is-open');
        }

        setupEvents();
    }

    initialize();
})();
