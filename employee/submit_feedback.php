<?php
/**
 * employee/submit_feedback.php
 * AJAX-powered feedback submission form.
 */

$currentPage = 'submit';
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('employee');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback – FeedbackPro</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="app-layout">

<?php require_once __DIR__ . '/../includes/sidebar_employee.php'; ?>

<div class="main-content" id="mainContent">
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <h1 class="page-title">Submit Feedback</h1>
                <p class="page-subtitle">Share your thoughts anonymously or with your name</p>
            </div>
        </div>
    </header>

    <div class="page-content page-content-narrow">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-paper-plane"></i> New Feedback</h2>
            </div>
            <div class="card-body">
                <!-- Toast notifications area -->
                <div id="formToast" class="toast-container" aria-live="polite"></div>

                <form id="feedbackForm" novalidate>
                    <!-- Category -->
                    <div class="form-group">
                        <label for="category">
                            <i class="fa-solid fa-tag"></i> Category <span class="required">*</span>
                        </label>
                        <select id="category" name="category" class="form-select" required>
                            <option value="">— Select a category —</option>
                            <option value="work_environment">Work Environment</option>
                            <option value="management">Management</option>
                            <option value="career_growth">Career Growth</option>
                            <option value="team_collaboration">Team Collaboration</option>
                            <option value="work_life_balance">Work-Life Balance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Star Rating -->
                    <div class="form-group">
                        <label><i class="fa-solid fa-star"></i> Rating <span class="required">*</span></label>
                        <div class="star-rating" id="starRating" role="radiogroup" aria-label="Rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button" class="star-btn" data-value="<?= $i ?>"
                                        aria-label="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">
                                    <i class="fa-regular fa-star"></i>
                                </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="ratingInput" name="rating" value="">
                        <div class="rating-label" id="ratingLabel"></div>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label for="message">
                            <i class="fa-solid fa-pen-to-square"></i> Your Feedback <span class="required">*</span>
                        </label>
                        <textarea id="message" name="message" rows="5"
                                  placeholder="Describe your experience in detail…" required
                                  maxlength="2000"></textarea>
                        <div class="char-counter"><span id="charCount">0</span> / 2000</div>
                    </div>

                    <!-- Anonymous toggle -->
                    <div class="form-group form-group-inline">
                        <label class="toggle-label">
                            <input type="checkbox" id="isAnonymous" name="is_anonymous" value="1">
                            <span class="toggle-switch"></span>
                            Submit anonymously
                        </label>
                        <p class="help-text">When enabled, your name won't be visible to administrators.</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fa-solid fa-paper-plane"></i> Submit Feedback
                        </button>
                        <a href="/employee/dashboard.php" class="btn btn-outline btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/main.js"></script>
<script>
/* ── Star rating interaction ─────────────────────────────────── */
const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
const stars        = document.querySelectorAll('.star-btn');
const ratingInput  = document.getElementById('ratingInput');
const ratingLabel  = document.getElementById('ratingLabel');

function setRating(val) {
    ratingInput.value = val;
    ratingLabel.textContent = ratingLabels[val] || '';
    stars.forEach(s => {
        const v = parseInt(s.dataset.value, 10);
        const icon = s.querySelector('i');
        icon.className = v <= val ? 'fa-solid fa-star' : 'fa-regular fa-star';
        s.classList.toggle('selected', v <= val);
    });
}

stars.forEach(star => {
    star.addEventListener('click', () => setRating(parseInt(star.dataset.value, 10)));
    star.addEventListener('mouseenter', () => {
        const v = parseInt(star.dataset.value, 10);
        stars.forEach(s => {
            const sv = parseInt(s.dataset.value, 10);
            s.querySelector('i').className = sv <= v ? 'fa-solid fa-star' : 'fa-regular fa-star';
        });
    });
    star.addEventListener('mouseleave', () => {
        const current = parseInt(ratingInput.value, 10) || 0;
        setRating(current);
    });
});

/* ── Character counter ───────────────────────────────────────── */
const messageArea = document.getElementById('message');
const charCount   = document.getElementById('charCount');
messageArea.addEventListener('input', () => {
    charCount.textContent = messageArea.value.length;
});

/* ── AJAX form submission ────────────────────────────────────── */
document.getElementById('feedbackForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const category  = document.getElementById('category').value;
    const rating    = ratingInput.value;
    const message   = messageArea.value.trim();
    const anonymous = document.getElementById('isAnonymous').checked ? 1 : 0;

    // Client-side validation
    if (!category) { showToast('Please select a category.', 'error'); return; }
    if (!rating)   { showToast('Please select a rating.', 'error');   return; }
    if (!message)  { showToast('Please write your feedback.', 'error'); return; }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting…';

    try {
        const res  = await fetch('/api/submit_feedback.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    new URLSearchParams({ category, rating, message, is_anonymous: anonymous }),
        });
        const data = await res.json();

        if (data.success) {
            showToast('Feedback submitted successfully! Thank you.', 'success');
            document.getElementById('feedbackForm').reset();
            setRating(0);
            charCount.textContent = '0';
        } else {
            showToast(data.message || 'Submission failed. Please try again.', 'error');
        }
    } catch {
        showToast('Network error. Please check your connection.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Feedback';
    }
});

function showToast(msg, type) {
    const toast = document.getElementById('formToast');
    toast.innerHTML = `<div class="toast toast-${type}">
        <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i>
        ${msg}
    </div>`;
    setTimeout(() => { toast.innerHTML = ''; }, 5000);
}
</script>
</body>
</html>
