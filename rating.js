const stars = document.querySelectorAll('.star');
const reviewText = document.getElementById('reviewText');
const starNumbersInput = document.getElementById('star-numbers');
const submitReview = document.getElementById('submitReview');

let rating = 0;

stars.forEach(star => {
    star.addEventListener('click', () => {
        rating = parseInt(star.dataset.rating);
        highlightStars();
        starNumbersInput.value = `Submitted a ${rating} star rating`; // Update the input field value
    });
});

function highlightStars() {
    stars.forEach(star => {
        const starRating = parseInt(star.dataset.rating);
        star.classList.toggle('active', starRating <= rating);
    });
}

submitReview.addEventListener('click', () => {
    const review = reviewText.value.trim();
    if (review !== '') {
        console.log(`Review Rating: ${rating}`);
        console.log(`Review Text: ${review}`);
        // Add your logic to submit the review
    } else {
        alert('Please write a review before submitting.');
    }
});
