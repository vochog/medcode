// Wait until PDF.js viewer is ready
window.addEventListener('pagesloaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const keyword = urlParams.get('keyword');
    if (!keyword) return;

    // Basic text layer highlighting
    const observer = new MutationObserver(() => {
        const spans = document.querySelectorAll(".textLayer span");
        spans.forEach(span => {
            if (span.textContent.toLowerCase().includes(keyword.toLowerCase())) {
                span.innerHTML = span.textContent.replace(
                    new RegExp(`(${keyword})`, 'gi'),
                    '<mark>$1</mark>'
                );
            }
        });
    });

    document.querySelectorAll(".textLayer").forEach(layer => {
        observer.observe(layer, { childList: true, subtree: true });
    });
});